<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\LoanInformationRepository;
use App\Repositories\UserLoanInformationRepository;

class UserLoanController extends Controller
{
    protected $loanInformationRepository;
    protected $userLoanInformationRepository;

    public function __construct(
        UserLoanInformationRepository $userLoanInformationRepository,
        LoanInformationRepository $loanInformationRepository
    )
    {
        $this->loanInformationRepository = $loanInformationRepository;
        $this->userLoanInformationRepository = $userLoanInformationRepository;
    }

    public function getLoansOfUser()
    {
        $apiFormat = [];

        try {
            $userLoans = $this->userLoanInformationRepository
                ->where('user_id', auth()->user()->id)
                ->get();

            $userLoanArray = [];
            foreach ($userLoans as $userLoan) {
                $loanInformation = $this->loanInformationRepository->find($userLoan['loan_id']);

                $userLoanArray[] = [
                    'loan_id' => $userLoan['loan_id'],
                    'loan_name' => $loanInformation['name'],
                    'loan_description' => $loanInformation['description'],
                    'rate_per_week' => $loanInformation['description'],
                    'amount_per_week' => $userLoan['userLoan'],
                    'paid_amount' => $userLoan['paid_amount'],
                    'remain_amount' => $userLoan['remain_amount'],
                ];

                $apiFormat['msg'] = 'Get loans successfully';
                $apiFormat['data'] = $userLoanArray;
            }

        } catch (\Exception $e) {
            $apiFormat['msg'] = 'There is error';
        }

        return response()->json($apiFormat);
    }

    public function apply(Request $request, int $loanId)
    {
        $apiFormat = [];

        try {
            $existedLoan = $this->loanInformationRepository->find($loanId);
            if(!$existedLoan) {
                return response()->json(['msg' => config('messages.loan.exist.no')]);
            }
            $userLoanInformation = $this->userLoanInformationRepository->create([
                'user_id'         => auth()->user()->id,
                'loan_id'         => $loanId,
                'start_date'      => date('Y-m-d', strtotime($request->start_date)),
                'end_date'        => date('Y-m-d', strtotime($request->end_date)),
                'amount_per_week' => $existedLoan['amount'] * $existedLoan['rate_per_week'],
                'remain_amount'   => $existedLoan['amount'],
            ]);

            $apiFormat['msg'] = config('messages.user_loan.apply.successfully');

            $apiFormat['data'] = [
                'user_id'          => $userLoanInformation['user_id'],
                'loan_id'          => $userLoanInformation['loan_id'],
                'loan_name'        => $existedLoan['name'],
                'loan_description' => $existedLoan['description'],
                'start_date'       => date('Y-m-d', strtotime($userLoanInformation['start_date'])),
                'end_date'         => date('Y-m-d', strtotime($userLoanInformation['end_date'])),
                'amount_per_week'  => $userLoanInformation['amount_per_week'],
                'paid_amount'      => $userLoanInformation['paid_amount'],
                'remain_amount'    => $userLoanInformation['remain_amount'],
            ];

        } catch (\Exception $e)
        {
            $apiFormat['msg'] = 'There is error';
        }

        return response()->json($apiFormat);
    }

    public function repay(Request $request, int $loanId)
    {
        $apiFormat = [];
        try {
            $existedLoan = $this->loanInformationRepository->find($loanId);
            if(!$existedLoan) {
                return response()->json(['msg' => config('messages.loan.exist.no')]);
            }

            $userLoanInformation = $this->userLoanInformationRepository->getByFields(
                [
                    'user_id' => auth()->user()->id,
                    'loan_id' => $loanId
                ]
            );
            
            if (!$userLoanInformation) {
                return response()->json(['msg' => config('messages.user_loan.exist.no')]);
            }

            $amountPerWeek = $existedLoan['amount'] * $existedLoan['rate_per_week'];
            $paidAmount = empty($userLoanInformation['paid_amount']) ? $amountPerWeek : ($userLoanInformation['paid_amount'] + $amountPerWeek);

            $userLoanInformation = $this->userLoanInformationRepository->update(
                [
                    'paid_amount' => $paidAmount,
                    'remain_amount' => $existedLoan['amount'] - $paidAmount
                ],
                $userLoanInformation['id']
            );

            $apiFormat['msg'] = config('messages.user_loan.repay.successfully');

            $apiFormat['data'] = [
                'user_id'          => $userLoanInformation['user_id'],
                'loan_id'          => $userLoanInformation['loan_id'],
                'loan_name'        => $existedLoan['name'],
                'loan_description' => $existedLoan['description'],
                'start_date'       => date('Y-m-d', strtotime($userLoanInformation['start_date'])),
                'end_date'         => date('Y-m-d', strtotime($userLoanInformation['end_date'])),
                'amount_per_week'  => $userLoanInformation['amount_per_week'],
                'paid_amount'      => $userLoanInformation['paid_amount'],
                'remain_amount'    => $userLoanInformation['remain_amount'],
            ];
        } catch (\Exception $e) {
            $apiFormat['msg'] = 'There is error';
        }

        return response()->json($apiFormat);
    }
}