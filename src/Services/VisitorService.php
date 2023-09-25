<?php

namespace FNP\ElVisitor\Services;

use FNP\ElVisitor\Interfaces\VisitorPlugin;
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
        $this->visitor      = new Visitor();

        // IpInfo
        foreach(config('visitor.plugins') as $pluginClass) {
            $pluginClass = app($pluginClass);
            if ($pluginClass instanceof VisitorPlugin) {
                $pluginClass->apply($this->visitor);
            }
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
}