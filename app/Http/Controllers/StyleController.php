<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Style;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Throwable;
use Validator;

class StyleController extends Controller
{
    // GET STYLES
    public function index() {
        $styles = Style::all();

        if ($styles == null || $styles == '[]') {
            return response(
                [
                    'message' => 'Nažalost, ne postoji takav stil. Molim pokušaj ponovo.'
                ], 404
            );
        }

        return response() -> json(
            [
                'message' => 'Uspješno pronađeni stilovi !',
                'data' => [
                    'styles' => $styles
                ]
            ], 200
        );
    }

    // GET STYLE
    public function show($id) {
        $styleById = Style::findOrFail($id);

        return response() -> json(
            [
                'message' => 'Stil je pronađen !',
                'data' => [
                    'style' => $styleById
                ]
            ],200
        );
    }

    // CREATE NEW STYLE
    public function store(Request $request) {
        $validator = Validator::make($request->all(), 
            [
                'headerImageMaxHeight' => ['required'],
                'itemTitleFontFamily' => ['required'],
                'itemTitleDisplay' => ['required'],
                'itemSubtitleColor' => ['required'], 
                'itemDescriptionColor' => ['required'],
                'itemTitleFontWeight' => ['required'],
                'itemSubtitleFontWeight' => ['required'],
                'itemDescriptionFontWeight' => ['required'],
                'itemPriceFontWeight' => ['required'],
                'itemTitleFontSize' => ['required'],
                'itemSubtitleFontSize' => ['required'],
                'itemDescriptionFontSize' => ['required'],
                'itemPriceFontSize' => ['required'],
                'itemPriceWidth' => ['required'],
                'restaurantId' => ['required']
            ],
            [],
            []
        );

        if ($validator->fails()) {
            return response()->jsonFail(
                [
                    'message' => 'Provjerite ispravnost unosa !'
                ], 400
            );
        };

        $data = $validator -> valid();

        // CHECK FOR RESTAURANT ID
        $restaurantId = $data['restaurantId'];
        $restaurantExists = Restaurant::find($restaurantId);
        
        if (!$restaurantExists) {
            return response() -> json(
                [
                    'message' => 'Odabrani restoran nije pronađen.'
                ], 404
            );
        }

        try {
            Style::create(
                [
                    'header_image_max_height' => $data['headerImageMaxHeight'],
                    'item_title_font_family' => $data['itemTitleFontFamily'],
                    'item_title_display' => $data['itemTitleDisplay'],
                    'item_subtitle_color' => $data['itemSubtitleColor'], 
                    'item_description_color' => $data['itemDescriptionColor'],
                    'item_title_font_weight' => $data['itemTitleFontWeight'],
                    'item_subtitle_font_weight' => $data['itemSubtitleFontWeight'],
                    'item_description_font_weight' => $data['itemDescriptionFontWeight'],
                    'item_price_font_weight' => $data['itemPriceFontWeight'],
                    'item_title_font_size' => $data['itemTitleFontSize'],
                    'item_subtitle_font_size' => $data['itemSubtitleFontSize'],
                    'item_description_font_size' => $data['itemDescriptionFontSize'],
                    'item_price_font_size' => $data['itemPriceFontSize'],
                    'item_price_width' => $data['itemPriceWidth'],
                    'restaurant_id' => $data['restaurantId']
                ]
            ); 
        } catch (Throwable $e) {
            return response() -> json(
                [
                    'errorMessage' => $e,
                    'message' => 'Došlo je do pogreške !' 
                ], 500
            );
        };
        $idOfLastStyle = Style::max('id');
        $newStyle = Style::find($idOfLastStyle);

        return response() -> json(
            [
                'message' => 'Stil uspješno dodan.',
                'data' => [
                    'newStyle' => $newStyle
                ]
            ], 200
        );
    }
}
