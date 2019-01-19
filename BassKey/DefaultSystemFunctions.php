<?php

namespace BassKey;

use BassKey\Components\Database\Controllers\DB;
use BassKey\Components\System\Doctrine;
use BassKey\Components\System\MemCache;

class DefaultSystemFunctions
{
    public function __construct()
    {
        $system = System::getInstance();

        /**
         * Method: dump
         * Used to: debug code
         */
        $system->addMethod("dump", function ($errors) {
            foreach ($errors as $data)
            {
                echo '<pre>' . @var_export($data, true) . '</pre>';
            }
        });

        /**
         * Method: getDoctrine
         * Used to: return doctrine manager object
         */
        $system->addMethod("getDoctrine", function () {

            //check configuration
            if(GlobalVariables::getInstance()->get("DB_CONFIG") === null
                || !array_key_exists('doctrine', GlobalVariables::getInstance()->get("DB_CONFIG"))) {
                return null;
            }

            //create new doctrine manager object
            $doctrineSys = new Doctrine();
            $manager = $doctrineSys->getEntityManager(
                GlobalVariables::getInstance()->get("DB_CONFIG")['doctrine'],
                GlobalVariables::getInstance()->get("HOME_PATH")
            );

            return $manager;
        });

        /**
         * Method: getDb
         * Used to: return DB object, to manage Database
         */
        $system->addMethod("getDb", function () {
            return new DB(GlobalVariables::getInstance()->get("DB_CONFIG")['db']);
        });

        /**
         * Method: getMC
         * Used to: return MemCache object, to manage Memcache
         */
        $system->addMethod("getMC", function () {
            $memCache = new MemCache();
            $memCache->addServers(GlobalVariables::getInstance()->get("MC_CONFIG")['default']['servers']);
            return $memCache;
        });
    }
}
