<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileImportController extends Controller
{
    public function importJSON(Request $request) {
        $file = $request->file;

        $stringContent = file_get_contents($file);
        $jsonContent = json_decode($stringContent);

        return response()->json(
            [
                'content' => $jsonContent
            ]
        );
    }
}
