<?php

namespace App\Http\Controllers;
use App\Models\Restaurant;
use App\Models\RestaurantTranslation;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function show($id) {

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

    public function showBySlug($slug) {
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
            )->where('slug', $slug)->firstOrFail();

        return response()->json(
            [
                'data' =>
                [
                    'restaurant' => $restaurant,
                ]
            ]
        );
    }

    public function index() {
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

    public function editFooter($id, Request $request) {

        $restaurant = Restaurant::findOrFail($id);
        $translations = collect(json_decode($request->translations));

        foreach($restaurant->translations as $restaurantTranslation) {
            $restaurantTranslation->footer = $translations[$restaurantTranslation->language_code];
            $restaurantTranslation->save();
        }

        $restaurantTranslations = $restaurant->translations;

        return response()->json(
            [
                'data' =>
                [
                    'translations' => $restaurantTranslations,
                ]
            ]
        );
    }
}
