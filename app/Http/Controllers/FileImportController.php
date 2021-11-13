<?php

namespace App\Http\Controllers;

use App\Models\CategoriesTranslation;
use App\Models\Category;
use App\Models\Restaurant;
use App\Models\Language;
use App\Models\RestaurantTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use Exception;

class FileImportController extends Controller
{
    public function importJSON(Request $request) {
        
        // Validate data that has been sent via Request
        $validator = Validator::make(
            $request->all(),
            [
                'file'      => 'required',
                'userID'    => 'required'
            ],
            [],
            []
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'errorMessage' => 'Error occured when importing file. Please try again.'
                ]
            );
        }

        $data = $validator->valid();

        $file = $data['file'];
        $userID = $data['userID'];

        // Get contents of the file
        $stringContent = file_get_contents($file);
        $jsonContent = json_decode($stringContent);

        // Get Data that is needed to create the Restaurant: Languages, Currency, Restaurant Name and Footer Text
        $restaurantName = $jsonContent->name;
        $restaurantCurrency = $jsonContent->currency;
        $restaurantLanguages = $jsonContent->languages;
        $restaurantFooterTexts = collect($jsonContent->footer_text);

        // Before creating Restaurant, check if there is already a Restaurant with such name
        // If there isn't - create Restaurant
        // If there is - stop and return Error
        $restaurantNames = RestaurantTranslation::where('name', $restaurantName) -> get();

        if (sizeOf($restaurantNames)) {
            return response()->json(
                [
                    'errorMessage' => 'Restaurant with such name alredy exists, please try another name.'
                ]
            );
        }

        // Create a Slug for New Restaurant
        $slug = $restaurantName;
        $slug = strtolower(preg_replace('/\s+/', '-', $slug));

        // Get Available position
        $restaurants = Restaurant::where('user_id', $userID)->get();
        $numberOfRestaurants = sizeOf($restaurants);
        $position = $numberOfRestaurants + 1;

        // Create a Restaurant with Name, Currency, Footer Text and Languages
        DB::beginTransaction();

        if ($restaurantName && $restaurantCurrency) {
            $newRestaurant = Restaurant::create(
                [
                    'position'  => $position,
                    'currency'  => $restaurantCurrency,
                    'slug'      => $slug,
                    'user_id'   => $userID
                ]
            );
        }

        // Until here, it is probably okay
        foreach($restaurantLanguages as $language) {
            $existingLanguage = Language::where('language_code', $language)->firstOrFail();

            $newRestaurant->languages()->attach($existingLanguage);
        }

        foreach($restaurantFooterTexts as $footerKey => $footerName) {
            if($footerKey) {
                $existingLanguage = Language::where('language_code', $footerKey)->firstOrFail();
                $newRestaurant->translations()->create(
                    [
                        'language_code' => $existingLanguage->language_code,
                        'is_default' => false,
                        'name' => $restaurantName,
                        'footer' => $restaurantFooterTexts[$footerKey]
                    ]
                );
            }
            else {
                throw new Exception('Name is empty');
            }
        }

        // Save all changes that were done during Transaction
        DB::commit();

        // Get newly created Restaurant and it's ID
        $newRestaurant = Restaurant::with('translations')->find($newRestaurant->id);
        $newRestaurantId = $newRestaurant->id;

        // Create all Categories, Subcategories and Items from Imported JSON
        $restaurantCategories = collect($jsonContent->categories);

        // Temporary storage
        $categoryId = 0;

        foreach ($restaurantCategories as $restaurantCategory) {
            $categoryTranslations = $restaurantCategory->name;

            // Get highest Position
            $position = Category::max('position');
            $category= Category::create(
                [
                    'position' => $position + 1,
                    'restaurant_id' => $newRestaurantId
                ]
            );

            $categoryId = $category->id;

            foreach ($categoryTranslations as $categoryTranslationsKey => $categoryTranslationsValue) {
                if ($categoryTranslationsKey == "hr") {
                    CategoriesTranslation::create(
                        [
                            'language_code' => $categoryTranslationsKey,
                            'is_default'    => true,
                            'name'          => $categoryTranslationsValue,
                            'category_id'   => $categoryId
                        ]
                    );
                } else {
                    CategoriesTranslation::create(
                        [
                            'language_code' => $categoryTranslationsKey,
                            'is_default'    => false,
                            'name'          => $categoryTranslationsValue,
                            'category_id'   => $categoryId
                        ]
                    );
                }
            }
        }



        return response()->json(
            [
                // 'userID'        => $userID, 
                // 'newRestaurant' => $newRestaurant,
                'message'       => 'Data imported successfully.',
                // 'restaurantCategories' => $restaurantCategories,
                // 'jsonContent'   => $jsonContent
            ]
        );
    }
}
