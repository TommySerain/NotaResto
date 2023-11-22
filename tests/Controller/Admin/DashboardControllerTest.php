<?php

namespace App\Tests\Controller\Admin;

use App\Controller\Admin\DashboardController;
use App\Repository\UserRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DashboardControllerTest extends WebTestCase
{
    private $client;
    private $clientAdmin;
    private $clientUser;
    private $clientRestorer;


    public function testIndex()
    {
        $this->client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $this->clientAdmin =  $userRepository->findOneByEmail('admin@test.fr');
        $this->clientUser =  $userRepository->findOneByEmail('regular1@test.fr');
        $this->clientRestorer =  $userRepository->findOneByEmail('resto1@test.fr');

        $this->client->loginUser($this->clientAdmin);
        $this->client->request('GET', '/admin');
        $this->assertResponseRedirects('http://localhost/admin?crudAction=index&crudControllerFqcn=App%5CController%5CAdmin%5CUserCrudController');

        $this->client->loginUser($this->clientUser);
        $this->client->request('GET', '/admin');
        $this->assertResponseStatusCodeSame(403);

        $this->client->loginUser($this->clientRestorer);
        $this->client->request('GET', '/admin');
        $this->assertResponseStatusCodeSame(403);
    }
}
