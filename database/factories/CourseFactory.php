<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Course;
use Faker\Generator as Faker;

$factory->define(Course::class, function (Faker $faker) {

    $courses = json_decode(file_get_contents('data/courses.json',true),true);

    Course::whereIn('name', $courses)->delete();

    return [
        'name' => $faker->unique()->randomElement($courses),
        'lecturer' => $faker->name,
        'department' => $faker->randomElement(['Computer Science', 'Accounting','Elect Elect','Business Administration']),
    ];
});
