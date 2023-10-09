<?php

namespace Database\Seeders;

use App\Models\Keyword;
use Illuminate\Database\Seeder;
use App\Models\UserSearchHistory;

class UserSearchHistorySeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();

        $keywords = Keyword::all();

        foreach (range(1, 50) as $index) {
            $randomKeyword = $faker->randomElement($keywords);
            UserSearchHistory::create([
                'user_id' => $faker->numberBetween(1, 10),
                'search_keyword' => $randomKeyword->keyword,
                'search_keyword_id'=>$randomKeyword->id,
                'search_time' => $faker->dateTimeBetween('-1 year', 'now'),
                'search_results' => $faker->text,
            ]);
        }
    }
}
