<?php

namespace Tests\Controllers;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Controller\DockerController;
use Symfony\Component\HttpFoundation\Request;

class SteamTest extends WebTestCase
{
    /**
     * @var Client
     */
    private $client;

    public function setUp()
    {
        $this->client = self::createClient();

        parent::setUp();
    }

    /**
     * @dataProvider urlProvider
     */
    public function testResponses(string $url)
    {
        $this->client->request('GET', $url);
        echo htmlentities((string)$results);
        return $this->assertJson(
            $this->client->getResponse()->getContent()
        );
    }

    public function urlProvider()
    {
        yield ['steam/add-by-id?id=100'];
        ## yield ['steam/remove-by-id?id=100'];
    }


}
