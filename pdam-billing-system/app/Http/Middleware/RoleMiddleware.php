<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        if (!$user->role) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User has no role assigned'
                ], 403);
            }
            abort(403, 'User has no role assigned.');
        }

        // Check if user has any of the required roles
        $userRole = $user->role->name;
        if (!in_array($userRole, $roles)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient permissions. Required roles: ' . implode(', ', $roles)
                ], 403);
            }
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
