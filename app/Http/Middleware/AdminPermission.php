<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class AdminPermission
{
    public function handle(Request $request, Closure $next)
    {
        switch (User::UserType()){
            case "admin": return $next($request);
            default : abort(403);
        }
    }
}
