<?php

namespace BassKey\Components\System;

use BassKey\System;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

class Doctrine
{
    private $isDevMode = true;
    private $entityManager = null;

    private function createEntityManager($dbParams, $homePath)
    {
        $isDevMode = false;
        $paths = array();
        foreach ($dbParams['entityFiles'] as $index => $file) {
            $paths[$index] = $homePath . $dbParams['entityFiles'][$index];
        }
        unset($dbParams['entityFiles']);
//        System::getInstance()->dump($dbParams);

        $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
        $this->entityManager = EntityManager::create($dbParams, $config);
    }

    public function getEntityManager($dbParams, $homePath)
    {
        if($this->entityManager === null) {
            $this->createEntityManager($dbParams, $homePath);
        }
        return $this->entityManager;
    }


    public function cliDoctrineSettings()
    {
        $entityManager = $this->entityManager;
        return ConsoleRunner::createHelperSet($entityManager);
    }

}