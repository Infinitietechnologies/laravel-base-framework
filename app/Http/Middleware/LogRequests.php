<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogRequests
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $logData = [
            'method' => $request->getMethod(),
            'url' => $request->getUri(),
            'payload' => $request->all(),
            'ip_address' => $request->ip(),
            'headers' => $request->headers->all()
        ];
        Log::info('Request Logged:', $logData);
        return $next($request);
    }
}
