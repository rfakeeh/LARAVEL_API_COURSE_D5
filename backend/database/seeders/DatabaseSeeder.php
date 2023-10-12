<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\News;
use App\Models\Image;
use App\Models\CategoryNews;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory(5)->create();
        Category::factory(10)->create();
        News::factory(20)->create();
        Image::factory(20)->create();
        CategoryNews::factory(50)->create();
    }
}
