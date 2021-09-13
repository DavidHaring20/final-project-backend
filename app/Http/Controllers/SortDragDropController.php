<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Validator;

class SortDragDropController extends Controller
{
    public function update(Request $request) {
        $validator = Validator::make($request -> all(),
            [
                'idArray' => 'required',
                'positionArray' => 'required',
                'itemId' => 'required'
            ],
            [],
            []
        );

        if ($validator -> fails()) {
            return response() -> json(
                [
                    'errorMessage' => 'Something went wrong'
                ]
            );
        }

        $data = $validator -> valid();

        $positions = $data['positionArray'];
        $ids = $data['idArray'];
        $itemId = $data['itemId'];
        
        $item = Item::findOrFail($itemId);
        $subcategoryId = $item -> subcategory -> id;
        $items = Item::where('subcategory_id', $subcategoryId)->get();

        for ($i = 0; $i < sizeOf($positions); $i++) {
            foreach($items as $item) {
                if($item -> id == $ids[$i]) {
                    $item -> position = $i + 1;
                    $item -> save();
                }
            }
        }       

        return response() -> json(
            [
                'updatedArray' => $items
            ]
        );
    }
}
