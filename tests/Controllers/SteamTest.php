<?php
namespace Tests\Controllers;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
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

        return $this->assertJson(
            $this->client->getResponse()->getContent()
        );
    }

    public function urlProvider()
    {
        yield ['steam'];
    }

}
