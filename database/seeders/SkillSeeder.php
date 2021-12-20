<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Skill;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // use the factory to create a Faker\Generator instance
        $faker = \Faker\Factory::create();

        // create 100 skills
        for ($i = 0; $i < 100; $i++) {
            Skill::create([
                'category_id' => $faker->numberBetween(1, 10),
                'name' => $faker->word,
            ]);
        }
    }
}
