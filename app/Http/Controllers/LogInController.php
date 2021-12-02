<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailSubmitted;
use Error;
use Exception;
use Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Validator;

class LogInController extends Controller 
{
    public function requestVerificationCode(Request $request) {

        $validator = Validator::make($request->all(), 
            [
                'email' => ['required']
            ],
            [],
            []
        );

        if ($validator -> fails()) {
            return response() -> json(
                [
                    'statusCode'    => 400,
                    'message'       => 'You need to Input E-mail Address'
                ]
                );
        };

        $data = $validator -> valid();

        // VALIDATE AN E-MAIL
        $email = $data['email'];
        $length = strlen($email);
        $firstCharacter = substr($email, 0, 1);
        $lastCharacter = substr($email, $length-1,$length-1);
        $characters = str_split($email);

        if (!in_array("@", $characters)) {
            return response()->json(
                [
                    'statusCode' => 400,
                    'message' => 'E-mail Address must contain "@" symbol'
                ]
            ); 
        }

        if ($firstCharacter == "." || $firstCharacter == "+" || $firstCharacter == "_" || $firstCharacter == "-") {
            return response()->json(
                [
                    'statusCode' => 400,
                    'message' => 'E-mail Address mustn\'t start with a symbol'
                ]
            );
        }

        if ($lastCharacter == "." || $lastCharacter == "+" || $lastCharacter == "_" || $lastCharacter == "-") {
            return response()->json(
                [
                    'statusCode' => 400,
                    'message' => 'E-mail Address mustn\'t end with symbol'
                ]
            );
        }

        // CAN'T HAVE 2 SPECIAL CHARACTERS NEXT TO EACH OTHER
        for ($i = 0; $i < $length - 2; $i++) {
            $current = $characters[$i];
            $next = $characters[$i + 1];

            if($current == "." || $current == "+" || $current == "_" || $current == "-") {
                if($next == "." || $next == "+" || $next == "_" || $next == "-") {
                    return response()->json(
                        [
                            'statusCode' => 400,
                            'message' => 'E-mail Address mustn\'t have 2 special characters next to each other'
                        ]
                    );
                }
            }
        }

        // Generate a passcode for the User
        $passcode = strval(rand(100000, 999999));

        // Find User by inputed e-mail
        $user = User::where('email', $email) -> first();

        // If there is a User with e-mail, just update his passcode
        if ($user) {
            $user -> passcode = $passcode;
            $user -> save();
        } else {
            // If there is no User with that e-mail, create new User with e-mail, passcode and role
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'email'     => $email,
                    'passcode'  => $passcode,
                    'role'      => 'user',
                    'status'    => 'active'
                ]
            );
        }     

        // Send an e-mail with the passcode
        try {
            Mail::to($email)->send(new EmailSubmitted($passcode));
            
            if ($user -> status == 'active') {
                return response()->json(
                    [
                        'user' => $user,
                        'statusCode' => 200,
                        'message' => 'Verification code has been sent. Please check your e-mail.'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'user' => $user,
                        'statusCode' => 400,
                        'message' => 'Requested login. You are currently inactive.'
                    ]
                );
            }
        } catch (Error $error) {

            return response()->json(
                [
                    'statusCode' => 500,
                    'message' => 'There has been an error. Please contact the Administration.'
                ]
            );
        }
    }
    
    public function authenticate(Request $request) {
        // GET EMAIL AND PASSCODE VALUES FROM THE REQUEST
        $email = $request->input('email');
        $passcode = $request->input('passcode');

        // CHECK AGAINST THE DATABASE
        try {
            $user = User::where('email', '=' ,$email)
                ->where('passcode', '=', $passcode)
                ->firstOrFail();  

            // HASH PASSCODE TO GET auth_token FOR SESSION
            $authToken = Hash::make($passcode, [
                'rounds' => 12
            ]);

            if ($user -> status ==  'active') {
                return response()->json(
                    [
                        'statusCode'    => 200,
                        'authenticated' => true,
                        'user'          => $user,
                        'authToken'     => $authToken
                    ]
                );
            } else {
                return response()->json(
                    [
                        'statusCode'    => 401,
                        'authenticated' => false,
                        'user'          => 'noUser',
                        'authToken'     => 'noToken'
                    ]
                );
            }
        } catch (ModelNotFoundException $error) {
            return response()->json(
                [
                    'statusCode' => 400,
                    'authenticated' => 'false',
                    'message' => 'Incorrect e-mail or password. Please try again.'
                ], 
            );
        } catch (Exception $error) {
            return response()->json(
                [
                    'message' => 'Error'
                ], 500
            );
        }
    }
}