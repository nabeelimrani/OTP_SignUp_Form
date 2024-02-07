<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Twilio\Rest\Client;

class UserController extends Controller
{
    public function submit(Request $request)
    {
        $isEmailVerified = session('isEmailVerified', false);
        $emailVerifiedExpiresAt = session('isEmailVerified_expires_at', now()->timestamp - 1); // Default to a past timestamp

        // Check if email or phone verification sessions were never initiated
        if (!session()->has('isEmailVerified')) {
            return response()->json(['message' => 'Please verify both <strong>EMAIL</strong> before creating the user.'], 400);
        }

        // Check if email verification has expired
        if (!$isEmailVerified || now()->timestamp > $emailVerifiedExpiresAt) {
            session()->forget(['isEmailVerified', 'isEmailVerified_expires_at']); // Optionally clear the sessions
            return response()->json(['message' => 'Email verification has expired. Please verify your email again.'], 400);
        }
        $isPhoneVerified = $request->phonestatus;
        if ($isEmailVerified) {
            // If both email and phone are verified, create a new user
            $otp = session('email_otp');
            $user = new User();
            $user->name = $request->name;
            $user->country = $request->country;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->gender = $request->gender;
            $user->otp = $otp;

            // Save the user to the database
            $user->save();

            return response()->json(['success' => true, 'message' => 'User created successfully!']);
        } elseif ($isEmailVerified) {
            // If only email is verified, show a message about verifying phone
            return response()->json([
                'success' => false,
                'message' => 'Please verify your <strong>Phone Number</strong> before creating the user.',
            ], 400); // Bad request
        } else {
            // If either email or phone verification fails, return an error response
            return response()->json([
                'success' => false,
                'message' => 'Please verify both <strong>EMAIL</strong> and <strong>PHONE</strong> before creating the user.',
            ], 400); // Bad request
        }

    }
    public function emailOTP(Request $request)
    {
        // Validate the email field
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid email address.'], 422);
        }

        $otp = rand(100000, 999999);
        // Save $otp to your database associated with the email for verification here
        // Store OTP in the Laravel session
        Session::put('email_otp', $otp);

        // Save the user to the database

        // Store the expiration time for OTP verification
        session(['email_otp_expires_at' => now()->addMinutes(1)->timestamp]);
        try {
            Mail::raw("Your OTP is: $otp", function ($message) use ($request) {
                $message->to($request->email);
                $message->subject('Email Verification OTP');
            });

            return response()->json(['message' => 'OTP sent.']);
        } catch (\Exception $e) {
            // Log error or handle accordingly
            return response()->json(['message' => 'Failed to send OTP. Please try again.'], 500);
        }
    }

    public function verifyEmailOTP(Request $request)
    {
        $otpFromUser = $request->input('otp');
        $emailOtp = session('email_otp');
        $otpExpiresAt = session('email_otp_expires_at');

        if (now()->timestamp > $otpExpiresAt) {
            session()->forget(['email_otp', 'email_otp_expires_at']);
            return response()->json(['message' => 'OTP has expired. Please request a new OTP.'], 400);
        }

        if ($otpFromUser == $emailOtp) {
            session(['isEmailVerified' => true]);
            session(['isEmailVerified_expires_at' => now()->addMinutes(2)->timestamp]);
            session()->forget(['email_otp', 'email_otp_expires_at']);
            return response()->json(['message' => 'Email successfully verified.']);
        } else {
            return response()->json(['message' => 'Invalid OTP. Please try again.'], 400);
        }
    }

    public function phoneOTP(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'phone' => 'required|regex:/^\+[1-9][0-9]*$/',
        ], [
            'phone.regex' => 'The phone number should start without zero.',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $phone = $request->input('phone');
        $otp = rand(100000, 999999);

        // Save $otp to your database associated with the phone for verification
        Session::put('phone_otp', $otp);
        // Twilio Client Initialization
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_TOKEN');
        $twilioNumber = env('TWILIO_PHONE');
        try {
            // Initialize Twilio client
            $client = new Client($id, $token);
            $message = $client->messages->create('+923347237975', // to
                [
                    'from' => '+13418991558',
                    'body' => "Your Verification Code is: {$otp}",
                ]
            );

            return response()->json(['message' => 'OTP sent.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to send OTP. Please try again.'], 500);
        }
    }
    public function resendOTP(Request $request)
    {
        // Check if the user is authenticated or has a valid email
        // For example, you can use $request->user() or any other validation

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid email address.'], 422);
        }

        $otp = rand(100000, 999999);
        Session::put('email_otp', $otp);
        session(['email_otp_expires_at' => now()->addMinutes(1)->timestamp]);

        try {
            Mail::raw("Your OTP is: $otp", function ($message) use ($request) {
                $message->to($request->email);
                $message->subject('Email Verification OTP');
            });

            return response()->json(['message' => 'OTP resent.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to resend OTP. Please try again.'], 500);
        }
    }

}
