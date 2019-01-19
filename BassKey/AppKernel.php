<?php

namespace BassKey;

use BassKey\Components\System\Controller;

class AppKernel
{
    private static $ObjectsBundles = array();
    
    public static function GetObjectsBundles()
    {
        return self::$ObjectsBundles;
    }
    
    public static function SetObjectsBundles($name, Controller $obj)
    {
        if (!array_key_exists($name, self::$ObjectsBundles))
        {
            self::$ObjectsBundles[$name] = $obj;
        }
    }
}
