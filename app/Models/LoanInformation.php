<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanInformation extends Model
{
    protected $table = 'loan_information';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'amount',
        'period',
        'rate_per_week',
        'created_at',
        'updated_at',
    ];
}