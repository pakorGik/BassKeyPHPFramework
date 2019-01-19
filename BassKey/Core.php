<?php

namespace BassKey;

use BassKey\Components\System\Config;

/**
 * Class Core
 * @package BassKey
 *
 * Global defaults variables list:
 *  HOME_PATH - server path to home project directory
 *  CONFIG_PATH - server path to app/config directory
 *  BK_PATH - server path to BassKey directory
 *  ROUTING_CONFIG - configuration of routing
 *  RENDER_CONSTANCE - constance list to changes in config files
 *  HOME_URL - url to home page
 *  ASSETS_URL - url to assets directory
 *  DB_CONFIG - database configuration
 *  MC_CONFIG - memcache configuration
 */

class Core
{
    public function setRootPaths($projectPath)
    {
        GlobalVariables::getInstance()->add("HOME_PATH", $projectPath);
        GlobalVariables::getInstance()->add("CONFIG_PATH", $projectPath. "/app/Config");
        GlobalVariables::getInstance()->add("BK_PATH", $projectPath. "/BassKey");
    }

    public function setRendererConstance($constanceArray)
    {
        $constanceReady = array();

        foreach ($constanceArray as $constanceName => $renderElement)
        {
            $constanceReady[$renderElement] = GlobalVariables::getInstance()->get($constanceName)."/";
        }

        GlobalVariables::getInstance()->add("RENDER_CONSTANCE", $constanceReady);
    }

    public function getConfig()
    {
        return (new Config())->getConfig();
    }

    public function firstSettings($routingConfig, $baseUrl)
	{
        GlobalVariables::getInstance()->add("ROUTING_CONFIG", $routingConfig);

        if (!isset($baseUrl) || empty($baseUrl) )
        {
            return false;
        }

        GlobalVariables::getInstance()->add("ASSETS_URL", "//" . "$baseUrl" . "assets");

        GlobalVariables::getInstance()->add("HOME_URL", "//" . $baseUrl);

        return true;
	}

    public function includeBaseFile($dbConfig)
	{
        GlobalVariables::getInstance()->add("DB_CONFIG", $dbConfig);
	}

    public function memCacheDeclaration($mcConfig)
    {
        GlobalVariables::getInstance()->add("MC_CONFIG", $mcConfig);
    }

    public function implementDefaultSystemFunctions()
    {
        (new DefaultSystemFunctions());
    }
}
