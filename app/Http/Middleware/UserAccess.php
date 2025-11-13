<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Pastikan user login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Samakan format huruf
        $userRole = strtolower(auth()->user()->role);
        $requiredRole = strtolower($role);

        // Cek role
        if ($userRole === $requiredRole) {
            return $next($request);
        }

        return redirect('/home');
    }

}
