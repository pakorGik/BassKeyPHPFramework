<?php

namespace BassKey;

use BassKey\Components\Database\Controllers\DB;
use BassKey\Components\System\Doctrine;
use BassKey\Components\System\MemCache;
use BassKey\Engine\Engine;

/**
 * @method void dump(...$errors)
 * @method Doctrine getDoctrine()
 * @method DB getDb()
 * @method MemCache getMC()
 */
class System extends Engine
{
    private static $instance;

    private function __construct()
    {
        parent::__construct();
    }

    public static function getInstance()
    {
        if(self::$instance === null)
        {
            self::$instance = new System();
        }

        return self::$instance;
    }
}
