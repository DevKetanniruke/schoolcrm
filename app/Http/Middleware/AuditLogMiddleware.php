<?php

namespace App\Http\Middleware;

use App\Core\Services\AuditLogger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditLogMiddleware
{
    public function __construct(protected AuditLogger $auditLogger)
    {
    }

    /**
     * Handle an incoming request and log state-modifying requests (POST, PUT, PATCH, DELETE).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $route = $request->route() ? $request->route()->getName() ?: $request->path() : $request->path();
            
            // Mask sensitive password fields in audit log
            $payload = array_diff_key($request->except(['password', 'password_confirmation', '_token']), array_flip(['password']));

            $this->auditLogger->log(
                action: 'http.' . strtolower($request->method()) . '.' . str_replace('/', '.', $route),
                payload: [
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'status_code' => $response->getStatusCode(),
                    'input' => $payload,
                ]
            );
        }

        return $response;
    }
}
