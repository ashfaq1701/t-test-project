<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Repositories\ConfirmAccountRepository;

class ConfirmAccountController {
    public $confirmAccountRepository;

    public function __construct(ConfirmAccountRepository $confirmAccountRepository)
    {
        $this->confirmAccountRepository = $confirmAccountRepository;
    }

    public function resendConfirmationEmail (Request $request) {
        if (!$request->has('email')) {
            return response()->json([
                'message' => 'Email address must be provided to send confirmation email'
            ], 401);
        }
        return $this->confirmAccountRepository->resendConfirmationEmail($request);
    }

    public function confirmAccount (Request $request) {
        if ((!$request->has('email')) || (!$request->has('confirmation_token'))) {
            return response()->json([
                'message' => 'Account confirmation endpoint must be supplied with email address and confirmation token'
            ], 401);
        }
        return $this->confirmAccountRepository->confirmAccount($request);
    }
}