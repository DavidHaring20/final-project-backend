<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

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

        return($amounts);
    }

    public function update($id, Request $request) {
        $titles = collect(json_decode($request->titles));
        $descriptions = collect(json_decode($request->descriptions));
        $prices = collect(json_decode($request->prices));
        $amount_descriptions = collect($request->amount_descriptions);

        $item = Item::findOrFail($id);
        $translations = $item->translations()->get();
        $amounts = $item->amounts()->get();


        foreach($translations as $translation) {

            $translation->title = $titles[$translation->language_code];
            $translation->description = $descriptions[$translation->language_code];
            $translation->save();
        }

        $i = 1;
        foreach($amounts as $amount) {
            return($amount);
            //ovo je ako amount vec postoji:
            try {
                DB::beginTransaction();
                $amount->update(['price' => $prices[$i]]);

                $translations = $amount->translations();

                foreach($amount_descriptions as $amount) {
                    foreach($amount as $language_code => $description) {
                        $translations->updateOrCreate(
                            ['language_code' => $language_code],
                            [
                                'language_code' => $language_code,
                                'is_default' => false,
                                'description' => $description
                            ]
                        );
                    }
                }

                DB::commit();
            } catch (Throwable $e) {
                DB::rollBack();
                report($e);
            }
            $i++;
        }
    }

    public function destroy($id) {

        $item = Item::findOrFail($id);

        $item->delete();

        return response()->json([
            'message' => 'Item has been deleted'
        ]);
    }
}
