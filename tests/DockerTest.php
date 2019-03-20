<?php

namespace Tests;
use PHPUnit\Framework\TestCase;
use App\Service\DockerService;

class DockerTest extends TestCase
{

    /**
     * @var App\Service\DockerService
     */
    private $DockerService;

    public function setUp()
    {
        $this->DockerService = new DockerService;
        parent::setUp();
    }

}
