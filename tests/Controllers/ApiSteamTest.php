<?php
namespace Tests\Controllers;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class ApiSteamTest extends WebTestCase
{
    /**
     * @var Symfony\Bundle\FrameworkBundle\Client
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

        return $this->assertJson(
            $this->client->getResponse()->getContent()
        );
    }

    public function testPost()
    {
        $this->client->request('POST', 'api/steam/563'); // left 4 dead 2

        return $this->assertJson(
            $this->client->getResponse()->getContent()
        );
    }

    public function testDelete()
    {
        $this->client->request('DELETE', 'api/steam/563'); // left 4 dead 2

        return $this->assertJson(
            $this->client->getResponse()->getContent()
        );
    }

    public function urlProvider()
    {
        yield ['api/steam'];
        yield ['api/steam/search?q=anodyne'];
        yield ['api/steam/search?q=' . urlencode('left 4 dead 2')];
    }

}
