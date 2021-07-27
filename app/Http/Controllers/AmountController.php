<?php

namespace App\Http\Controllers;

use App\Models\Amount;
use App\Models\Item;
use Illuminate\Http\Request;

class AmountController extends Controller
{
    public function store($id, Request $request) {
        $languages = $request->languages;

        $item = Item::findOrFail($id);

        $newAmount = $item->amounts()->create([
            'position' => 1,
            'price' => null
        ]);

        foreach ($languages as $language) {
            $newAmount->translations()->create([
                'language_code' => $language['language_code'],
                'is_default' => false,
                'description' => '',
            ]);
        }

        $newAmount = Amount::with('translations')->find($newAmount->id);

        return response()->json(
            [
                'data' =>
                [
                    'amount' => $newAmount,
                ]
            ]
        );
    }

    public function destroy($id) {

        $amount = Amount::findOrFail($id);

        $amount->delete();

        return response()->json([
            'message' => 'Amount has been deleted'
        ]);
    }
}
