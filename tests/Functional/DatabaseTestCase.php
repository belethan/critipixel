<?php

namespace App\Tests\Functional;

use Doctrine\ORM\EntityManagerInterface;

abstract class DatabaseTestCase extends FunctionalTestCase
{
    protected function getEntityManager(): EntityManagerInterface
    {
        return static::getContainer()->get('doctrine')->getManager();
    }
}
