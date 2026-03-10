<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuideMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || (!auth()->user()->hasRole('guide') && !auth()->user()->tourGuide)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized. Guide access required.'], 403);
            }
            
            return redirect()->route('home')->with('error', 'Unauthorized. This area is for Tour Guides only.');
        }

        return $next($request);
    }
}
