<?php

namespace BassKey\Components\YmlParser\Controllers;

use BassKey\GlobalVariables;

class YmlParser
{
    /**
     * @var string $ymlContent
     * Content yml file
     */
    private $ymlContent = "";
    /**
     * @var string $ymlFilePath
     * Path to yml file
     */
    private $ymlFilePath = "";
    /**
     * @var string $homePath
     */
    private $homePath = "";
    /**
     * @var string $dir
     */
    private $dir = "";
    /**
     * @var array $constancePathsList
     */
    private $constancePathsList = array();
    /**
     * @var bool $importsFiles
     */
    private $importsFiles = true;

    public function __construct()
    {
        $this->homePath = GlobalVariables::getInstance()->get("HOME_PATH") . "\\";
        $this->constancePathsList = GlobalVariables::getInstance()->get("RENDER_CONSTANCE");
    }

    public function ymlDump($array)
    {
        return Spyc::YAMLDump($array);
    }

    public function getYmlContent($content): bool
    {
        if (empty($content))
        {
            echo "ERROR: File content dont exist (1)";
            return false;
        }

        $this->ymlContent =  $content;

        return true;
    }
    
    public function getYmlByPath($path): bool
    {
        if (empty($path))
        {
            echo "ERROR: Required is file path";
            return false;
        }

        $this->ymlFilePath = str_replace("\\", "/", $path);
        $this->LoadYmlFromFile();

        return true;
    }

    private function importWhereResourceKey($elementToImport, &$ymlArray)
    {
        foreach ($elementToImport as $key => $item)
        {
            if($key !== "resource")
            {
                continue;
            }

            $ymlArray = array_merge($ymlArray, $this->parseYmlToArrayFromParameter($item));
        }
    }

    private function importYmlFileMoreLevels($array)
    {
        $ymlArray = array();

        foreach ($array as $elementKey => $importElement)
        {
            $this->importWhereResourceKey($importElement, $ymlArray);
        }

        return $ymlArray;
    }

    private function importYmlFile($array)
    {
        foreach ($array as $key => $item)
        {
            if($key === "imports" && is_array($item) && $this->importsFiles === true)
            {
                $array = $this->importYmlFileMoreLevels($item);
                continue;
            }

            if (is_array($item))
            {
                $array[$key] = $this->importYmlFile($item);
            }
        }

        return $array;
    }
    
    public function parseYmlToArray($dir = "", $importsFiles = true)
    {
        $this->dir = $dir;
        $this->importsFiles = $importsFiles;

        if(empty($this->ymlFilePath))
        {
            return null;
        }

        foreach ($this->constancePathsList as $name => $dir)
        {
            $this->ymlFilePath = str_replace($name, $dir, $this->ymlFilePath);
        }

        if(!file_exists($this->ymlFilePath))
        {
            return null;
        }

        return $this->importYmlFile(Spyc::YAMLLoad($this->ymlFilePath));
    }

    private function parseYmlToArrayFromParameter($ymlFilePath)
    {
        if(empty($ymlFilePath))
        {
            return null;
        }

        foreach ($this->constancePathsList as $name => $dir)
        {
            $ymlFilePath = str_replace($name, $dir, $ymlFilePath);
        }

        return $this->importYmlFile(Spyc::YAMLLoad($ymlFilePath));
    }

    private function LoadYmlFromFile()
    {
        if (empty($this->ymlFilePath) || !file_exists($this->ymlContent))
        {
            return false;
        }

        $this->ymlContent = file_get_contents($this->ymlFilePath);

        return true;
    }
    
    public function __destruct() {
       
    }
}
