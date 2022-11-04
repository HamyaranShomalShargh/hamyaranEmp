<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function idle(){
        switch (User::UserType()){
            case "admin": return redirect()->route("admin_idle");
            case "staff": return redirect()->route("staff_idle");
            default : abort(403);
        }
    }
    public function admin(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view("admin.idle");
    }
    public function staff(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view("staff.idle");
    }
}
