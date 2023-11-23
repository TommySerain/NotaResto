<?php

namespace App\Services;

use App\Entity\City;
use App\Entity\User;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(private EntityManagerInterface $entityManager, private UserPasswordHasherInterface $userPasswordHasher, private CityRepository $cityRepository)
        {
        }
    public function createUser
        (  string $email,
           string $roles,
           string $password,
           string $firstname,
           string $lastname,
           string $address,
           City $city,
        ):User
    {
//        $city=$this->cityRepository->findOneBy(['name'=>$city_name]);
        $user = new User();
        $user->setEmail($email);
        $user->setRoles([$roles]);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $password) );
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setAddress($address);
        $user->setCity($city);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;

    }
}