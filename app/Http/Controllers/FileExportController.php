<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Style;

class FileExportController extends Controller
{
    public function toJson($restaurant) {
        $socials_array = [];

        $restaurant->networks->each(function($social) use (&$socials_array) {
            $social_name = $social->name;
            $socials_array[$social_name] = $social->link;
        });

        $socials_array = (object)$socials_array;

        // CODE FOR GETTING THE STYLE OBJECT FROM OTHER TABLE
        $selectedStyleId = $restaurant->style_id;
        $styleFromDB = Style::find($selectedStyleId);
        $selectedStyle = new Style;
        
        $selectedStyle->headerImageMaxHeight = $styleFromDB->header_image_max_height;
        $selectedStyle->itemTitleFontFamily = $styleFromDB->item_title_font_family;
        $selectedStyle->itemTitleDisplay = $styleFromDB->item_title_display;
        $selectedStyle->itemSubtitleColor = $styleFromDB->item_subtitle_color;
        $selectedStyle->itemDescriptionColor = $styleFromDB->item_description_color;
        $selectedStyle->itemTitleFontWeight = $styleFromDB->item_title_font_weight;
        $selectedStyle->itemSubtitleFontWeight = $styleFromDB->item_subtitle_font_weight;
        $selectedStyle->itemDescriptionFontWeight = $styleFromDB->item_description_font_weight;
        $selectedStyle->itemPriceFontWeight = $styleFromDB->item_price_font_weight;
        $selectedStyle->itemTitleFontSize = $styleFromDB->item_title_font_size;
        $selectedStyle->itemSubtitleFontSize = $styleFromDB->item_subtitle_font_size;
        $selectedStyle->itemDescriptionFontSize = $styleFromDB->item_description_font_size;
        $selectedStyle->itemPriceFontSize = $styleFromDB->item_price_font_size;
        $selectedStyle->itemPriceWidth = $styleFromDB->item_price_width;

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
                'style' => $selectedStyle,
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
