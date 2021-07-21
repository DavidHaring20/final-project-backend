<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubcategoriesTranslation;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class SubcategoryController extends Controller
{
    public function store($id, Request $request) {

        $category = Category::findOrFail($id);

        $newSubcategory = $category->subcategories()->create([
            'position' => 1,
        ]);

        $translations = collect(json_decode($request->translations));
        foreach ($translations as $language_code => $value) {
            $newSubcategory->translations()->create([
                'language_code' => $language_code,
                'is_default' => false,
                'name' => $value,
            ]);
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

        foreach($subcategory_translations as $subcategory_translation) {
            $subcategory_translation->name = $translations[$subcategory_translation->language_code];
            $subcategory_translation->save();
        }
        return($subcategory_translations);
    }

    public function destroy($id) {

        $subcategory = Subcategory::findOrFail($id);

        $subcategory->delete();

        return response()->json([
            'message' => 'Subcategory has been deleted'
        ]);
    }
}
