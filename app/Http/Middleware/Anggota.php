<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Role;

class Anggota
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user_role = $GLOBALS['USER_DATA']->role_id;
        $role = Role::find($user_role);

        if($role->name == "Anggota") return $next($request);
        else return response("Error", Response::HTTP_FORBIDDEN);
    }
}
