<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileImportController extends Controller
{
    public function importJSON(Request $request) {
        $file = $request->file;

        $content = file_get_contents($file);

        // print_r($content);

        return response()->json(
            [
                'content' => $content
            ]
        );
    }
}
