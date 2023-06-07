<?php

namespace FNP\ElVisitor\Services;

use FNP\ElVisitor\Models\Visitor;
use Illuminate\Http\Request;
use Nette\Utils\DateTime;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Cookie;
use WhichBrowser\Parser;

class VisitorService
{
    protected Visitor $visitor;

    public function __construct(Request $request)
    {
        $token              = $request->cookie(config('visitor.cookie'), '');
        $ips                = array_unique(array_merge($request->ips(), $request->getClientIps()));
        $this->visitor      = new Visitor();
        $this->visitor->new = false;

        // Empty token
        if (empty($token)) {
            $this->visitor->new = true;
            $token              = Uuid::uuid4();
        }

        // Fix malformed token
        if (strlen($token) !== 36) {
            $this->visitor->new = true;
            $token              = Uuid::uuid4();
        }

        // Apply visitor information
        $this->visitor->ip        = $ips[0];
        $this->visitor->token     = $token;
        $this->visitor->userAgent = $request->header('User-Agent');
        $this->visitor->uri       = $request->getUri();
        $this->visitor->referer   = $request->headers->get('referer');

        // User agent parsing
        $parser                        = new Parser(getallheaders());
        $this->visitor->browserName    = $parser->browser->name;
        $this->visitor->browserVersion = $parser->browser->version->toString();
        $this->visitor->browserEngine  = $parser->engine->name;
        $this->visitor->osName         = $parser->os->name;
        $this->visitor->osVersion      = $parser->os->version->toString();
        $this->visitor->osAlias        = $parser->os->alias;
        $this->visitor->osFamily       = $parser->os->family->name;
        $this->visitor->deviceModel    = $parser->device->model;
        $this->visitor->deviceMake     = $parser->device->manufacturer;


        // IpInfo
        $ipSourceClass = config('visitor.ip.source');

        if ($ipSourceClass) {
            $ipSource = new $ipSourceClass();
            $ipSource->apply($this->visitor, $ips[0]);
        }
    }

    public function visitor(): Visitor
    {
        return $this->visitor;
    }

    public function storeToken($response): void
    {
        $response->headers->setCookie(
            new Cookie(
                config('visitor.cookie'),
                $this->visitor()->token,
                new DateTime('now +400 days'),
                '/',
            ),
        );
    }

    /**
     * @return array
     */
    public function getIps(): array
    {
        return $ips;
    }
}