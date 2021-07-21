<?php

namespace App\Http\Controllers;
use App\Models\Restaurant;

class RestaurantController extends Controller
{
    public function index($id) {

        $restaurant = Restaurant::with(
            'translations',
            'languages',
            'categories',
            'styles',
            'categories.translations',
            'categories.subcategories',
            'categories.subcategories.translations',
            'categories.subcategories.items',
            'categories.subcategories.items.translations',
            'categories.subcategories.items.amounts',
            'categories.subcategories.items.amounts.translations'
            )->find($id);

        return response()->json(
            [
                'data' =>
                [
                    'restaurant' => $restaurant,
                ]
            ]
        );
    }

    public function show() {
        $restaurants = Restaurant::with(
            'translations',
            'languages',
            )->get();

        return response()->json(
            [
                'data' =>
                [
                    'restaurants' => $restaurants,
                ]
            ]
        );
    }

    public function destroy($id) {

        $restaurant = Restaurant::findOrFail($id);

        $restaurant->delete();

        return response()->json([
            'message' => 'Restaurant has been deleted'
        ]);
    }
}
