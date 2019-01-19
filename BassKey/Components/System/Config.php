<?php

namespace BassKey\Components\System;

use BassKey\Components\YmlParser\Controllers\YmlParser;
use BassKey\GlobalVariables;

class Config
{
    private $config = null;

    public function __construct()
    {
        $configPatch = GlobalVariables::getInstance()->get("CONFIG_PATH");
        $ymlParser = new YmlParser();

        if(!file_exists($configPatch."/base_config.yml"))
        {
            return null;
        }

        $ymlParser-> getYmlByPath($configPatch."/base_config.yml");

        $config = $ymlParser->parseYmlToArray(GlobalVariables::getInstance()->get("HOME_PATH"));

        foreach ($config as $element) {

            if(!isset($element['domain']) || $element['domain'] !== $_SERVER['HTTP_HOST'])
            {
                continue;
            }
            $this->config = $element;
        }

        return null;
    }

    /**
     * @return array $config
     */
    public function getConfig()
    {
        return $this->config;
    }

}