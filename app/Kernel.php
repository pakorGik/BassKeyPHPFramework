<?php
/**
 * Welcome in BassKeyPHP Framework
 * File -Kernel.php initialize structure framework
 */

/**
 * @constance bool DEV_MODE
 * show or hide php errors
 */
define('DEV_MODE', true);

/**
 * @constance bool WHOOPS_DEBUGGER
 * Turn on whoops debugger
 * VERY UNSAFE! | USE ONLY IN TEST MODE ENVIRONMENT!
 */
define('WHOOPS_DEBUGGER', false);

/**
 * include autoload file
 */
include(dirname(__DIR__) . "/vendor/autoload.php");

/**
 * include DevMode.php file if DEV_MODE constance is true
 */
if(DEV_MODE === true) {
    include_once __DIR__ . "/DevMode.php";
}

use BassKey\Core;
use BassKey\Components\Runner\Initiator;

class Kernel
{
    /**
     * function array getRendererConstance
     * function return array with equivalents global variables to global renderer variables
    */
    private function getRendererConstance(): array
    {
        return array(
            "CONFIG_PATH" => "%root_dir%",
            "HOME_PATH" => "%home_path%",
            "BK_PATH" => "%bk_path%",
        );
    }

    public function __construct()
    {
        $core = new Core();

        //add root paths to global variables
        $core->setRootPaths(dirname(__DIR__));

        //add renderer constance to global variables
        $core->setRendererConstance($this->getRendererConstance());

        $config = $core->getConfig();

        $core->firstSettings($config['config']['routing'], $config['base-url']);
        $core->includeBaseFile($config['config']['db']);
        $core->memCacheDeclaration($config['config']['memcache']);
        $core->implementDefaultSystemFunctions();

        $initiator = new Initiator();
        $initiator->runFramework($config['base-url'], $config['config']['routing']);
    }
}

new Kernel();
