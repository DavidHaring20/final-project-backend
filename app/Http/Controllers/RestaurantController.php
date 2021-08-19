<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Restaurant;
use App\Models\Style;
use Validator;
use Exception;
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
            $currency = $request->currency;
            $names = collect(json_decode($request->names));
            $footers = collect(json_decode($request->footers));
            $languages = collect(json_decode($request->languages));

            $slug = $names['hr'];
            $slug = strtolower(preg_replace('/\s+/', '-', $slug));

            try {
                DB::beginTransaction();

                if($currency) {
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
                        if($name) {
                            $newRestaurant->translations()->create(
                                [
                                    'language_code' => $languageCode,
                                    'is_default' => false,
                                    'name' => $name,
                                    'footer' => $footers[$languageCode]
                                ]
                            );
                        }
                        else {
                            throw new Exception('Name is empty');
                        }
                    }
                    DB::commit();
                }
                else {
                    throw new Exception('Currency is empty');
                }
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

    // SELECT STYLE
    public function select($id, Request $request) {
    
        $foundRestaurant = Restaurant::findOrFail($id);

        $validator = Validator::make($request->all(),
            [
                'styleId' => ['required']
            ],
            [],
            []
        );
        try {
            if ($validator->fails()) {
                return response() -> json([
                    'message' => 'Something is wrong.'
                ], 400); 
            }
            $data = $validator -> valid();

            $foundStyle = Style::findOrFail($data['styleId']);

            $foundRestaurant['style_id'] = $data['styleId'];

            $foundRestaurant->save();

            return response() -> json(
                [
                'data' => [
                    'message' => 'Style selected successfully',
                ]
            ]);
        } catch (\Error $e) {
            return response() -> json(
                [
                'error' => 'Something went wrong.'
            ]);
        }
    }
}
