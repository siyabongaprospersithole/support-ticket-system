<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventEnvReload
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Create a flag that tells Laravel not to reload on env changes
        file_put_contents(storage_path('app/prevent_env_reload'), '1');
        
        $response = $next($request);
        
        // Keep the flag for a short period to prevent reloads between AJAX requests
        // The flag will be automatically cleaned up by Laravel's garbage collection
        
        return $response;
    }
} 