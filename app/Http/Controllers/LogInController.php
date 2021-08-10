<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailSubmitted;
use DB;
use Error;

class LogInController extends Controller 
{
    public function requestVerificationCode(Request $request) {

        $email = $request->input('e-mail');

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
            Mail::to('95f1301a34-4c4098@inbox.mailtrap.io')->send(new EmailSubmitted($passcode));

            DB::beginTransaction();
            DB::insert('INSERT INTO users (email, passcode) VALUES (?, ?)', [$email, $passcode]);
            
            DB::commit();

            return response()->json(
                [
                    'statusCode' => 200,
                    'message' => 'Verifikacijski kod je poslan. Molim provjerite e-mail'
                ]
            );
        } catch (Error $error) {
            DB::rollback();

            return response()->json(
                [
                    'statusCode' => 500,
                    'message' => 'Došlo je do pogreške. Molim pokušajte ponovo ili kontaktirajte administratora'
                ]
            );
        }
    }

    public function authenticate(Request $request) {
        // GET EMAIL AND PASSCODE VALUES FROM THE REQUEST
        $email = $request->input('e-mail');
        $passcode = $request->input('passcode');

        // CHECK AGAINST THE DATABASE
        try {
            DB::beginTransaction();
            $user = DB::select('SELECT * FROM users WHERE email = :email AND passcode = :passcode',
                ['email' => $email, 'passcode' => $passcode]);

            if ($user == null) {
                return response()->json(
                    [
                        'message' => 'There is no such user'
                    ]
                );
            }

            return response()->json(
                [
                    'data' => [
                        'message' => 'E-mail and passcode correct',
                        'userFound' => true,
                        'user' => $user
                    ]
                ]
            );
        } catch (Error $error) {
            DB::rollBack();

            return response()->json(
                [
                    'message' => 'Error: ' + $error->getMessage()
                ]
            );
        }

    }
}