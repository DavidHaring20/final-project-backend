<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubcategoriesTranslation;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class SubcategoryController extends Controller
{
    public function store($id, Request $request) {

        $category = Category::findOrFail($id);
        $translations = collect(json_decode($request->translations));

        try {
            DB::beginTransaction();

            $newSubcategory = $category->subcategories()->create([
                'position' => 1,
            ]);

            foreach ($translations as $language_code => $value) {
                $newSubcategory->translations()->create([
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

        $newSubcategory = Subcategory::with('translations')->find($newSubcategory->id);

        return response()->json(
            [
                'data' =>
                [
                    'subcategory' => $newSubcategory,
                ]
            ]
        );
    }

    public function update($id, Request $request) {
        $subcategory_translations = SubcategoriesTranslation::where('subcategory_id', $id)->get();
        $translations = collect(json_decode($request->translations));

        try {
            DB::beginTransaction();

            foreach($subcategory_translations as $subcategory_translation) {
                $subcategory_translation->name = $translations[$subcategory_translation->language_code];
                $subcategory_translation->save();
            }

            DB::commit();
        } catch(Throwable $e) {
            DB::rollBack();
            report($e);
        }

        $updatedSubcategory = Subcategory::with('translations', 'items', 'items.translations', 'items.amounts')->find($subcategory_translations[0]->subcategory_id);

        return response()->json(
            [
                'data' =>
                [
                    'subcategory' => $updatedSubcategory,
                ]
            ]
        );
    }

    public function destroy($id) {

        $subcategory = Subcategory::findOrFail($id);

        $subcategory->delete();

        return response()->json([
            'message' => 'Subcategory has been deleted'
        ]);
    }
}
