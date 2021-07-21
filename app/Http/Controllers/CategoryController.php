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

        $newCategory = Category::with('translations')->find($newCategory->id);

        return response()->json(
            [
                'data' =>
                [
                    'category' => $newCategory,
                ]
            ]
        );
    }

    public function update($id, Request $request) {
        $category_translations = CategoriesTranslation::where('category_id', $id)->get();
        $translations = collect(json_decode($request->translations));

        foreach($category_translations as $category_translation) {
            $category_translation->name = $translations[$category_translation->language_code];
            $category_translation->save();
        }

        $updatedCategory = Category::with(
            'translations',
            'subcategories',
            'subcategories.translations',
            'subcategories.items',
            'subcategories.items.translations',
            'subcategories.items.amounts',
            'subcategories.items.amounts.translations'
            )->find($category_translations[0]->id);

        return response()->json(
            [
                'data' =>
                [
                    'category' => $updatedCategory,
                ]
            ]
        );
    }

    public function destroy($id) {

        $category = Category::findOrFail($id);

        $category->delete();

        return response()->json([
            'message' => 'Category has been deleted'
        ]);
    }
}
