<?php

namespace App\Http\Controllers;

use Validator;
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
            $languageCode = $request->languageCode;
            $languageName = $request->languageName;

            $language = Language::create(
                [
                    'language_code' => $languageCode,
                    'language_name' => $languageName
                ]
            );

            return response() -> json(
                [
                    'message'       => 'Language successfully created',
                    'newLanguage'   => $language
                ]
            );
        }
        else {
            return response() -> json(
                [
                    'message' => 'The language code and language name fields have to be provided.'
                ]
            );
        }
    }

    public function destroy($code) {
        $deletedLanguage = Language::where('language_code', $code)->firstOrFail();
        $deletedLanguage -> delete();

        return response() -> json(
            [
                'message'           => 'Language deleted successfully.',
                'deletedLanguage'   => $deletedLanguage
            ]
        );
    }

    public function update(Request $request, $code) {
        $validator = Validator::make($request -> all(),
            [
                'languageCode' => ['required', 'string', 'max:10'],
                'languageName' => ['required', 'string', 'max:20']
            ],
            [],
            []
        );

        if ($validator -> fails()) {
            return response() -> json(
                [
                    'errorMessage' => 'Please check inputed data.'
                ], 400
            );
        }

        $data = $validator -> valid();

        // Get Language from DB
        $language = Language::where('language_code', $code) -> firstOrFail();

        // Update values 
        $language -> language_code = $data['languageCode'];
        $language -> language_name = $data['languageName'];
        
        $language -> save();

        return response() -> json(
            [
                'message'           => 'Successfully updated Language.',
                'updatedLanguage'   => $language           
            ]
        );
    }
}
