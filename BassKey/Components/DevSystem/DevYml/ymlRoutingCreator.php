<?php

namespace BassKey\Components\DevSystem\DevYml;

use BassKey\GlobalVariables;
use BassKey\System;
use BassKey\Components\YmlParser\Controllers\YmlParser;
use BassKey\Components\DevSystem\DevBundleCreator\Controllers\MakesFile;

class ymlRoutingCreator
{
    public function saveRoutingYmlFile($routing, $fileLocation, $fileName)
    {
        $ymlParser = new YmlParser();
        $file = $ymlParser->ymlDump($routing);
        $fileLocation = GlobalVariables::getInstance()->get("HOME_PATH") . "\\" . $fileLocation . "\\";
        $fileCreator = new MakesFile($fileLocation);
        $file = "#DONT EDIT THIS FILE!\n#GENERATED AUTOMATIC BY BassKeyPHP Framework\n\n" . $file;
        $fileCreator->generateFile($fileName, $file, true);
    }
}
