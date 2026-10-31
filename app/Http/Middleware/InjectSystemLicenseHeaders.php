<?php

namespace App\Http\Middleware;

use App\Services\DSS\SystemLicenseService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InjectSystemLicenseHeaders
{
    /**
     * Handle an incoming request and inject permanent provenance headers.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Enforce inviolable software provenance & license integrity
        SystemLicenseService::enforceIntegrity();

        /** @var Response $response */
        $response = $next($request);

        $response->headers->set('X-System-Architect', SystemLicenseService::AUTHOR_NAME);
        $response->headers->set('X-Engine-Provenance', 'RIE-EXTEND by '.SystemLicenseService::AUTHOR_NAME);
        $response->headers->set('X-Authorship-Integrity', 'VERIFIED-PROPRIETARY');
        $response->headers->set('X-Software-License', 'Institutional-Turnover-APJ');

        return $response;
    }
}
