<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function store($id, Request $request) {

        $subcategory = Subcategory::findOrFail($id);

        $newItem = $subcategory->items()->create([
            'position' => 1,
            'image_url' => '' //?
        ]);

        $titles = collect(json_decode($request->titles));
        $descriptions = collect(json_decode($request->descriptions));
        $amounts = collect(json_decode($request->amounts));

        foreach ($titles as $language_code => $title) {
            $newItem->translations()->create([
                'language_code' => $language_code,
                'is_default' => false,
                'title' => $title,
                'description' => isset($descriptions[$language_code]) ? $descriptions[$language_code] : null
            ]);
        }

        //Add amounts

        foreach($amounts as $amount) {
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

    public function update($id, Request $request) {

        $titles = collect(json_decode($request->titles));
        $descriptions = collect(json_decode($request->descriptions));
        $amounts = collect($request->amounts);

        $item = Item::findOrFail($id);
        $translations = $item->translations()->get();

        //Update translations and descriptions
        foreach($translations as $translation) {

            $translation->title = $titles[$translation->language_code];
            $translation->description = $descriptions[$translation->language_code];
            $translation->save();
        }

        //Update amounts
        foreach($amounts as $amount) {
            $updatedAmount = $item->amounts()->updateOrCreate(
                ['id' => $amount['id']],
                ['price' => $amount['price']]
            );

            foreach($amount['translations'] as $translationKey => $translationVal) {
                $updatedAmount->translations()->updateOrCreate(
                    ['language_code' => $translationKey],
                    ['description' => $translationVal]
                );
            }
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
}
