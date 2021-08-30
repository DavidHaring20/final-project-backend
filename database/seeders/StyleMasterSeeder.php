<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class StyleMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $keys = array('headerImageMaxHeight', 'itemTitleFontFamily', 'itemTitleDisplay', 
                    'itemSubtitleColor', 'itemDescriptionColor', 'itemTitleFontWeight', 'itemSubtitleFontWeight', 
                    'itemDescriptionFontWeight', 'itemPriceFontWeight', 'itemTitleFontSize', 'itemSubtitleFontSize', 
                    'itemDescriptionFontSize', 'itemPriceFontSize', 'itemPriceWidth');

        $values = array('200px', 'Open Sans', 'none', '#e7272d', '#000000', '600', '600', '300',
                        '300', '18px', '14px', '14px', '18px', '70px');

        for ($i = 0; $i < sizeof($keys); $i++) {
            DB::table('style_default_property_values') -> insert(
                [
                    'key'   => $keys[$i],
                    'value' => $values[$i]
                ]
            );
        }
    }
}
