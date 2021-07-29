<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use Illuminate\Database\Seeder;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $restaurant = Restaurant::updateOrCreate([
            'position' => 1,
            'currency' => 'kn',
            'slug' => 'test-restaurant'
            ]
        );

        $restaurant_names = [
            'hr' => 'Test Restaurant',
            'en' => 'Test Restaurant en',
            'it' => 'Test Restaurant it',
            'de' => 'Test Restaurant de'
        ];

        $footers = [
            'hr' => 'U cijenu su uračunati couvert, usluga i porez.\nKnjiga žalbe nalazi se na šanku.\n\nZABRANJENA JE PRODAJA, TOČENJE I KONZUMIRANJE ALKOHOLNIH PIĆA OSOBAMA MLAĐIM OD 18 GODINA!\n\nRestoran Grill Lovorka\nRujevica 6, 51000 Rijeka\n+385 51 260244\ninfo@grill-lovorka.hr',
            'en' => 'The price includes the couvert, service and tax.\nThe Complaints Book is at the bar.\n\nTHE SALE, DISPENSING AND CONSUMPTION OF ALCOHOL TO PERSONS YOUNGER THAN 18 YEARS OF AGE IS PROHIBITED.\n\nRestoran Grill Lovorka\nRujevica 6, 51000 Rijeka\n+385 51 260244\ninfo@grill-lovorka.hr',
            'it' => 'I prezzi includono il coperto, il servizio e l’IVA croata.\nIl Libro dei reclami si trova al banco del bar.\n\nÈ VIETATA LA CONSUMAZIONE, LA SOMMINISTRAZIONE E LA VENDITA DI BEVANDE ALCOLICHE AI MINORI DI 18 ANNI.\n\nRestoran Grill Lovorka\nRujevica 6, 51000 Rijeka\n+385 51 260244\ninfo@grill-lovorka.hr',
            'de' => 'Im Preis sind Couvert, Service und Steuern enthalten.\nDas Beschwerdebuch ist an der Theke zu finden.\n\nDER VERKAUF, DIE AUSGABE UND DER KONSUM VON ALKOHOLISCHEN GETRÄNKEN AN PERSONEN UNTER 18 JAHREN IST VERBOTEN.\n\nRestoran Grill Lovorka\nRujevica 6, 51000 Rijeka\n+385 51 260244\ninfo@grill-lovorka.hr',
        ];

        $category_names = [
            [
                'hr' => 'Hrana',
                'en' => 'Food',
                'it' => 'Cibo',
                'de' => 'Lebensmittel'
            ],
            [
                'hr' => 'Pića',
                'en' => 'Drinks',
                'it' => 'Bevande',
                'de' => 'Getranke'
            ]
        ];

        $subcategory_names = [
            [
                'hr' => 'Prva',
                'en' => 'First',
                'it' => 'Primo',
                'de' => 'Zuerst'
            ],
            [
                'hr' => 'Druga',
                'en' => 'Second',
                'it' => 'Altro',
                'de' => 'Andere'
            ],
        ];

        $item_titles = [
            [
                'titles' => [
                    'hr' => 'Prvi',
                    'en' => 'First',
                    'it' => 'Primo',
                    'de' => 'Zuerst'
                ],
                'descriptions' => [
                    'hr' => 'Prvi opis',
                    'en' => '',
                    'it' => 'Primo',
                    'de' => 'Zuerst'
                ],
            ],
            [
                'titles' => [
                    'hr' => 'Drugi',
                    'en' => 'Second',
                    'it' => 'Altro',
                    'de' => 'Andere'
                ],
                'descriptions' => [
                    'hr' => '',
                    'en' => '',
                    'it' => '',
                    'de' => 'Andere'
                ],
            ],
        ];

        $languages = \App\Models\Language::all();
        $restaurant->languages()->sync($languages);

        foreach($languages as $language) {
            $restaurant->translations()->create([
                'language_code' => $language['language_code'],
                'is_default' => false,
                'name' => $restaurant_names[$language['language_code']],
                'footer' => $footers[$language['language_code']]
            ]);
        }

        foreach($category_names as $category) {
            $newCategory = $restaurant->categories()->create([
                'position' => 1
            ]);

            foreach($category as $language_code => $name) {
                $newCategory->translations()->create([
                    'language_code' => $language_code,
                    'is_default' => false,
                    'name' => $name
                ]);
            }

            foreach($subcategory_names as $subcategory) {
                $newSubcategory = $newCategory->subcategories()->create([
                    'position' => 1
                ]);

                foreach($subcategory as $language_code => $name) {
                    $newSubcategory->translations()->create([
                        'language_code' => $language_code,
                        'is_default' => false,
                        'name' => $name
                    ]);
                }

                foreach($item_titles as $item) {
                    $newItem = $newSubcategory->items()->create([
                        'image_url' => '',
                        'position' => 1
                    ]);

                    foreach($item["titles"] as $language_code => $title) {
                        $newItem->translations()->create([
                            'language_code' => $language_code,
                            'is_default' => false,
                            'title' => $title,
                            'description' => ''
                        ]);
                    }

                    $newItem->amounts()->create([
                        'position' => 1,
                        'price' => 18
                    ]);
                }
            }
        }
    }
}
