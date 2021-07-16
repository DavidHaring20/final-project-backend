<?php

namespace App\Http\Controllers;

use App\Models\CategoriesTranslation;
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

    public function update($id, Request $request) {
        $category_translations = CategoriesTranslation::where('category_id', $id)->get();
        $translations = collect(json_decode($request->translations));

        foreach($category_translations as $category_translation) {
            $category_translation->name = $translations[$category_translation->language_code];
            $category_translation->save();
        }

        return response()->json([
            'message' => 'Category has been updated'
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
