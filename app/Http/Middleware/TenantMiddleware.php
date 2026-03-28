<?php

namespace App\Http\Middleware;

use ECommerce\User\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = null;

        // Try X-Tenant-ID header first
        if ($tenantId = $request->header('X-Tenant-ID')) {
            $tenant = Tenant::find((int) $tenantId);
        }

        // Try subdomain identification
        if (!$tenant) {
            $host = $request->getHost();
            $subdomain = explode('.', $host)[0];
            if ($subdomain && $subdomain !== 'www') {
                $tenant = Tenant::where('subdomain', $subdomain)->active()->first();
            }
        }

        // Fall back to default tenant if none found
        if (!$tenant) {
            $tenant = Tenant::active()->first();
        }

        if ($tenant) {
            app()->instance('current_tenant', $tenant);
            $request->merge(['current_tenant' => $tenant]);
        }

        return $next($request);
    }
}
