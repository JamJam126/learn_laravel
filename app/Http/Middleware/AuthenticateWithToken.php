<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class AuthenticateWithToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        // Log::info("Received token: " . $token);

        if (!$token || !User::where('api_token', $token)->exists()) {
            // Log::warning("Unauthorized access with token: " . $token);
            return response()->json(['message' => 'Unauthorized'], 401);
            // return redirect('/login');
            // return Redirect::to('/login');
        }
        
        return $next($request);
    }
}
