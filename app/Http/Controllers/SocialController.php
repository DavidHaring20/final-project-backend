<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Social;
use App\Models\Style;
use Illuminate\Http\Request;
use Validator;
use Error;

class SocialController extends Controller
{
    public function show($restaurant_id) {
        // GET RESTAURANT BY ID
        $restaurant = Restaurant::findOrFail($restaurant_id);

        // GET social_id FROM THAT RESTAURANT
        if ($restaurant -> social == "" || $restaurant -> social == null) {
            return response() -> json(
                [
                    'message' => 'Can\'t find Social by ID.'
                ]
            );
        }

        $social_id = $restaurant -> social['id'];
        
        // GET SOCIAL BY ID 
        $social = Social::findOrFail($social_id);

        // RETURN JSON AND SOCIAL
        return response() -> json(
            [
                'message' => 'Social found.',
                'social' => $social                
            ]
        );
    }

    public function store($restaurant_id) {
        // GET RESTAURANT WITH restaurant_id
        $restaurant = Restaurant::findOrFail($restaurant_id);

        // IF SOCIAL DOESN'T EXIST, THEN CREATE IT AND ADD restaurant_id TO IT  
        if ($restaurant -> social == null || $restaurant -> social == "") {
            try {
                Social::create(
                    [
                        'facebook_url' => '',
                        'foursquare_url' => '',
                        'google_url' => '',
                        'instagram_url' => '',
                        'tripadvisor_url' => '',
                        'twitter_url' => '',
                        'restaurant_id' => $restaurant_id
                    ]
                ); 

                $newestSocialId = Social::max('id');
                $newestSocial = Social::find($newestSocialId);

                return response() -> json(
                    [
                        'message' => 'Created new Social',
                        'newSocial' => $newestSocial
                    ]
                );
            } catch (Error $error) {
                print_r($error->getMessage()); 
            }
        } else {
            return response() -> json(
                [
                    'message' => 'There is already social for that resturant.'
                ]
            );
        }
    }

    public function update(Request $request, $restaurant_id) {
        // VALIDATE DATA
        $validator = Validator::make($request -> all(), [
            'facebookUrl' => 'max:255',
            'foursquareUrl' => 'max:255',
            'googleUrl' => 'max:255',
            'instagramUrl' => 'max:255',
            'tripadvisorUrl' => 'max:255',
            'twitterUrl' => 'max:255'
            
        ],
        [],
        []);

        if ($validator -> fails()) {
            return response() -> json(
                [
                    'message' => 'Please check data.'
                ]
            );
        }

        $data = $validator -> valid();
        
        // GET RESTAURANT BY ID
        $restaurant = Restaurant::findOrFail($restaurant_id);

        // GET social_id FROM THAT RESTAURANT
        $social_id = $restaurant -> social['id']; 
        
        // UPDATE SOCIAL
        $social = Social::findOrFail($social_id);

        $social['facebook_url'] = $data['facebookUrl'];
        $social['foursquare_url'] = $data['foursquareUrl'];
        $social['google_url'] = $data['googleUrl'];
        $social['instagram_url'] = $data['instagramUrl'];
        $social['tripadvisor_url'] = $data['tripadvisorUrl'];
        $social['twitter_url'] = $data['twitterUrl'];

        $social -> save();

        // RETURN JSON AND SOCIAL
        return response() -> json(
            [
                'message' => 'Success !',
                'updatedSocial' => $social
            ]
        );
    }
}