<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Subcategory;
use Illuminate\Http\Request;

use function Symfony\Component\String\b;

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
        $prices = collect(json_decode($request->prices));
        $amount_descriptions = collect($request->amount_descriptions);

        foreach ($titles as $language_code => $title) {
            $newItem->translations()->create([
                'language_code' => $language_code,
                'is_default' => false,
                'title' => $title,
                'description' => isset($descriptions[$language_code]) ? $descriptions[$language_code] : null
            ]);
        }

        $index = 1;

        foreach($amount_descriptions as $amount) {
            $newAmount = $newItem->amounts()->create([
                'position' => 1,
                'price' => $prices[$index]
            ]);

            foreach ($amount as $language_code => $value) {
                $newAmount->translations()->create([
                    'language_code' => $language_code,
                    'is_default' => false,
                    'description' => isset($value) ? $value : null
                ]);
            }

            $index++;
        }

        return response()->json([
            'message' => 'Item has been created'
        ]);
    }

    public function destroy($id) {

        $item = Item::findOrFail($id);

        $item->delete();

        return response()->json([
            'message' => 'Item has been deleted'
        ]);
    }
}
