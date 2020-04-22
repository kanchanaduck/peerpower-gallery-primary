<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Image;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Image::class, function (Faker $faker) {
    return [
        'name' => 'avatar.png',
        'type' => 'image/png',
        'size' => '10485760',
        'user_id' => 1
    ];
});

