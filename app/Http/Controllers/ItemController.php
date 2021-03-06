<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Subcategory;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use Throwable;

class ItemController extends Controller
{
    public function store($id, Request $request) {
        $validatedData = $request->validate([
            'titles' => ['required'],
            'subtitles' => ['required'],
            'descriptions' => ['required'],
            'amounts' => ['required'],
        ]);

        if($validatedData) {
            $subcategory = Subcategory::findOrFail($id);

            $titles = collect(json_decode($request->titles));
            $subtitles = collect(json_decode($request->subtitles));        
            $descriptions = collect(json_decode($request->descriptions));
            $amounts = collect(json_decode($request->amounts));

            // Get number of Items from DB and increment by 1 to get new item's position
            $items = $subcategory -> items;
            $numberOfItems = sizeof($items);
            $position = $numberOfItems + 1;

            try {
                DB::beginTransaction();

                $newItem = $subcategory->items()->create([
                    'position' => $position,
                    'image_url' => ''
                ]);

                foreach ($titles as $language_code => $title) {
                    if($title) {
                        $newItem->translations()->create([
                            'language_code' => $language_code,
                            'is_default' => false,
                            'title' => $title,
                            'subtitle' => isset($subtitles[$language_code]) ? $subtitles[$language_code] : null,
                            'description' => isset($descriptions[$language_code]) ? $descriptions[$language_code] : null
                        ]);
                    }
                    else {
                        throw new Exception('Title is empty');
                    }
                }

                //Add amounts
                // Check if the prices are null/empty
                foreach($amounts as $amount) {
                    if ($amount->price == "" || $amount->price == null) {
                        DB::rollBack();
                        return response() -> json(
                            [
                                'message' => 'Price can\'t be empty !'
                            ],
                        );
                    } elseif ($amount->price > 9999.99) {       // Check if the price is greater than what DB permits
                        return response() -> json(
                            [
                                'message' => 'Price can\'t be greater than 9999.99'
                            ],
                        ); 
                    } elseif ($amount->price < 0.01) {          // Check if the price is lesser than what DB permits
                        return response() -> json(
                            [
                                'message' => 'Price can\'t be lesser than 0.01'
                            ],
                        ); 
                    } elseif (strpos($amount->price, ',')) {    // Check if the price contains coma (,)
                        return response() -> json(
                            [
                                'message' => 'Price can\'t contain ",". Instead of it use "."'
                            ],
                        ); 
                    }

                    $newAmount = $newItem->amounts()->create([
                        'position' => 1,
                        'price' => $amount->price
                    ]);

                    foreach($amount->translations as $languageCode => $description) {
                        $newAmount->translations()->create([
                            'language_code' => $languageCode,
                            'is_default' => false,
                            'description' => $description
                        ]);
                    }
                }

                DB::commit();
            } catch(Throwable $e) {
                DB::rollBack();
                report($e);
            }

            $categoryId = $subcategory->category_id;
            $newItem = Item::with('translations', 'amounts', 'amounts.translations')->find($newItem->id);

            return response()->json(
                [
                    'data' =>
                    [
                        'categoryId' => $categoryId,
                        'newItem' => $newItem,
                    ]
                ]
            );
        }
        else {
            return response()->json(
                'The titles, descriptions and amounts have to be provided.'
            );
        }
    }

    public function update($id, Request $request) {
        $titles = collect(json_decode($request->titles));
        $subtitles = collect(json_decode($request->subtitles));
        $descriptions = collect(json_decode($request->descriptions));
        $amounts = collect($request->amounts);

        $item = Item::findOrFail($id);
        $translations = $item->translations()->get();

        try {
            DB::beginTransaction();

            //Update translations and descriptions
            foreach($translations as $translation) {
                if($titles[$translation->language_code]) {
                    $translation->title = $titles[$translation->language_code];
                    $translation->subtitle = $subtitles[$translation->language_code];
                    $translation->description = $descriptions[$translation->language_code];
                    $translation->save();
                }
                else {
                    throw new Exception('Title is empty');
                }
            }

            //Update amounts
            foreach($amounts as $amount) {
                if ($amount['price'] == "" || $amount['price'] == null) {
                    DB::rollBack();
                    return response() -> json(
                        [
                            'message' => 'Price can\'t be empty !'
                        ],
                    );
                } elseif ($amount['price'] > 9999.99) {       // Check if the price is greater than what DB permits
                    return response() -> json(
                        [
                            'message' => 'Price can\'t be greater than 9999.99'
                        ],
                    ); 
                } elseif ($amount['price'] < 0.01) {          // Check if the price is lesser than what DB permits
                    return response() -> json(
                        [
                            'message' => 'Price can\'t be lesser than 0.01'
                        ],
                    ); 
                } elseif (strpos($amount['price'], ',')) {    // Check if the price contains coma (,)
                    return response() -> json(
                        [
                            'message' => 'Price can\'t contain ",". Instead of it use "."'
                        ],
                    ); 
                }

                $updatedAmount = $item->amounts()->updateOrCreate(
                    ['id' => $amount['id']],
                    ['price' => $amount['price']]
                );

                foreach($amount['translations'] as $translationKey => $translationVal) {
                    $updatedAmount->translations()->updateOrCreate(
                        ['language_code' => $translationKey],
                        [
                            'language_code' => $translationKey,
                            'is_default' => false,
                            'description' => $translationVal
                        ]
                    );
                }
            }

            DB::commit();
        } catch(Throwable $e) {
            DB::rollBack();
            report($e);
        }

        $subcategory = Subcategory::findOrFail($item->subcategory_id);
        $categoryId = $subcategory->category_id;
        $updatedItem = Item::with('translations', 'amounts', 'amounts.translations')->find($item->id);

        return response()->json(
            [
                'data' =>
                [
                    'categoryId' => $categoryId,
                    'updatedItem' => $updatedItem,
                ]
            ]
        );
    }

    public function destroy($id) {

        $item = Item::findOrFail($id);

        $item->delete();

        return response()->json([
            'message' => 'Item has been deleted'
        ]);
    }

    public function saveImage(Request $request) {

        if ($request->hasFile('image')) {
            if ($request->file('image')->isValid()) {
                $validated = $request->validate([
                    'name' => 'string|max:40',
                    'image' => 'mimes:jpeg,png|max:1014',
                ]);

                $extension = $request->image->extension();
            //     $request->image->storeAs('/public', $validated['name'].".".$extension);
            //     $url = Storage::url($validated['name'].".".$extension);
            //     $file = File::create([
            //        'name' => $validated['name'],
            //         'url' => $url,
            //     ]);
            //     Session::flash('success', "Success!");
            //     return \Redirect::back();
            }
        }
        abort(500, 'Could not upload image :(');
    }
}
