<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UserRepository extends BaseRepository
{
    protected $modelClass = User::class;

    public function getUserByEmail(string $email)
    {
        return $this->model->newQuery()->where('email', $email)->first();
    }
}