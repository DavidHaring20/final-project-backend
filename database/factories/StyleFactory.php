<?php

namespace Database\Factories;

use App\Models\Style;
use Illuminate\Database\Eloquent\Factories\Factory;

class StyleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Style::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // $heights = array('50px', '100px', '150px','200px', '400px');
        // $fonts = array('Arial', 'Verdana', 'Heretica', 'Georgia', 'Garamond', 'Courier New', 'Tahoma');
        // $displays = array('inline', 'block', 'none', 'inherit');
        // $colors = array('red', 'blue', 'green', 'brown', 'blueviolet', 'coral', 'darkviolet', 'deeppink');
        // $weights = array('200', '300', '400', '500', '600', '700');
        // $fontSizes = array('12px', '14px', '16px', '18px', '20px', '22px', '24px');
        // return [
        //     'header_image_max_height' => $this->faker->randomElement($heights),
        //     'item_title_font_family' => $this->faker->randomElement($fonts),
        //     'item_title_display' => $this->faker->randomElement($displays),
        //     'item_subtitle_color' => $this->faker->randomElement($colors), 
        //     'item_description_color' => $this->faker->randomElement($colors),
        //     'item_title_font_weight' => $this->faker->randomElement($weights),
        //     'item_subtitle_font_weight' => $this->faker->randomElement($weights),
        //     'item_description_font_weight' => $this->faker->randomElement($weights),
        //     'item_price_font_weight' => $this->faker->randomElement($weights),
        //     'item_title_font_size' => $this->faker->randomElement($fontSizes),
        //     'item_subtitle_font_size' => $this->faker->randomElement($fontSizes),
        //     'item_description_font_size' => $this->faker->randomElement($fontSizes),
        //     'item_price_font_size' => $this->faker->randomElement($fontSizes),
        //     'item_price_width' => $this->faker->randomElement($fontSizes)
        // ];
        return [
            'header_image_max_height' => '200px',          
            'item_title_font_family' => 'Open Sans',             
            'item_title_display' => 'none',
            'item_subtitle_color' => '#e7272d',               
            'item_description_color' => '#000000',               
            'item_title_font_weight' => '600',         
            'item_subtitle_font_weight' => '600',          
            'item_description_font_weight' => '300',           
            'item_price_font_weight' => '300',    
            'item_title_font_size' => '18px',
            'item_subtitle_font_size' => '14px',
            'item_description_font_size' => '14px',
            'item_price_font_size' => '18px',
            'item_price_width' => '70px'  
        ];
    }
}
