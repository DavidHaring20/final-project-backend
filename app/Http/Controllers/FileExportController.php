<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use stdClass;

class FileExportController extends Controller
{
    public function toJson($restaurant) {
        $socials_array = [];

        $restaurant->networks->each(function($social) use (&$socials_array) {
            $social_name = $social->name;
            $socials_array[$social_name] = $social->link;
        });

        $socials_array = (object)$socials_array;

        $style_array = new stdClass();

        $restaurant->styles->each(function($style) use (&$style_array) {
            $style_array->headerImageMaxHeight = $style->header_image_max_height;
            $style_array->itemTitleFontFamily = $style->item_title_font_family;
            $style_array->itemTitleDisplay = $style->item_title_display;

            $style_array->itemSubtitleColor = $style->item_subtitle_color;
            $style_array->itemDescriptionColor = $style->item_description_color;

            $style_array->itemTitleFontWeight = $style->item_title_font_weight;
            $style_array->itemSubtitleFontWeight = $style->item_subtitle_font_weight;
            $style_array->itemDescriptionFontWeight = $style->item_description_font_weight;
            $style_array->itemPriceFontWeight = $style->item_price_font_weight;

            $style_array->itemTitleFontSize = $style->item_title_font_size;
            $style_array->itemSubtitleFontSize = $style->item_subtitle_font_size;
            $style_array->itemDescriptionFontSize = $style->item_description_font_size;
            $style_array->itemPriceFontSize = $style->item_price_font_size;

            $style_array->itemPriceWidth = $style->item_price_width;
        });

        $languages_array = [];

        $restaurant->languages->each(function($language) use(&$languages_array){
            $languages_array[] = $language->language_code;
        });

        $footer_array = [];

        $restaurant->translations->each(function($translation) use(&$footer_array){
            $footer_array[$translation->language_code] = $translation->footer;
        });

        $restaurant_col = $restaurant->categories->transform(function($category) {
            $name_array = [];

            $category->translations->each(function($category_translation) use (&$name_array) {
                $name_array[$category_translation->language_code] = $category_translation->name;
            });

            $subcategories = $category->subcategories->transform(function($subcategory) {
                $sub_name_array = [];

                $subcategory->translations->each(function($subcategory_translation) use (&$sub_name_array) {
                    $sub_name_array[$subcategory_translation->language_code] = $subcategory_translation->name;
                });

                $items = $subcategory->items->transform(function($item) {
                    $item_name_array = [];
                    $item_subtitle_array = [];
                    $item_description_array = [];

                    $item->translations->each(function($item_translation) use (&$item_name_array, &$item_subtitle_array, &$item_description_array) {
                        $item_name_array[$item_translation->language_code] = $item_translation->title;
                        $item_subtitle_array[$item_translation->language_code] = $item_translation->subtitle;
                        $item_description_array[$item_translation->language_code] = $item_translation->description;
                    });


                    $amount = $item->amounts->transform(function($amount) {
                        $amount_title_array = [];

                        $amount->translations->each(function($amount_translation) use (&$amount_title_array) {
                            $amount_title_array[$amount_translation->language_code] = $amount_translation->description;
                        });

                        if(!empty($amount_title_array)) {
                            return[ 'price' => $amount->price,
                                'title' => $amount_title_array];
                        }
                        else {
                            return[ 'price' => $amount->price,];
                        }
                    });

                    $flagForDescriptionNullCheck = false;
                    $flagForSubtitleNullCheck = false;

                    foreach ($item_subtitle_array as $key => $subtitle) {
                        if ($subtitle != "") {
                            $flagForSubtitleNullCheck = true;
                        }
                    }

                    foreach ($item_description_array as $key => $description) {
                        if ($description != "") {
                            $flagForDescriptionNullCheck = true;
                        }
                    }

                    if ($flagForSubtitleNullCheck == false && $flagForDescriptionNullCheck == false) {          // $subtitle is NULL, $description is NULL => dont return them
                        return[ 
                            'title' => $item_name_array,
                            'amount' => $amount,
                            'imageUrl' => $item->image_url
                        ];
                    } else if ($flagForSubtitleNullCheck == true && $flagForDescriptionNullCheck == false) {    // $subtitle is NOT NULL, $description is NULL => return subtitles 
                        return [
                            'title' => $item_name_array,
                            'subtitle' => $item_subtitle_array,
                            'amount' => $amount,
                            'imageUrl' => $item->image_url
                        ];
                    } else if ($flagForSubtitleNullCheck == false && $flagForDescriptionNullCheck == true) {    // $subtitle is NULL, $description is NOT NULL => return descriptions
                        return [
                            'title' => $item_name_array,
                            'description' => $item_description_array,
                            'amount' => $amount,
                            'imageUrl' => $item->image_url
                        ];
                    } else {  
                        return [                                                                                  // $subtitle is NOT NULL, $description is NOT NULL => return subtitles and descriptions
                            'title' => $item_name_array,
                            'subtitles' => $item_subtitle_array,
                            'description' => $item_description_array,
                            'amount' => $amount,
                            'imageUrl' => $item->image_url
                        ];
                    }
                    
                    return[ 'title' => $item_name_array,
                            'amount' => $amount,
                            'imageUrl' => $item->image_url,
                        ];
                });

                return ['name' => $sub_name_array,
                        'items' => $items];
            });

            return collect(
                ['name' => $name_array,
                'subCategories' => $subcategories]
            );
        });

        $json = $data['restaurant'][0] = [
                'name' => $restaurant->translations[0]->name,
                'socials' => $socials_array,
                'style' => $style_array,
                'currency' => $restaurant->currency,
                'languages' => $languages_array,
                'footer_text' => $footer_array,
                'categories' => $restaurant_col
            ];

        return $json;
    }

    public function FileExport($id) {

        $restaurant = Restaurant::with(
            'translations',
            'languages',
            'categories',
            'styles',
            'categories.translations',
            'categories.subcategories',
            'categories.subcategories.translations',
            'categories.subcategories.items',
            'categories.subcategories.items.translations',
            'categories.subcategories.items.amounts',
            'categories.subcategories.items.amounts.translations'
            )->find($id);

        $json = $this->toJson($restaurant);
        return $json;
    }

    public function ExportBySlug($slug) {

        $restaurant = Restaurant::with(
            'translations',
            'languages',
            'categories',
            'styles',
            'categories.translations',
            'categories.subcategories',
            'categories.subcategories.translations',
            'categories.subcategories.items',
            'categories.subcategories.items.translations',
            'categories.subcategories.items.amounts',
            'categories.subcategories.items.amounts.translations'
            )->where('slug', $slug)->firstOrFail();

        $json = $this->toJson($restaurant);
        return $json;
    }
}
