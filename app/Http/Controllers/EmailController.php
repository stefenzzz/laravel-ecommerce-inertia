<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmailController extends Controller
{
    /**
     * Route returns Inertia Auth/VerifyEmail page component
     *
     * @param Request $request
     * @return Inertia\Inertia
     */
    public function verifyEmail(Request $request)
    {

        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended('/profile');
        }

        $request->user()->sendEmailVerificationNotification();

        return inertia('Auth/VerifyEmail', ['status' => true] );
        
    }

    /**
     * Handles the request to fullfill user`s verification from the link sent to the user's email
     *
     * @param EmailVerificationRequest $request
     * @return void
     */
    public function emailVerification(EmailVerificationRequest $request)
    {
        $request->fulfill();
        return redirect()->route('home')->with('flash_message', ['success' => 'You have successfully verified your account.'] );
    }
}
