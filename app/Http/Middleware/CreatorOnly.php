<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CreatorOnly
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (! $user || ! $user->isApprovedCreator()) {
            return response()->json(['message' => 'Creator access required.'], 403);
        }

        return $next($request);
    }
}
