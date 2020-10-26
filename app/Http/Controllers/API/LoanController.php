<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\LoanInformationRepository;

class LoanController extends Controller
{
    protected $loanInformationRepository;

    public function __construct(LoanInformationRepository $loanInformationRepository)
    {
        $this->loanInformationRepository = $loanInformationRepository;
    }

    public function getAvailableLoans()
    {
        $apiFormat = [];

        try {
            $loans = $this->loanInformationRepository->get();

            $loanArray = [];
            foreach ($loans as $loan) {
                $loanArray[] = [
                    'name'          => $loan->name,
                    'description'   => $loan->description,
                    'amount'        => $loan->amount,
                    'period'        => $loan->period,
                    'rate_per_week' => $loan->rate_per_week,
                ];
            }

            $apiFormat['success'] = true;
            $apiFormat['msg'] = 'Get loans successfully';
            $apiFormat['data'] = $loanArray;

        } catch (\Exception $e) {
            $apiFormat['msg'] = 'There is error';
        }

        return response()->json($apiFormat);
    }

    public function create(Request $request)
    {
        $apiFormat = [];

        $existedLoan = $this->loanInformationRepository->getByFields(['name' => $request->name]);
        if ($existedLoan) {
            return response()->json(['msg' => config('messages.loan.exist.yes')]);
        }

        try {
            $loan = $this->loanInformationRepository->create([
                'name'          => $request->name,
                'description'   => $request->description,
                'amount'        => $request->amount,
                'period'        => $request->period,
                'rate_per_week' => $request->rate_per_week,
            ]);
            
            $apiFormat['success'] = true;
            $apiFormat['msg'] = config('messages.loan.create.successfully');
            $apiFormat['data'] = [
                'name'          => $loan['name'],
                'description'   => $loan['description'],
                'amount'        => $loan['amount'],
                'period'        => $loan['period'],
                'rate_per_week' => $loan['rate_per_week'],
            ];

        } catch (\Exception $e) {
            $apiFormat['msg'] = 'There is error';
        }

        return response()->json($apiFormat);
    }

    public function getLoanById(Request $request, int $loanId)
    {
        $apiFormat = [];

        try {
            $loan = $this->loanInformationRepository->find($loanId);

            $apiFormat['success'] = true;
            $apiFormat['msg'] = config('messages.loan.create.successfully');
            $apiFormat['data'] = [
                'name'          => $loan['name'],
                'description'   => $loan['description'],
                'amount'        => $loan['amount'],
                'period'        => $loan['period'],
                'rate_per_week' => $loan['rate_per_week'],
            ];
        } catch (\Exception $e) {
            $apiFormat['msg'] = 'There is error';
        }

        return response()->json($apiFormat);
    }
}