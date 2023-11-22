<?php

namespace App\Tests\Controller;

use App\Controller\RestaurantController;
use App\Form\RestaurantType;
use App\Repository\RestaurantRepository;
use App\Repository\UserRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RestaurantControllerTest extends WebTestCase
{

    private $client;
    private $clientAdmin;
    private $clientUser;
    private $clientRestorer;
    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $this->clientAdmin =  $userRepository->findOneByEmail('admin@test.fr');
        $this->clientUser =  $userRepository->findOneByEmail('regular1@test.fr');
        $this->clientRestorer =  $userRepository->findOneByEmail('resto1@test.fr');
    }

    public function testEdit()
    {
        $this->client->loginUser($this->clientRestorer);
        $crawler = $this->client->request('GET', '/restaurant/52/edit');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

//        $this->client->loginUser($this->clientUser);
//        $crawler = $this->client->request('GET', '/restaurant/52/edit');
//        $this->assertResponseStatusCodeSame(307);

//        $this->assertSelectorTextContains('h1', 'Edit Restaurant');
//
//        $this->client->submitForm('Update', ['restaurant[description]' => "Test de changement de description"]);
//
//        $this->assertResponseStatusCodeSame(200);

    }
//
//    public function testDelete()
//    {
//
//
//    }
//
    public function testGetRestaurantsByCurrentUser()
    {
        $this->client->loginUser($this->clientRestorer);
        $crawler = $this->client->request('GET', '/restaurant/MyRestaurants');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->client->loginUser($this->clientUser);
        $crawler = $this->client->request('GET', '/restaurant/MyRestaurants');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());

    }
//
    public function testGetRestaurantsByZipCode()
    {
        $crawler = $this->client->request('GET', '/');
        $form = $crawler->selectButton('Search')->form();
        $form['zipcode'] = '41418';
        $this->client->submit($form);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

//        $form['zipcode'] = '17';
//        $this->client->submit($form);
//        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

    }
//
//    public function testNew()
//    {
//
//    }
//
//    public function testGetAllRestaurants()
//    {
//
//    }

    public function testShowOne()
    {
        $this->client->loginUser($this->clientAdmin);
        $crawler = $this->client->request('GET', '/restaurant/details/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}
