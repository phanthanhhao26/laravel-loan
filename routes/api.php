<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('auth')->group(function () {
    Route::post('register', 'App\Http\Controllers\API\UserController@register');
    Route::post('login', 'App\Http\Controllers\API\UserController@login');
});

Route::middleware('api')->group(function () {
    Route::get('loan/lists', 'App\Http\Controllers\API\LoanController@getAvailableLoans');
    Route::get('loan/user-loans', 'App\Http\Controllers\API\UserLoanController@getLoansOfUser');
    Route::get('loan/user-loan/{loanId}', 'App\Http\Controllers\API\UserLoanController@getALoanOfUserById');
    Route::post('loan', 'App\Http\Controllers\API\LoanController@create');
    Route::get('loan/{loanId}', 'App\Http\Controllers\API\LoanController@getLoanById');
    Route::post('loan/apply/{loanId}', 'App\Http\Controllers\API\UserLoanController@apply');
    Route::put('loan/repay/{loanId}', 'App\Http\Controllers\API\UserLoanController@repay');
});


