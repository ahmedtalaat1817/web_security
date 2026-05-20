<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * @param  string  ...$roles  user_type string values e.g. admin, restaurant
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $allowed = collect($roles)->contains(fn (string $role) => $user->user_type?->value === $role);

        if (! $allowed) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden'], Response::HTTP_FORBIDDEN);
            }

            abort(Response::HTTP_FORBIDDEN, 'This action is unauthorized.');
        }

        return $next($request);
    }
}
