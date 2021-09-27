<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use Storage;

class MinioStorageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Storage::extend('minio', function() {
        //     $client = new S3Client([
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

        //     $options = [
        //         'override_visibility_on_copy' => true
        //     ];

        //     return new Filesystem(new AwsS3Adapter($client, $_ENV['BUCKET_MINIO'], '', $options));
        // });
    }
}
