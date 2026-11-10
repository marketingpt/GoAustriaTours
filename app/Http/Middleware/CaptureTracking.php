<?php

namespace App\Http\Middleware;

use App\Support\Tracking;
use Closure;
use Illuminate\Http\Request;

/**
 * Captures ad/UTM parameters on every public GET (first touch kept 30 days, last touch updated each time
 * parameters are present). Stored in a cookie so it survives multi-page visits.
 */
class CaptureTracking
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($request->isMethod('GET') && ! $request->is('admin*') && ! $request->is('up')) {
            Tracking::capture($request, $response);
        }

        return $response;
    }
}
