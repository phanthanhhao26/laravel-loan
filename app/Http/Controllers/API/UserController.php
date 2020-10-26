<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Hash;
use Illuminate\Support\Facades\Mail;
use App\Repositories\UserRepository;

class UserController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(Request $request)
    {
        $apiFormat = [];

        try {
            $email = $request->email;

            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return response()->json(['status' => config('constants.STATUS.INACTIVE'), 'msg' => 'Invalid format']);
            }

            $existedUser = $this->userRepository->getByFields(['email' => $email]);
            if ($existedUser) {
                return response()->json(['status' => config('constants.STATUS.INACTIVE'), 'msg' => 'Email already exists']);
            }

            $user = $this->userRepository->create([
                'email'      => $email,
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
                'password'   => Hash::make($request->password),
                'address'    => $request->address,
            ]);

            // $apiFormat['status'] = config('constants.STATUS.ACTIVE');
            $apiFormat['success'] = true;
            $apiFormat['msg'] = config('messages.user.register.successfully');

            $apiFormat['data'] = [
                'email'      => $user['email'],
                'first_name' => $user['first_name'],
                'last_name'  => $user['last_name'],
                'address'    => $user['address'],
                'token_key' => auth()->tokenById($user['id'])
            ];
        } catch (\Exception $e) {
            $apiFormat['msg'] = 'There is error';
        }

        return response()->json($apiFormat);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $apiFormat = [];

        $credentials = $request->only(['email', 'password']);
        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        } else {
            $user = auth()->user();
        }

        // $apiFormat['status'] = config('constants.STATUS.ACTIVE');
        $apiFormat['success'] = true;
        $apiFormat['msg'] = config('messages.user.login.successfully');

        $apiFormat['data'] = [
            'email'      => $user['email'],
            'first_name' => $user['first_name'],
            'last_name'  => $user['last_name'],
            'address'    => $user['address'],
            'token_key'  => $token
        ];

        return response()->json($apiFormat);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['msg' => config('messages.user.logout.successfully')]);
    }
}
