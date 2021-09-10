<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubcategoriesTranslation;
use App\Models\Subcategory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class SubcategoryController extends Controller
{
    public function store($id, Request $request) {

        $validatedData = $request->validate([
            'translations' => ['required']
        ]);

        if($validatedData) {
            $category = Category::findOrFail($id);
            $translations = collect(json_decode($request->translations));

            $subcategories = $category -> subcategories;
            $numberOfSubcategories = sizeof($subcategories);
            $position = $numberOfSubcategories + 1;

            try {
                DB::beginTransaction();

                $newSubcategory = $category->subcategories()->create([
                    'position' => $position,
                ]);

                foreach ($translations as $language_code => $name) {
                    if($name) {
                        $newSubcategory->translations()->create([
                            'language_code' => $language_code,
                            'is_default' => false,
                            'name' => $name,
                        ]);
                    }
                    else {
                        throw new Exception('Name is empty');
                    }
                }

                DB::commit();
            } catch(Throwable $e) {
                DB::rollBack();
                report($e);
            }

            $newSubcategory = Subcategory::with('translations', 'items')->find($newSubcategory->id);

            return response()->json(
                [
                    'data' =>
                    [
                        'subcategory' => $newSubcategory,
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
            $subcategory_translations = SubcategoriesTranslation::where('subcategory_id', $id)->get();
            $translations = collect(json_decode($request->translations));

            try {
                DB::beginTransaction();

                foreach($subcategory_translations as $subcategory_translation) {
                    if($translations[$subcategory_translation->language_code]) {
                        $subcategory_translation->name = $translations[$subcategory_translation->language_code];
                        $subcategory_translation->save();
                    }
                    else {
                        throw new Exception('Name is empty');
                    }
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
        else {
            return response()->json(
                'The translations field has to be provided.'
            );
        }
    }

    public function destroy($id) {

        $subcategory = Subcategory::findOrFail($id);

        $subcategory->delete();

        return response()->json([
            'message' => 'Subcategory has been deleted'
        ]);
    }
}
