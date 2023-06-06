<?php

namespace FNP\ElVisitor\Middleware;

use FNP\ElVisitor\Services\VisitorService;
use Illuminate\Http\Request;

class VisitorMiddleware
{
    /**
     * @var VisitorService
     */
    protected VisitorService $visitorService;

    public function __construct(VisitorService $visitorService)
    {
        $this->visitorService = $visitorService;
    }

    public function handle(Request $request, \Closure $next)
    {
        $response = $next($request);

        $this->visitorService->storeToken($response);

        return $response;
    }
}