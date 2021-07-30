<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;


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

    public function store(Request $request) {

        $validatedData = $request->validate([
            'currency' => ['required'],
            'names' => ['required'],
            'footers' => ['required'],
            'languages' => ['required'],
        ]);

        if($validatedData) {
            $currency = collect(json_decode($request->currency));
            $names = collect(json_decode($request->names));
            $footers = collect(json_decode($request->footers));
            $languages = collect(json_decode($request->languages));

            $slug = $names['hr'];
            $slug = strtolower(preg_replace('/\s+/', '-', $slug));


            try {
                DB::beginTransaction();

                $newRestaurant = Restaurant::create(
                    [
                        'position' => 1,
                        'currency' => $currency,
                        'slug' => $slug
                    ]
                );

                foreach($languages as $language) {
                    $existingLanguage = Language::firstOrCreate(
                        ['language_code' => $language->language_code],
                        ['language_name' => $language->language_name]
                    );

                    $newRestaurant->languages()->attach($existingLanguage);
                }

                foreach($names as $languageCode => $name) {
                    $newRestaurant->translations()->create(
                        [
                            'language_code' => $languageCode,
                            'is_default' => false,
                            'name' => $name,
                            'footer' => $footers[$languageCode]
                        ]
                    );
                }
                DB::commit();
            } catch(Throwable $e) {
                DB::rollBack();
                report($e);
            }

            $newRestaurant = Restaurant::with('translations')->find($newRestaurant->id);

            return response()->json(
                [
                    'data' =>
                    [
                        'restaurant' => $newRestaurant,
                    ]
                ]
            );
        }
        else {
            return response()->json(
                'The currency, names, footers and languages fields have to be provided.'
            );
        }
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
