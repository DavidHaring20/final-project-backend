<?php

namespace App\Http\Controllers;

use App\Models\Style;
use Illuminate\Http\Request;
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
                'itemPriceWidth' => ['required']
            ],
            [],
            []
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => 'Provjerite ispravnost unosa !'
                ], 400
            );
        };

        $data = $validator -> valid();

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
                    'item_price_width' => $data['itemPriceWidth']
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
    
    public function destroy($id) {
        try {
            // CHECK IF THERE IS STYLE WITH THAT ID
            $foundStyle = Style::find($id);
            
            if (!$foundStyle) {
                return response() -> json([
                    'message' => 'The style wasn\'t found'
                ]);
            };

            $deleteStyle = Style::destroy($id);

            return response() -> json([
                'data' => [
                    'message' => 'Style successfully deleted.',
                    'deletedStyle' => $deleteStyle
                ]
            ]);
        } catch (Throwable $e) {
            report($e);
        }
    }

    public function update(Request $request, $id) {
        // CHECK IF THERE IS STYLE WITH THAT ID
        $foundStyle = Style::find($id);
            
        if (!$foundStyle) {
            return response() -> json([
                'message' => 'The style wasn\'t found'
            ]);
        };

        // CHECK VALIDITY OF DATA
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
                'itemPriceWidth' => ['required']
            ],
            [],
            []
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => 'Something is wrong with the data entered !'
                ], 400
            );
        };

        $data = $validator -> valid();
        
        $foundStyle['header_image_max_height'] = $data['headerImageMaxHeight'];
        $foundStyle['item_title_font_family'] = $data['itemTitleFontFamily'];
        $foundStyle['item_title_display'] = $data['itemTitleDisplay'];
        $foundStyle['item_subtitle_color'] = $data['itemSubtitleColor'];
        $foundStyle['item_description_color'] = $data['itemDescriptionColor'];
        $foundStyle['item_title_font_weight'] = $data['itemTitleFontWeight'];
        $foundStyle['item_subtitle_font_weight'] = $data['itemSubtitleFontWeight'];
        $foundStyle['item_description_font_weight'] = $data['itemDescriptionFontWeight'];
        $foundStyle['item_price_font_weight'] = $data['itemPriceFontWeight'];
        $foundStyle['item_title_font_size'] = $data['itemTitleFontSize'];
        $foundStyle['item_subtitle_font_size'] = $data['itemSubtitleFontSize'];
        $foundStyle['item_description_font_size'] = $data['itemDescriptionFontSize'];
        $foundStyle['item_price_font_size'] = $data['itemPriceFontSize'];
        $foundStyle['item_price_width'] = $data['itemPriceWidth'];

        $foundStyle->save();

        return response() -> json([
            'message' => 'Data successfully updated.',
            'dataUpdated' => $foundStyle
        ]);
    }
}
