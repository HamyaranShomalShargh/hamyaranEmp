<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class StaffPermission
{
    public function handle(Request $request, Closure $next)
    {
        switch (User::UserType()){
            case "staff": return $next($request);
            default : abort(403);
        }
    }
}
