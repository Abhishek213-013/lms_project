<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IncreaseUploadLimits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Force PHP configuration for large file uploads
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH'])) {
            @ini_set('upload_max_filesize', '512M');
            @ini_set('post_max_size', '512M');
            @ini_set('max_execution_time', '600');
            @ini_set('max_input_time', '600');
            @ini_set('memory_limit', '1024M');
        }

        return $next($request);
    }
}