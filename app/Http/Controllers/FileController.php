<?php

namespace App\Http\Controllers;

use App\Models\User;
use Aws\S3\S3Client;
use Illuminate\Http\Request;
use Validator;

class FileController extends Controller
{
    public function getPresignedUrl(Request $request) {
        // Create a Validator
        $validator = Validator::make(
            $request -> all(),
            [
                'userId'    => ['required'],
                'imageName' => ['required', 'string']
            ],
            [],
            []
        );

        // Handle Validator fail
        if ($validator -> fails()) {
            return response() -> json(
                [
                    'message' => 'Error. Wrong User.'
                ], 400
            );
        }

        $data = $validator -> valid();
        $userId = $data['userId'];
        $imageName = $data['imageName'];
        
        // Check if the User that wants to get URL exists
        User::findOrFail($userId); 

        // Create Client
        $client = new S3Client([
            'credentials' => [
                'key' => $_ENV['MINIO_KEY'],
                'secret' => $_ENV['MINIO_SECRET']
            ],
            'region' => $_ENV['MINIO_REGION'],
            'version' => $_ENV['MINIO_VERSION'],
            'bucket_endpoint' => false,
            'use_path_style_endpoint' => true,
            'endpoint' => $_ENV['MINIO_ENDPOINT']
        ]);

        // Create Put Command
        $cmd = $client -> getCommand('PutObject', [
            'Bucket' => $_ENV['MINIO_BUCKET'],
            'Key'   => $imageName,
            'Content-Type' => 'image/jpeg'
        ]); 

        // Create Presigned Request
        $presignedRequest = $client -> createPresignedRequest($cmd, '+20 minutes');

        // Create Presigned URL out of Presigned Request
        $presignedUrl = (string) $presignedRequest -> getUri();

        return response() -> json(
            [
                'imageName' => $imageName,
                'presignedUrl' => $presignedUrl
            ]
        );
    }
}
