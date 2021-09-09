<?php

namespace App\Http\Controllers;

use App\Models\StyleMaster;
use App\Models\Style;
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

    // CREATE NEW STYLE PROPERTY AND ADD IT TO ALL STYLES 
    public function store(Request $request) {
        $validator = Validator::make($request->all(),
            [
                'key'   => ['max:30', 'min:5', 'unique:style_default_property_values,key'],
                'value' => ['max:15']
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
        $key = $data['key'];
        $value = $data['value'];

        try {
            StyleMaster::create(
                [
                    'key' => $key,
                    'value' => $value
                ]
            );
        } catch (Exception $exception) {
            return response() -> json(
                [
                    'errorMessage' => $exception -> getMessage()
                ]
            );
        }

        try {
            // SEARCH FOR restaurant_id IN STYLE TABLE
            $differentRestaurants = Style::all()->unique();

            foreach ($differentRestaurants as $restaurant) {
                Style::create(
                    [
                        'key'   => $key,
                        'value' => $value,
                        'restaurant_id' => $restaurant -> restaurant_id
                    ]
                );
            }
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
                'message'                   => 'Successfully created style master property and property in Style Table.',
                'newStyleMasterProperty'    => $lastStyleMasterProperty,
                'addedRowsInStyleTable'     => sizeof($differentRestaurants)
            ]
        );
    }

    // DELETE STYLE PROPERTY FROM MASTERSTYLE AND ALL OTHER STYLES
    public function destroy($id) {
        $styleMasterProperty = StyleMaster::find($id);
        $key = $styleMasterProperty -> key;

        $styleMasterProperty -> delete();
        $deletedRows = Style::where('key', '=', $key) -> delete();


        return response() -> json(
            [
                'message'                       => 'Successfully deleted styleMasterProperty.',
                'deletedStyleProperty'          => $styleMasterProperty,
                'deletedRowsFromStyleTable'     => $deletedRows
            ]
        );
    }
}
