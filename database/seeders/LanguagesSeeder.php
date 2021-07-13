<?php

namespace Database\Seeders;

use App;
use Illuminate\Database\Seeder;
use App\Models\Language;

class LanguagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $languages = [
            [
                'language_code' => 'hr',
                'language_name' => 'Hrvatski'
            ],
            [
                'language_code' => 'en',
                'language_name' => 'Engleski'
            ],
            [
                'language_code' => 'it',
                'language_name' => 'Talijanski'
            ],
            [
                'language_code' => 'de',
                'language_name' => 'Njemački'
            ]
        ];

        foreach($languages as $language) {
            // dd($language);
            Language::updateOrCreate(
                ['language_code' => $language['language_code']],
                $language,
            );
        }
    }
}
