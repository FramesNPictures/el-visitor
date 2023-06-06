<?php

namespace FNP\ElVisitor\Services;

use FNP\ElVisitor\Models\Visitor;
use FNP\ElVisitor\Sources\IpInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Nette\Utils\DateTime;
use Ramsey\Uuid\Uuid;
use Src\Auth\Services\IpInfoService;
use Symfony\Component\HttpFoundation\Cookie;

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

        // IpInfo
        $ipSourceClass = config('visitor.ip.source', IpInfo::class);

        if ($ipSourceClass) {
            $ipSource = new $ipSourceClass();
            $ipSource->apply($this->visitor, $ips[0]);
        }
    }

    public function visitor(): Visitor
    {
        Log::shareContext(
            [
                'v'  => $this->visitor->token,
                'ip' => $this->visitor->ip,
                'c'  => $this->visitor->country ?? 'N/A',
                'r'  => $this->visitor->region ?? 'N/A',
            ],
        );


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