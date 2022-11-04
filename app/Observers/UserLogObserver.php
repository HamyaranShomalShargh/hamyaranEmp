<?php

namespace App\Observers;

use App\Models\UserLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class UserLogObserver
{
    public bool $afterCommit = true;

    public function deleted(UserLog $userLog): void
    {
        UserLog::query()->create([
            "user_id" => Auth::id(),
            "username" => Auth::user()->username,
            "name" => Auth::user()->name,
            "operation" => "حذف",
            "description" => "حذف گزارشات کاربران",
            "model" => "UserLog",
            "data" => json_encode($userLog->toArray()),
            "ip" => Request::ip()
        ]);
    }
}
