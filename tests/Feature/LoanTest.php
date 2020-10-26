<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\LoanInformation;
use App\Models\UserLoanInformation;
use Tests\TestCase;

class LoanTest extends TestCase
{
    protected $urlBasePath = 'loan';
    protected $modelClass = LoanInformation::class;

    /**
     * Test that we can create a loan
     *
     * @return void
     */
    public function testWeCanCreateALoanNew()
    {
        $loanInformation = LoanInformation::factory()->create([
            'description' => 'loan_description',
            'amount' => 1200,
            'period' => 6,
            'rate_per_week' => 0.01
        ]);
        
        $this->response = $this->json('GET', $this->getUrlPath($loanInformation->id));

        $this->response
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'msg',
                'success',
                'data' => [
                    'name',
                    'description',
                    'amount',
                    'period',
                    'rate_per_week',
                ]
            ]);
    }

    public function testAUserCanApplyALoan()
    {
        $user = User::factory()->create();
        $loanInformation = LoanInformation::factory()->create([
            'description' => 'loan_description',
            'amount' => 1200,
            'period' => 6,
            'rate_per_week' => 0.01
        ]);

        $userLoanInformation = UserLoanInformation::factory()->create([
            'user_id' => $user->id,
            'loan_id' => $loanInformation->id,
            'start_date' => date('Y-m-d', strtotime('2020-10-26')),
            'end_date' => date('Y-m-d', strtotime('2021-10-26')),
        ]);
        

        $this->response = $this->json('GET', $this->getUrlPath('user-loan/' . $loanInformation->id));

        $this->response
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'msg',
                'success',
                'data' => [
                    'user_id',
                    'loan_id',
                    'loan_name',
                    'loan_description',
                    'start_date',
                    'end_date',
                    'amount_per_week',
                    'paid_amount',
                    'remain_amount',
                ]
            ])
            ->assertJsonFragment([
                'user_id' => $user->id,
                'loan_id' => $loanInformation->id,
                'loan_name' => $loanInformation->name,
                'loan_description' => $loanInformation->description,
                'start_date' => date('Y-m-d', strtotime($userLoanInformation['start_date'])),
                'end_date' => date('Y-m-d', strtotime($userLoanInformation['end_date'])),
                'amount_per_week' => $loanInformation['amount'] * $loanInformation['rate_per_week'],
                'paid_amount' => null,
                'remain_amount' => $loanInformation['amount']
            ]);


    }

    public function testAUserCanRepayALoan()
    {
        $user = User::factory()->create();
        $loanInformation = LoanInformation::factory()->create([
            'description' => 'loan_description',
            'amount' => 1200,
            'period' => 6,
            'rate_per_week' => 0.01
        ]);

        $userLoanInformation = UserLoanInformation::factory()->create([
            'user_id' => $user->id,
            'loan_id' => $loanInformation->id,
            'start_date' => date('Y-m-d', strtotime('2020-10-26')),
            'end_date' => date('Y-m-d', strtotime('2021-10-26')),
        ]);

        $amountPerWeek = $loanInformation['amount'] * $loanInformation['rate_per_week'];
        $paidAmount = empty($userLoanInformation['paid_amount']) ? $amountPerWeek : ($userLoanInformation['paid_amount'] + $amountPerWeek);

        $userLoanInformation = $this->userLoanInformationRepository->update(
            [
                'paid_amount' => $paidAmount,
                'remain_amount' => $loanInformation['amount'] - $paidAmount
            ],
            $userLoanInformation['id']
        );

        $this->response = $this->json('GET', $this->getUrlPath('user-loan/' . $loanInformation->id));

        $this->response
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'msg',
                'success',
                'data' => [
                    'user_id',
                    'loan_id',
                    'loan_name',
                    'loan_description',
                    'start_date',
                    'end_date',
                    'amount_per_week',
                    'paid_amount',
                    'remain_amount',
                ]
            ])
            ->assertJsonFragment([
                'user_id' => $user->id,
                'loan_id' => $loanInformation->id,
                'loan_name' => $loanInformation->name,
                'loan_description' => $loanInformation->description,
                'start_date' => date('Y-m-d', strtotime($userLoanInformation['start_date'])),
                'end_date' => date('Y-m-d', strtotime($userLoanInformation['end_date'])),
                'amount_per_week' => $loanInformation['amount'] * $loanInformation['rate_per_week'],
                'paid_amount' => $userLoanInformation['paid_amount'],
                'remain_amount' => $userLoanInformation['remain_amount']
            ]);
    }
}