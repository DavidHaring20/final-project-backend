<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Validation\Rule;
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

        $validator = Validator::make($request -> all(),
            [
                'languageCode' => ['required', 'unique:languages,language_code', 'min:2', 'max:3'],
                'languageName' => ['required', 'unique:languages,language_name']
            ],
            [],
            []
        );

        if ($validator -> fails()) {
            return response() -> json(
                [
                    'errorMessage' => $validator -> messages()
                ]
            );
        }

        $data = $validator -> valid();

        if($data) {
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
                'languageCode' => [
                    'required', 
                    Rule::unique('languages', 'language_code') -> ignore($request -> languageCode, 'language_code'), 
                    'min:2', 
                    'max:3'
                ],
                'languageName' => [
                    'required', 
                    Rule::unique('languages', 'language_name') -> ignore($request -> languageName, 'language_name')
                ]
            ],
            [],
            []
        );

        if ($validator -> fails()) {
            return response() -> json(
                [
                    'errorMessage' => $validator -> messages()
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
