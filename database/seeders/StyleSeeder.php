<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class StyleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Creates 14 Style Property Values
        $keys = array('headerImageMaxHeight', 'itemTitleFontFamily', 'itemTitleDisplay', 
                    'itemSubtitleColor', 'itemDescriptionColor', 'itemTitleFontWeight', 'itemSubtitleFontWeight', 
                    'itemDescriptionFontWeight', 'itemPriceFontWeight', 'itemTitleFontSize', 'itemSubtitleFontSize', 
                    'itemDescriptionFontSize', 'itemPriceFontSize', 'itemPriceWidth');

        $values = array('200px', 'Open Sans', 'none', '#e7272d', '#000000', '600', '600', '300',
                        '300', '18px', '14px', '14px', '18px', '70px');

        for ($i = 0; $i < sizeof($keys); $i++) {
            DB::table('styles') -> insert(
                [
                    'key'           => $keys[$i],
                    'value'         => $values[$i],
                    'restaurant_id' => 1
                ]
            );
        }
    }
}
