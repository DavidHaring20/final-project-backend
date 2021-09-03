<?php

namespace Database\Factories;

use App\Models\Social;
use Illuminate\Database\Eloquent\Factories\Factory;

class SocialFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Social::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $randomName = 'name-text';
        return [
            
            'tripadvisor_url' => $randomName."/tripadvisor.com",
            'facebook_url' => $randomName."/facebook.com",
            'foursquare_url' => $randomName."/foursquare.com",
            'google_url' => $randomName."/google.com",
            'twitter_url' => $randomName."/twitter.com",
            'instagram_url' => $randomName."/instagram.com",
            'restaurant_id' => 1
        ];
    }
}
