<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Service;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $service = new Service();
        $service->setName("Steam");
        $manager->persist($service);

        $manager->flush();
    }
}
