<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Storage;
use Str;
use Validator;

class FileController extends Controller
{
    public function getPresignedUrl(Request $request) {
        $validator = Validator::make(
            $request -> all(),
            [
                'userId' => 'required',
            ],
            [],
            []
        );

        if ($validator -> fails()) {
            return response() -> jsonFail(
                [
                    'message' => 'Error. Wrong User.'
                ], 400
            );
        }

        $data = $validator -> valid();
        User::findOrFail($data['userId']);
        $path = 'pictures/'.Str::random(9);


        $presignedUrl = Storage::disk('minio') -> temporaryUrl($path, '+5 minutes');

        return response() -> json(
            [
                'presignedUrl' => $presignedUrl
            ]
        );
    }
}
