<?php

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Restaurant;
use App\Entity\Review;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hash) {

    }
    const NB_CITY = 10;
    const NB_RESTAURANT = 100;
    const NB_REVIEW = 20;
    const NB_USER_RESTORER = 20;
    const NB_USER_REGULAR = 20;

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        for ($i = 0; $i < self::NB_CITY; $i++) {
            $city = new City();
            $city->setName($faker->city())
            ->setZipCode($faker->postcode());
            
            $manager->persist($city);
            $cities[] = $city;
        }

        $userAdmin = new User();
        $userAdmin->setEmail("admin@test.fr")
        ->setPassword($this->hash->hashPassword($userAdmin, "test1234"))
        ->setFirstname($faker->firstName())
        ->setLastname($faker->lastName())
        ->setAddress($faker->address())
        ->setCity($faker->randomElement($cities))
        ->setRoles(['ROLE_ADMIN'])
        ;
        $manager->persist($userAdmin);
        $users[]= $userAdmin;
        for ($i=0; $i < self::NB_USER_RESTORER; $i++) { 
            $userRestorer = new User();
            $userRestorer->setEmail("resto$i@test.fr")
            ->setPassword($this->hash->hashPassword($userRestorer, "test1234"))
            ->setFirstname($faker->firstName())
            ->setLastname($faker->lastName())
            ->setAddress($faker->address())
            ->setCity($faker->randomElement($cities))
            ->setRoles(['ROLE_RESTORER'])
            ;
            $manager->persist($userRestorer);
            $users[]=$userRestorer;
        }
        for ($i=0; $i < self::NB_USER_RESTORER; $i++) { 
            $userRegular = new User();
            $userRegular->setEmail("regular$i@test.fr")
            ->setPassword($this->hash->hashPassword($userRegular, "test1234"))
            ->setFirstname($faker->firstName())
            ->setLastname($faker->lastName())
            ->setAddress($faker->address())
            ->setCity($faker->randomElement($cities))
            ->setRoles(['ROLE_USER'])
            ;

            $manager->persist($userRegular);
            $users[]=$userRegular;
        }


        for($i = 0; $i < self::NB_RESTAURANT; $i++){
            $restaurant = new Restaurant();
            $restaurant->setName($faker->company())
            ->setDescription($faker->text())
            ->setAddress($faker->streetAddress())
            ->setCity($faker->randomElement($cities))
            ->setUser($faker->randomElement($users))
            ;
            $manager->persist($restaurant);
            $restaurants[] = $restaurant;
        }

        for ($i = 0; $i < self::NB_REVIEW; $i++) {
            $review = new Review();
            $review->setComment($faker->text())
            ->setRate($faker->numberBetween(1, 5))
            ->setPostedDate($faker->dateTime())
            ->setUser($faker->randomElement($users))
            ->setRestaurant($faker->randomElement($restaurants));
            $manager->persist($review);
            $reviews[] = $review;
        }

        $manager->flush();
    }
}
