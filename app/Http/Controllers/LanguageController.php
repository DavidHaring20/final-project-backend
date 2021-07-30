<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function index() {

        $languages = Language::get();

        return response()->json(
            [
                'data' =>
                [
                    'languages' => $languages,
                ]
            ]
        );
    }

    public function store(Request $request) {

        $validatedData = $request->validate([
            'languageCode' => ['required'],
            'languageName' => ['required'],
        ]);

        if($validatedData) {
            $languageCode = collect($request->languageCode);
            $languageName = collect($request->languageName);

            Language::create([
                'language_code' => $languageCode[0],
                'language_name' => $languageName[0]
            ]);
        }
        else {
            return response()->json(
                'The language code and language name fields have to be provided.'
            );
        }
    }
}
