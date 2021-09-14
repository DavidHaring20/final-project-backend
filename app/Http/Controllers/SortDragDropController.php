<?php

namespace App\Http\Controllers;

use App\Models\Category;
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

    public function incrementCategoryPosition(Request $request) {
        $validator = Validator::make($request -> all(),
            [
                'categoryId' => 'required'
            ],
            [],
            [],
        );

        if ($validator -> fails()) {
            return response() -> json(
                [
                    'errorMessage' => 'Category ID is required.'
                ]
            ); 
        }

        // Get Data from Validator
        $data = $validator -> valid();
        $categoryId = $data['categoryId'];

        // Get Current Category, Increment and Save it's Position
        $categoryCurrent = Category::findOrFail($categoryId);
        $categoryCurrent -> position += 1;
        $categoryCurrent -> save();

        // Get Id that current Category belongs to
        $restaurantId = $categoryCurrent -> restaurant -> id;
        
        $categoryCurrentPosition = $categoryCurrent -> position;        

        $categoryNext = Category::where('position', $categoryCurrentPosition) 
                                -> where('restaurant_id', $restaurantId) 
                                -> first();

        $categoryNext -> position -= 1;
        $categoryNext -> save();

        return response() -> json(
            [
                'restaurantId' => $restaurantId,
                'categoryCurrent' => $categoryCurrent,
                'categoryNext' => $categoryNext,
                'categoryCurrentPosition' => $categoryCurrentPosition,
                'categoryNextPosition' => $categoryNext -> position,
            ]
        );
    }

    public function decrementCategoryPosition(Request $request) {
        $validator = Validator::make($request -> all(),
            [
                'categoryId' => 'required'
            ],
            [],
            [],
        );

        if ($validator -> fails()) {
            return response() -> json(
                [
                    'errorMessage' => 'Category ID is required.'
                ]
            ); 
        }

        // Get Data from Validator
        $data = $validator -> valid();
        $categoryId = $data['categoryId'];

        // Get Current Category, Increment and Save it's Position
        $categoryCurrent = Category::findOrFail($categoryId);
        $categoryCurrent -> position -= 1;
        $categoryCurrent -> save();

        // Get Restaurant's Id that current Category belongs to
        $restaurantId = $categoryCurrent -> restaurant -> id;
        $categoryCurrentPosition = $categoryCurrent -> position;        

        $categoryPrevious = Category::where('position', $categoryCurrentPosition) 
                                -> where('restaurant_id', $restaurantId) 
                                -> first();

        $categoryPrevious -> position += 1;
        $categoryPrevious -> save();

        return response() -> json(
            [
                'restaurantId' => $restaurantId,
                'categoryCurrent' => $categoryCurrent,
                'categoryPrevious' => $categoryPrevious,
                'categoryCurrentPosition' => $categoryCurrentPosition,
                'categoryPreviousPosition' => $categoryPrevious -> position,
            ]
        );
    }

    public function incrementSubcategoryPosition() {}

    public function decrementSubcategoryPosition() {}
}
