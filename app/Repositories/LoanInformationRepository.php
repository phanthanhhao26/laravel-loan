<?php

namespace App\Repositories;

use App\Models\LoanInformation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LoanInformationRepository extends BaseRepository
{
    protected $modelClass = LoanInformation::class;
}