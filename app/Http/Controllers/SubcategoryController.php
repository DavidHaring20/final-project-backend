<?php

namespace App\Http\Controllers;

use App\Models\Category;
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

        return response()->json([
            'message' => 'Subcategory is created'
        ]);
    }

    public function destroy($id) {

        $subcategory = Subcategory::findOrFail($id);

        $subcategory->delete();

        return response()->json([
            'message' => 'Subcategory has been deleted'
        ]);
    }
}
