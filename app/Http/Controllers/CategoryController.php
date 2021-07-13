<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function store($id, Request $request) {

        $restaurant = Restaurant::findOrFail($id);

        $newCategory = $restaurant->categories()->create([
            'position' => 1,
        ]);

        $translations = collect(json_decode($request->translations));
        foreach ($translations as $language_code => $value) {
            $newCategory->translations()->create([
                'language_code' => $language_code,
                'is_default' => false,
                'name' => $value,
            ]);
        }

        return response()->json([
            'message' => 'Category is created'
        ]);
    }

    public function destroy($id) {

        $category = Category::findOrFail($id);

        $category->delete();

        return response()->json([
            'message' => 'Category has been deleted'
        ]);
    }
}
