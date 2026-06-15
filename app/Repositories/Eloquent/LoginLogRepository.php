<?php

namespace App\Repositories\Eloquent;

use App\Models\LoginLog;
use App\Repositories\Contracts\LoginLogRepositoryInterface;

class LoginLogRepository implements LoginLogRepositoryInterface
{
    public function create(array $data): LoginLog
    {
        return LoginLog::create($data);
    }
}
