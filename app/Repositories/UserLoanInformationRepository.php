<?php

namespace App\Repositories;

use App\Models\UserLoanInformation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UserLoanInformationRepository extends BaseRepository
{
    protected $modelClass = UserLoanInformation::class;
}