<?php

namespace App\Repositories\Contracts;

use App\Models\LoginLog;

interface LoginLogRepositoryInterface
{
    public function create(array $data): LoginLog;
}
