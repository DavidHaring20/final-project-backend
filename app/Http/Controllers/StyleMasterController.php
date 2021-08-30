<?php

namespace App\Http\Controllers;

use App\Models\StyleMaster;
use Exception;
use Illuminate\Http\Request;
use Validator;

class StyleMasterController extends Controller
{
    // READ ALL STYLE PROPERTIES
    public function index() {
        try {
            $styleMaster = StyleMaster::all();
        } catch (Exception $exception) {
            return response() -> json(
                [
                    'errorMessage' => $exception -> getMessage()
                ]
            );
        }

        return response() -> json(
            [
                'message'       => 'styleMaster list returned successfully.',
                'styleMaster'   => $styleMaster
            ]
        );
    }

    // CREATE NEW STYLE PROPERTY 
    public function store(Request $request) {
        $validator = Validator::make($request->all(),
            [
                'key'   => ['max:50', 'string', 'unique:style_default_property_values'],
                'value' => ['max:50', 'string']
            ],
            [],
            []
        );

        if ($validator -> fails()) {
            return response() -> json(
                [
                    'message' => 'Error. Please check input.'
                ]
            );
        }

        $data = $validator -> valid();

        try {
            StyleMaster::create(
                [
                    'key'   => $data['key'],
                    'value' => $data['value']
                ]
            );
        } catch (Exception $exception) {
            return response() -> json(
                [
                    'errorMessage' => $exception -> getMessage()
                ]
            );
        }

        $idOfLastStyleMasterProperty = StyleMaster::max('id');
        $lastStyleMasterProperty = StyleMaster::find($idOfLastStyleMasterProperty);
        return response() -> json(
            [
                'message' => 'Successfully created style master property.',
                'newStyleMasterProperty' => $lastStyleMasterProperty
            ]
        );
    }

    // DELETE STYLE PROPERTY
    public function destroy($id) {
        $styleMasterProperty = StyleMaster::find($id);

        $styleMasterProperty -> delete();

        return response() -> json(
            [
                'message'               => 'Successfully deleted styleMasterProperty.',
                'deletedStyleProperty'  => $styleMasterProperty
            ]
        );
    }
}
