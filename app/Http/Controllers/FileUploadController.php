<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileUploadController extends Controller
{
    public function FileUpload(Request $request) {
        $file_route = $request->file->store('public/resource');
        $jsonString = file_get_contents(base_path('\storage\app\\' . $file_route));
        $json = json_decode($jsonString, true);
//      $id_rest = $request->id;

        return $json;
    }
}
