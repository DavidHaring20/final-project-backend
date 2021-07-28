<?php

namespace App\Http\Controllers;

use App\Models\CategoriesTranslation;
use App\Models\Category;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class CategoryController extends Controller
{
    public function store($id, Request $request) {

        $validatedData = $request->validate([
            'translations' => ['required']
        ]);

        if($validatedData) {
            $restaurant = Restaurant::findOrFail($id);
            $translations = collect(json_decode($request->translations));

            try {
                DB::beginTransaction();

                $newCategory = $restaurant->categories()->create([
                    'position' => 1,
                ]);

                foreach ($translations as $language_code => $value) {
                    $newCategory->translations()->create([
                        'language_code' => $language_code,
                        'is_default' => false,
                        'name' => $value,
                    ]);
                }

                DB::commit();
            } catch(Throwable $e) {
                DB::rollBack();
                report($e);
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
        else {
            return response()->json(
                'The translations field has to be provided.'
            );
        }
    }

    public function update($id, Request $request) {
        $validatedData = $request->validate([
            'translations' => ['required']
        ]);

        if($validatedData) {
            $category_translations = CategoriesTranslation::where('category_id', $id)->get();
            $translations = collect(json_decode($request->translations));

            try {
                DB::beginTransaction();

                foreach($category_translations as $category_translation) {
                    $category_translation->name = $translations[$category_translation->language_code];
                    $category_translation->save();
                }

                DB::commit();
            } catch(Throwable $e) {
                DB::rollBack();
                report($e);
            }

            $updatedCategory = Category::with(
                'translations',
                'subcategories',
                'subcategories.translations',
                'subcategories.items',
                'subcategories.items.translations',
                'subcategories.items.amounts',
                'subcategories.items.amounts.translations'
                )->find($category_translations[0]->category_id);

            return response()->json(
                [
                    'data' =>
                    [
                        'category' => $updatedCategory,
                    ]
                ]
            );
        }
        else {
            return response()->json(
                'The translations field has to be provided.'
            );
        }
    }

    public function destroy($id) {

        $category = Category::findOrFail($id);

        $category->delete();

        return response()->json([
            'message' => 'Category has been deleted'
        ]);
    }
}
