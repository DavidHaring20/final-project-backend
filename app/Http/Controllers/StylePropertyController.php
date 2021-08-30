<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use App\Models\Style;
use Exception;
use Validator;

class StylePropertyController extends Controller
{
    // GET ALL STYLE PROPERTIES FROM RESTAURANT THEY BELONG TO
    public function show ($id) {
        $restaurant = Restaurant::find($id);

        $styles = $restaurant -> styles;

        return response() -> json(
            [
                'message'                   => 'Styles found successfully.',
                'numberOfStyleProperties'   => sizeOf($styles),
                'styleProperties'           => $styles
            ]
        );
    }

    // UPDATE VALUE OF A STYLE
    public function update (Request $request, $id) {
        $validator = Validator::make($request->all(),
        [
            'key'   => 'max:50', 
            'value' => 'max:50'
        ],
        [],
        []
        );

        if ($validator -> fails()) {
            return response() -> json(
                [
                    'message' => 'Please check input'
                ], 400
            );
        }

        $data = $validator -> valid();
        $key = $data['key'];
        $value = $data['value'];

        // SEARCH IF THE RESTAURANT EXISTS
        $restaurant = Restaurant::findOrFail($id);

        try {
            // UPDATE STYLE
            $updatedRows = Style::where('key', '=', $key)
                                ->where('restaurant_id', '=', $id)
                                ->update(['value' => $value]);

            // GET UPDATED STYLE FROM THE DB
            $updatedStyle = Style::where('restaurant_id', '=', $id)
                                ->where('key', '=', $key)
                                ->where('value', '=', $value)
                                ->get();

        } catch (Exception $exception) {
            return response() -> json(
                [
                    'message' => 'Something went wrong.'
                ], 500
            ); 
        }

        // CHECK IF THE STYLE WAS REALLY UPDATED
        if ($updatedRows != 0) {
            return response() -> json(
                [
                    'message'       => 'Updated successfully.',
                    'updatedStyle'  => $updatedStyle,
                    'updatedRows'   => $updatedRows
                ], 200
            );
        } else {
            return response() -> json(
                [
                    'message' => 'Name inputed incorrectly.'
                ], 400
            );
        }
    }
}
