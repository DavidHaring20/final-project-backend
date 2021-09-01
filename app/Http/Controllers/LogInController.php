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

class LogInController extends Controller 
{
    public function requestVerificationCode(Request $request) {

        $email = $request->input('email');

        // VALIDATE AN E-MAIL
        $length = strlen($email);
        $firstCharacter = substr($email, 0, 1);
        $lastCharacter = substr($email, $length-1,$length-1);
        $characters = str_split($email);

        if ($length < 1) {
            return response()->json(
                [
                    'statusCode' => 400,
                    'message' => 'Potrebno unijeti E-mail Adresu'
                ]
            );
        }

        if (!in_array("@", $characters)) {
            return response()->json(
                [
                    'statusCode' => 400,
                    'message' => 'E-mail Adresa mora sadržavati "@" simbol'
                ]
            ); 
        }

        if ($firstCharacter == "." || $firstCharacter == "+" || $firstCharacter == "_" || $firstCharacter == "-") {
            return response()->json(
                [
                    'statusCode' => 400,
                    'message' => 'E-mail Adresa nesmije počinjati sa simbolom'
                ]
            );
        }

        if ($lastCharacter == "." || $lastCharacter == "+" || $lastCharacter == "_" || $lastCharacter == "-") {
            return response()->json(
                [
                    'statusCode' => 400,
                    'message' => 'E-mail Adresa nesmije završavati sa simbolom'
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
                            'message' => 'E-mail Adresa nesmije sadržavati dva uzastopna simbola'
                        ]
                    );
                }
            }
        }

        // GENERATE A PASSCODE
        $passcode = strval(rand(100000, 999999));

        // SEND AN EMAIL WITH TEXT AND PASSCODE
        // CREATE NEW USER AND STORE EMAIL AND PASSCODE
        try {
            Mail::to($email)->send(new EmailSubmitted($passcode));

            $user = new User;

            $user -> email = $email;
            $user -> passcode = $passcode;
            $user -> role = 'role';

            $user->save();

            return response()->json(
                [
                    'statusCode' => 200,
                    'message' => 'Verifikacijski kod je poslan. Molim provjerite e-mail'
                ]
            );
        } catch (Error $error) {

            return response()->json(
                [
                    'statusCode' => 500,
                    'message' => 'Došlo je do pogreške. Molim pokušajte ponovo ili kontaktirajte administratora',
                    'errorMessage' => "Error =>" + $error 
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

            return response()->json(
                [
                    'statusCode'    => 200,
                    'authenticated' => true,
                    'user'          => $user,
                    'authToken'     => $authToken
                ]
            );
        } catch (ModelNotFoundException $error) {
            return response()->json(
                [
                    'statusCode' => 400,
                    'authenticated' => 'false',
                    'message' => 'Krivo unesen e-mail ili passcode. Molim pokušajte ponovo.'
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