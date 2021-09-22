<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Restaurant;
use App\Models\RestaurantTranslation;
use App\Models\Style;
use App\Models\StyleMaster;
use App\Models\User;
use Barryvdh\Reflection\DocBlock\Type\Collection;
use Validator;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use PhpParser\ErrorHandler\Collecting;
use PhpParser\Node\Expr\Cast\Object_;
use stdClass;

class RestaurantController extends Controller
{
    public function show($id) {
        $restaurant = Restaurant::with([
            'translations',
            'languages',
            'categories' => function ($query) {
                $query
                    ->orderBy('position');
            },
            'categories.translations',
            'categories.subcategories' => function($query) {
                $query
                    -> orderBy('position');
            },
            'categories.subcategories.translations',
            'categories.subcategories.items',
            'categories.subcategories.items.translations',
            'categories.subcategories.items.amounts',
            'categories.subcategories.items.amounts.translations'
            ])->find($id);
        
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

    public function index($userId) {
        // Get restaurant with translations and languages where it matches User Id
        $restaurants = Restaurant::where('user_id', $userId)->with('translations', 'languages')->get();

        return response()->json(
            [
                'data' =>
                [
                    'restaurants' => $restaurants
                ]
            ]
        );
    }

    public function store(Request $request) {

        $validatedData = $request->validate([
            'currency'  => ['required'],
            'name'     => ['required'],
            'footers'   => ['required'],
            'languages' => ['required'],
            'userId'    => ['required']
        ]);


        // Proceed if data all data is provided
        if($validatedData) {
            // Collect and transform data in using 'json_decode'
            $currency = $request -> currency;
            $name = $request -> name;
            $footers = collect(json_decode($request->footers));
            $languages = collect(json_decode($request->languages));
            $userId = intval(json_decode($request -> userId));

            //Restaurant position 
            $restaurants = Restaurant::where('user_id', $userId)->get();
            $numberOfRestaurants = sizeOf($restaurants);
            $position = $numberOfRestaurants + 1;

            // Create slug
            $slug = $name;
            $slug = strtolower(preg_replace('/\s+/', '-', $slug));

            // Create restaurant with it's languages and translations for that languages
            DB::beginTransaction();

            if($currency) {
                $newRestaurant = Restaurant::create(
                    [
                        'position'  => $position,
                        'currency'  => $currency,
                        'slug'      => $slug,
                        'user_id'   => $userId
                    ]
                );

                foreach($languages as $language) {
                    $existingLanguage = Language::where('language_name', $language)->firstOrFail();

                    $newRestaurant->languages()->attach($existingLanguage);
                }

                foreach($footers as $footerKey => $footerName) {
                    if($footerKey) {
                        $existingLanguage = Language::where('language_name', $footerKey)->firstOrFail();
                        $newRestaurant->translations()->create(
                            [
                                'language_code' => $existingLanguage->language_code,
                                'is_default' => false,
                                'name' => $name,
                                'footer' => $footers[$footerKey]
                            ]
                        );
                    }
                    else {
                        throw new Exception('Name is empty');
                    }
                }
                
                // Save changes to all tables done during creating of a restaurant
                DB::commit();

                // Create style which is clone of 'masterStyle' for newly created restaurant
                $restaurant_id = Restaurant::max('id');
                $stylePropertiesFromMasterStyle = StyleMaster::all();

                foreach ($stylePropertiesFromMasterStyle as $styleProperty) {
                    Style::create( [
                        'key' => $styleProperty['key'],
                        'value' => $styleProperty['value'],
                        'restaurant_id' => $restaurant_id
                    ]
                    );
                }
            } else {
                throw new Exception('Currency is empty');
            }

            // Get restaurant which will be returned to the DOM in Frontend
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

    public function editRestaurantName(Request $request, $restaurantId) {

        // Validate Request data
        $validator = Validator::make(
            $request -> all(),
            [
                'updatedRestaurantName' => 'required', 'string', 'min:5', 'max:30'
            ],
            [],
            []
        );

        if ($validator -> fails()) {
            return response() -> json(
                [
                    'errorMessage' => 'Restaurant Name is missing.'
                ]
            );
        }

        $data = $validator -> valid();
        $updatedRestaurantName = $data['updatedRestaurantName'];

        // Get all Restaurant Translations that belong to
        $updatedRows = RestaurantTranslation::where('restaurant_id', $restaurantId) -> update(['name' => $updatedRestaurantName]);

        return response() -> json(
            [
                'updatedRows' => $updatedRows
            ]
        ); 

    }

    public function displayInfoForEditingSlug() {
        // Gather all data
        $usernames = User::orderBy('id') -> get('email');
        $restaurantSlugs = Restaurant::orderBy('id') -> get('slug');
        $restaurantNames = RestaurantTranslation::orderBy('id') -> where('language_code', 'hr') -> get('name');

        $dataObjects = array();

        // Put everything into same object
        for ($i = 0; $i < sizeof($usernames); $i++) {
            $dataObject = new stdClass;
            $dataObject -> index = $i; 
            $dataObject -> username = $usernames[$i] -> email;
            $dataObject -> slug = $restaurantSlugs[$i] -> slug;
            $dataObject -> restaurantName = $restaurantNames[$i] -> name;

            array_push($dataObjects, $dataObject);
        }

        // Return that object
        return response() -> json([
            'dataObjects'   => $dataObjects
        ]);
    }

    public function editSlug(Request $request, $slug) {

        $validator = Validator::make($request -> all(),
            [
                'slug' => [
                    'required', 
                    Rule::unique('restaurants', 'slug') -> ignore($slug, 'slug'), 
                    'min:8', 
                    'max:30'
                ]
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

        $restaurant = Restaurant::where('slug', '=', $slug) -> firstOrFail();

        $restaurant -> slug = $data['slug'];

        $restaurant -> save();

        return response() -> json(
            [
                'updatedSlug'   => $restaurant -> slug,
                'restaurant'    => $restaurant
            ]
        ); 
    }
}
