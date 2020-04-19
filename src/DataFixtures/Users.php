<?php

namespace Enigma972\UserBundle\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;


class Users extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $jhon = new User;
        $jhon->setUsername('john')->setEmail('john@gmail.com')->setEnabled(true)
            // password => user
            ->setPassword('$argon2id$v=19$m=65536,t=4,p=1$BhKEH0Z4RC5WipzFIkYt0Q$QykARO4ORc6/oPPCerh5w4C/QvV/w5m73rWFvEJAFLc')
            ;

        $jane = new User;
        $jane->setUsername('jane')->setEmail('jane@gmail.com')->setEnabled(false)
            // password => user
            ->setPassword('$argon2id$v=19$m=65536,t=4,p=1$BhKEH0Z4RC5WipzFIkYt0Q$QykARO4ORc6/oPPCerh5w4C/QvV/w5m73rWFvEJAFLc')
            ;

        $manager->persist($jhon);
        $manager->persist($jane);

        $manager->flush();
    }
}
