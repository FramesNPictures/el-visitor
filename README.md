# El Visitor

A Laravel package that resolves the current request into a single, richly populated `Visitor`
object — IP address and version, geolocation, network/ASN owner, device, browser, platform,
languages, bot detection and abuse reputation.

Everything is produced by a **plugin pipeline**. Each plugin receives the same `Visitor` instance
and fills in the fields it knows about, so you can add, remove or reorder sources without touching
application code. Geolocation and ASN lookups are served from local MaxMind-format (MMDB)
databases shipped in [`data/`](data/), so the default pipeline performs **no outbound HTTP requests**.

## Requirements

- PHP 8.0+
- Laravel (the package is registered through Laravel's package auto-discovery)

## Installation

```bash
composer require framesnpictures/el-visitor
```

The service provider `FNP\ElVisitor\ElVisitorModule` is auto-discovered — no manual registration
is needed.

To customise the cookie name or the plugin pipeline, publish the config:

```bash
php artisan vendor:publish --provider="FNP\ElVisitor\ElVisitorModule" --tag=config
```

This creates `config/visitor.php`.

Finally, download the local IP databases. They are **not shipped with the package**, so this step
is required for geolocation and ASN lookups to return anything:

```bash
php artisan app:visitor:update
```

See [Local IP databases](#local-ip-databases) for how to wire this into a deployment.

## Usage

The package registers two singletons, so you can resolve either the service or the resolved
visitor directly:

```php
use FNP\ElVisitor\Models\Visitor;
use FNP\ElVisitor\Services\VisitorService;

// Inject the resolved visitor anywhere in your app
public function index(Visitor $visitor)
{
    return $visitor->country;   // 'US'
}

// Or go through the service
$visitor = app(VisitorService::class)->visitor();
```

The pipeline runs once, when `VisitorService` is first resolved, and the result is reused for the
rest of the request.

### Persisting the visitor cookie

Returning visitors are recognised by a cookie (`APP-V` by default). To issue it, hand a response
to `storeToken()` — typically from middleware:

```php
app(VisitorService::class)->storeToken($response);
```

The cookie stores the visitor's UUID and is set to expire in 400 days. Until it is issued, every
request is treated as a new visitor (`$visitor->new === true`).

### Available data

| Property | Type | Description |
|---|---|---|
| `visitorId` | `?string` | UUID from the visitor cookie, or a freshly generated one |
| `requestId` | `?string` | Per-request identifier (Cloudflare Ray ID when available) |
| `new` | `?bool` | `true` when no visitor cookie was present |
| `ip` | `?string` | Client IP address |
| `ipVersion` | `?int` | `4` or `6` |
| `userAgent` | `?string` | Raw user agent string |
| `browser` / `device` / `platform` | `?string` | Detected client details |
| `languages` | `array` | Accepted languages, e.g. `['en-us']` |
| `uri` / `referer` | `?string` | Requested path and referring URL |
| `city` / `region` / `country` / `postcode` | `?string` | Geolocation (`country` is an ISO code) |
| `location` / `timezone` | `?string` | Coordinates and timezone |
| `providerId` / `providerName` | `?int` / `?string` | ASN number and owning organisation |
| `organisation` | `?string` | Convenience string, e.g. `AS13335 Cloudflare, Inc.` |
| `providerType` | `?string` | Network type, when known |
| `isRobot` / `isMobile` | `?bool` | Bot and mobile detection |
| `isTor` / `isDataCenter` | `?bool` | Populated by AbuseIPDB |
| `abuseScore` / `abuseReports` | `int` | AbuseIPDB confidence score and report count |
| `extra` | `array` | Additional source-specific values |

## Configuration

`config/visitor.php` holds the cookie name and the ordered plugin pipeline:

```php
return [
    'cookie'  => 'APP-V',

    'plugins' => [
        FNP\ElVisitor\Plugins\ProvideBasicVisitorData::class,
        FNP\ElVisitor\Plugins\RecogniseIPVersion::class,
        FNP\ElVisitor\Plugins\MobileBrowserDetection::class,
        FNP\ElVisitor\Plugins\BotCrowlerDetection::class,
        FNP\ElVisitor\Plugins\ProvideCloudFlareIPData::class,
        FNP\ElVisitor\Plugins\JenssegersAgentDetection::class,
        FNP\ElVisitor\Plugins\ClientHintsDetection::class,
        FNP\ElVisitor\Plugins\Services\DataByLocalDatabase::class,
    ],
];
```

Order matters: plugins run top to bottom, and later plugins generally overwrite values set by
earlier ones.

### Enabled by default

| Plugin | Fills in |
|---|---|
| `ProvideBasicVisitorData` | `visitorId`, `requestId`, `new`, `ip`, `uri`, `userAgent`, `referer` |
| `RecogniseIPVersion` | `ipVersion` |
| `MobileBrowserDetection` | `isMobile` |
| `BotCrowlerDetection` | `isRobot` |
| `ProvideCloudFlareIPData` | `ip`, `country`, `requestId` from `CF-*` headers |
| `JenssegersAgentDetection` | `browser`, `device`, `platform`, `languages`, `isRobot` |
| `ClientHintsDetection` | `platform`, `isMobile` from `Sec-CH-UA-*` headers |
| `Services\DataByLocalDatabase` | `providerId`, `providerName`, `organisation`, `city`, `region`, `country` |

### Optional API-backed plugins

These are **not enabled by default** because they require an API token and make outbound HTTP
requests. Responses are cached for 7 days, and private/non-routable IPs are skipped.

| Plugin | Service | Fills in |
|---|---|---|
| `Services\DataByAbuseIPDB` | [AbuseIPDB](https://www.abuseipdb.com/) | `abuseScore`, `abuseReports`, `isTor`, `isDataCenter`, `country`, `organisation`, `providerName`, `providerType`, `extra` |
| `Services\LocationByIpinfoIO` | [ipinfo.io](https://ipinfo.io/) | `ip`, `city`, `region`, `country`, `postcode`, `location`, `timezone`, `organisation` |
| `Services\LocationByDbIpCom` | [db-ip.com](https://db-ip.com/) | `city`, `region`, `country` |

Plugins that take constructor arguments are configured using the class name as the **key** and the
arguments as the **value**:

```php
'plugins' => [
    // ...
    FNP\ElVisitor\Plugins\Services\DataByAbuseIPDB::class => [
        'token' => env('ABUSEIPDB_TOKEN'),
    ],
    FNP\ElVisitor\Plugins\Services\LocationByIpinfoIO::class => [
        'token' => env('IPINFO_IO_TOKEN'),
    ],
],
```

If a token is `null`, the plugin returns immediately and does nothing. Network and API failures are
caught and logged rather than thrown, so a failing lookup never breaks the request.

### Writing your own plugin

Implement `VisitorPlugin` and add the class to the pipeline. Plugins are resolved from the
container, so dependencies are injected automatically.

```php
namespace App\Visitor;

use FNP\ElVisitor\Interfaces\VisitorPlugin;
use FNP\ElVisitor\Models\Visitor;

class MyPlugin implements VisitorPlugin
{
    public function apply(Visitor $visitor): void
    {
        $visitor->extra['internal'] = true;
    }
}
```

## Local IP databases

The MMDB files in `data/` back the `DataByLocalDatabase` plugin. IPv4 and IPv6 have separate
files, and the ASN, GeoLite2 city and DB-IP city databases are consulted in that order.

**The databases are not distributed with this package.** Together they are close to 200 MB and are
rebuilt upstream daily to monthly, so committing them would bloat the repository and ship stale
data. Fetch them as a deployment step instead:

```bash
php artisan app:visitor:update
```

This runs [`usr/update-mmdb`](usr/update-mmdb), which downloads the current builds into the
package's `data/` directory, verifies each file before replacing the previous one, and exits
non-zero if any download fails.

### Deploying

The databases live inside the installed package (`vendor/framesnpictures/el-visitor/data/`). If
your deployment builds `vendor/` from scratch — the usual case for `composer install` on a fresh
release directory — **the databases must be downloaded on every deploy**. Add the command after
your install step:

```bash
composer install --no-dev --optimize-autoloader
php artisan app:visitor:update
```

Two things to keep in mind:

- The command needs network access and a writable `vendor/` directory. It exits non-zero on
  failure, so it will fail a deployment loudly rather than silently shipping empty databases.
- Refreshing occasionally is worthwhile even on long-lived installs: DB-IP rebuilds monthly and
  GeoLite2 twice weekly, so the data drifts.

If the files are absent, the plugin degrades gracefully — every lookup is skipped and the
geolocation and ASN fields simply stay `null`. Nothing throws, which also means a failed download
is easy to miss; treat a non-zero exit from `app:visitor:update` as a deployment failure.

## IP data credits

The databases downloaded into `data/` are **not produced by this package**. They are compiled and
published by [**sapics/ip-location-db**](https://github.com/sapics/ip-location-db), which converts
several freely available IP datasets into MMDB format. Full credit for this data goes to that
project and to the upstream data providers below.

| Files | Upstream dataset | Original source | License |
|---|---|---|---|
| `geolite2-city-ipv4.mmdb`, `geolite2-city-ipv6.mmdb` | [`geolite2-city`](https://github.com/sapics/ip-location-db/tree/main/geolite2-city) | GeoLite2 by [MaxMind](https://www.maxmind.com/) | [CC BY-SA 4.0](https://creativecommons.org/licenses/by-sa/4.0/) |
| `dbip-city-ipv4.mmdb`, `dbip-city-ipv6.mmdb` | [`dbip-city`](https://github.com/sapics/ip-location-db/tree/main/dbip-city) | DB-IP Lite by [DB-IP](https://db-ip.com/) | [CC BY 4.0](https://creativecommons.org/licenses/by/4.0/) |
| `asn-ipv4.mmdb`, `asn-ipv6.mmdb` | [`origin-asn`](https://github.com/sapics/ip-location-db/tree/main/origin-asn) | RIR / NRO delegation data | [PDDL 1.0](https://opendatacommons.org/licenses/pddl/1-0/) |

> **Why `origin-asn`?** Upstream publishes four ASN builds, and `origin-asn` is the only one that
> populates `autonomous_system_organization` — `iptoasn-asn`, `dbip-asn` and `geolite2-asn` all
> return the AS number with an empty organisation name, which would leave `providerName` and
> `organisation` blank. PDDL is a public-domain dedication, so these files need no attribution.

### Required attribution

Both city databases carry attribution requirements. If you redistribute this package or expose the
data it produces, include the following notices:

> This product includes GeoLite2 data created by MaxMind, available from
> [https://www.maxmind.com](https://www.maxmind.com)

> IP Geolocation by [DB-IP](https://db-ip.com)

GeoLite2 is additionally governed by MaxMind's
[GeoLite2 End User Licence Agreement](https://www.maxmind.com/en/geolite2/eula), and the CC BY-SA
4.0 share-alike terms apply to derivative datasets. Review both before redistributing.

## Development

```bash
./usr/update-mmdb   # download the IP databases (needed once, before testing)
composer test       # run the Pest test suite
composer lint       # check formatting with Laravel Pint
```

The two `DataByLocalDatabase` tests assert against real database lookups. They are skipped rather
than failed when the databases have not been downloaded, so the suite is green on a fresh
checkout — run `./usr/update-mmdb` to exercise them.
