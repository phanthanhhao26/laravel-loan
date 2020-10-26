<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserLoanInformation extends Model
{
    use HasFactory;

    protected $table = 'user_loan_information';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'loan_id',
        'start_date',
        'end_date',
        'amount_per_week',
        'paid_amount',
        'remain_amount',
        'created_at',
        'updated_at',
    ];
}