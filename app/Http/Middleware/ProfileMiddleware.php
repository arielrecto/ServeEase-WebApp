<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ProfileMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $user = Auth::user();
        if(is_null($user->email_verified_at)){
            return to_route('verification.verify');
        }


        $profile = $user->profile;



        if(!$profile){
            return to_route('profile.edit')->with(['message_warning' => 'Fill Up Profile First!']);
        }


        return $next($request);
    }
}
