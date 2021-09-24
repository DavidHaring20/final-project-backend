<?php

namespace App\Http\Controllers;

use App\Models\User;
use Aws\S3\S3Client;
use Illuminate\Http\Request;
use Storage;
use Validator;

class FileController extends Controller
{
    // public function getPresignedUrl(Request $request) {
    //     $validator = Validator::make(
    //         $request -> all(),
    //         [
    //             'userId' => 'required',
    //             'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
    //         ],
    //         [],
    //         []
    //     );

    //     if ($validator -> fails()) {
    //         return response() -> json(
    //             [
    //                 'message' => 'Error. Wrong User.'
    //             ], 400
    //         );
    //     }

    //     $data = $validator -> valid();
    //     $userId = $data['userId'];
    //     User::findOrFail($userId);

    //     $s3Client = new S3Client([
    //         'credentials' => [
    //             'key' => $_ENV['KEY_MINIO'],
    //             'secret' => $_ENV['SECRET_MINIO']
    //         ],
    //         'region' => $_ENV['REGION_MINIO'],
    //         'version' => $_ENV['VERSION_MINIO'],
    //         'bucket_endpoint' => false,
    //         'use_path_style_endpoint' => true,
    //         'endpoint' => $_ENV['ENDPOINT_MINIO']
    //     ]);

    //     $putCommand = $s3Client -> getCommand('PutObject', [
    //         'Bucket' => $_ENV['BUCKET_MINIO'],
    //         'Key' => 'radnom',
    //         // 'SourceFile' => $request -> image, 
    //         // 'Content-Type' => $request -> content_type
    //     ]);

    //     $presignedRequest = $s3Client-> createPresignedRequest($putCommand, '+20 minutes');
    //     $presignedUrl = (string) $presignedRequest -> getUri();

    //     // $path = 'photos/shrek.jpg';
    //     // $signedUrl = Storage::disk('minio') -> temporaryUrl($path, now() -> addMinutes(10));

    //     return response() -> json(
    //         [
    //             'presignedRequest' => $presignedRequest,
    //             'presignedUrl' => $presignedUrl
    //         ]
    //     );
    // }

    // public function putPicture(Request $request) {

    //     $validator = Validator::make($request -> all(), 
    //         [
    //             'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    //             'url' => 'required'
    //         ],
    //         [],
    //         []
    //     );

    //     if ($validator -> fails()) {
    //         return response() -> json(
    //             [
    //                 'errorMessage' => 'Error'
    //             ]
    //         ); 
    //     }

    //     $s3Client = new S3Client([
    //         'credentials' => [
    //             'key' => $_ENV['KEY_MINIO'],
    //             'secret' => $_ENV['SECRET_MINIO']
    //         ],
    //         'region' => $_ENV['REGION_MINIO'],
    //         'version' => $_ENV['VERSION_MINIO'],
    //         'bucket_endpoint' => false,
    //         'use_path_style_endpoint' => true,
    //         'endpoint' => $_ENV['ENDPOINT_MINIO']
    //     ]);

    //     $picture = $request -> image;
        
    //     $data = $validator -> valid();
    //     $url = $data['url'];        
    //     $path = Storage::disk('minio') -> putFile($url, $picture);

    //     return response() -> json(
    //         [
    //             'path' => $path
    //         ]
    //     );
    // }
}
