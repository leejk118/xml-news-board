<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Article;
use Faker\Generator as Faker;

$factory->define(Article::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
        'content' => $faker->sentence,
        'send_date' => $faker->date,
        'news_link' => 'http://news.example.com',
        'preview_content' => $faker->sentence,
        'view_count' => 0
    ];
});
