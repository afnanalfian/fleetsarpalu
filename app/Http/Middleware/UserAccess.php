<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserAccess
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Pastikan user login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Role user
        $userRole = strtolower(Auth::user()->role);

        // Normalisasi role di parameter middleware
        $allowed = array_map('strtolower', $roles);

        // dd([
        //     'userRole' => $userRole,
        //     'allowed' => $allowed,
        // ]);

        // Jika user tidak punya salah satu role tersebut
        if (!in_array($userRole, $allowed)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
