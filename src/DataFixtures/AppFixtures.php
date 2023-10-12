<?php

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Restaurant;
use App\Entity\Review;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    const NB_CITY = 10;
    const NB_RESTAURANT = 100;
    const NB_REVIEW = 20;

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        for ($i = 0; $i < self::NB_CITY; $i++) {
            $city = new City();
            $city->setName($faker->city());
            $city->setZipCode($faker->postcode());
            
            $manager->persist($city);
            $cities[] = $city;
        }

        for($i = 0; $i < self::NB_RESTAURANT; $i++){
            $restaurant = new Restaurant();
            $restaurant->setName($faker->company())
            ->setDescription($faker->text())
            ->setAddress($faker->streetAddress())
            ->setCity($faker->randomElement($cities));
            $manager->persist($restaurant);
            $restaurants[] = $restaurant;
        }

        for ($i = 0; $i < self::NB_REVIEW; $i++) {
            $review = new Review();
            $review->setComment($faker->text())
            ->setRate($faker->numberBetween(1, 5))
            ->setPostedDate($faker->dateTime())
            ->setRestaurant($faker->randomElement($restaurants));
            $manager->persist($review);
            $reviews[] = $review;
        }

        $manager->flush();
    }
}
