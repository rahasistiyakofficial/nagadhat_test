<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KeywordsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        foreach (range(1, 10) as $index) {
            DB::table('keywords')->insert([
                'keyword' => $faker->word,

            ]);
        }
        // DB::table('keywords')->insert([
        //     'keyword' => 'Keyword1',
        // ]);

        // DB::table('keywords')->insert([
        //     'keyword' => 'Keyword2',
        // ]);

    }
}
