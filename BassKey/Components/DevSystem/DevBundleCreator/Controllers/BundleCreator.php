<?php

namespace BassKey\Components\DevSystem\DevBundleCreator\Controllers;

use BassKey\Components\System\Controller;
use BassKey\GlobalVariables;
use BassKey\System;
use BassKey\Components\System\Request;
use BassKey\Components\YmlParser\Controllers\YmlParser;

class BundleCreator extends Controller
{
    private $placesViews = array(
        'cms' => '%BassKeyCMS%/Pages/',
    );
    private $placeView = '';

    private $bundle;
    private $path = '';
    private $viewContent = '<!doctype html><html><head><meta charset="UTF-8"><title><?=isset($title) ? $title : "TITLE"; ?></title></head><body> PAGE </body></html>';

    private function createRouting($renderAttribute, $bundleName, $className)
    {
        $ymlRoutingThisPagePath = "%home_path%/BassKeyCMS/Pages/$bundleName/Config/routing.yml";

        //Get YML CMS Pages Routing File
        $ymlParser = new YmlParser();
        $ymlParser->getYmlByPath($ymlRoutingThisPagePath);
        $routing = $ymlParser->parseYmlToArray("", false);

        System::getInstance()->dump($routing);

//        var_dump($bundleName);

        //create config
        $classExecuteName = "BassKeyCMS\\Pages\\$bundleName:$className:" . $renderAttribute['view-name'];
        $routing[$renderAttribute['url']] = array(
                'adress' => $renderAttribute['url'],
                'defaults' => array( '_controller' => $classExecuteName ),
            );

        //Save YML CMS Routing File
        $file = $ymlParser->ymlDump($routing);
        $fileCreator = new MakesFile($this->path . "/$bundleName/Config/");
        $fileCreator->generateFile('routing.yml', $file, true);

        //Get BASE CMS YML Routing File
        $baseConfigPath = '%home_path%/BassKeyCMS/Config/routing.yml';
        $ymlParser->getYmlByPath($baseConfigPath);
        $baseConfigArr = $ymlParser->parseYmlToArray("", false);


        //check if isset this import
        if(isset($baseConfigArr, $baseConfigArr['imports']))
        {
            foreach ($baseConfigArr['imports'] as $key => $resource)
            {
                if(!isset($resource['resource']))
                {
                    continue;
                }

                if($resource['resource'] === $ymlRoutingThisPagePath)
                {
                    //echo "No jest xd";
                    return true;
                }
            }
        }

        //add import to array
        if(isset($baseConfigArr, $baseConfigArr['imports']))
        {
            array_push($baseConfigArr['imports'], array(
                'resource' => $ymlRoutingThisPagePath,
            ));
        }
        elseif (isset($baseConfigArr))
        {
            $baseConfigArr['imports'][0] = array(
                'resource' => $ymlRoutingThisPagePath,
            );
        }

        //save array as yml routing
        $file = $ymlParser->ymlDump($baseConfigArr);
        $fileCreator = new MakesFile(dirname($this->path) . "/Config/");
        $fileCreator->generateFile('routing.yml', $file, true);

        return true;
    }

    private function createActionsPhpRender($actionName, $viewPath, $viewParameters, $static = false)
    {
        $access = 'public';

        return $this->GetRenderPhp("%BassKey%/Components/DevSystem/DevBundleCreator:ActionModel_PHP", array(
            'functionAccess' => $access,
            'static' => $static,
            'functionName' => $actionName,
            'viewPath' => $viewPath,
            'parametersPage' => $viewParameters,
        ), false);
    }

    private function executeActionsFromArray($actionsArrayStructure, $bundleName, $className)
    {
        $functions = array();

        foreach ($actionsArrayStructure as $functionName => $functionProperty)
        {
            if(!isset($functionProperty['render-php'], $functionProperty['render-php']['view-name'],
                $functionProperty['render-php']['view-path'], $functionProperty['render-php']['parameters'])
                || !is_array($functionProperty['render-php']['parameters']))
            {
                continue;
            }
            $this->createRouting($functionProperty['render-php'], $bundleName, $className);

            array_push($functions, $this->createActionsPhpRender(
                $functionName, $functionProperty['render-php']['view-path'],
                $functionProperty['render-php']['parameters']
            ));
        }

        var_dump($functions);

        return $functions;
    }

    private function createClass($bundleName, $classStructure)
    {
        if(empty($bundleName) || empty($classStructure))
        {
            return false;
        }

        foreach ($classStructure as $className => $class)
        {
            if(!isset($class['namespace'], $class['actions'])
                || empty($class['namespace']))
            {
                continue;
            }

            $file = $this->GetRenderPhp("%BassKey%/Components/DevSystem/DevBundleCreator:ClassModel", array(
                'namespace' => $class['namespace'],
                'className' => $className,
                'functions' => $this->executeActionsFromArray($class['actions'], $bundleName, $className),
                'use' => array(),
            ), false);

            if(empty($file))
            {
                continue;
            }

            $fileCreator = new MakesFile($this->path . "/$bundleName/Controllers/");
            $fileCreator->generateFile($className . '.php', $file, true);
        }

        return true;
    }

    private function createViewsPHP($bundleName, $view)
    {
        //System::getInstance()->dump($view);
        if(empty($bundleName) || empty($view))
        {
            return false;
        }

        foreach ($view as $viewName => $viewElement)
        {
            $fileCreator = new MakesFile($this->path . "/$bundleName/Views/");
            $fileCreator->generateFile($viewName . '.php', $this->viewContent);
        }

        return true;
    }

    private function generateDirectory($directorySequence)
    {
        if(!is_array($directorySequence))
        {
            return false;
        }

        $directoryCreator = new MakesDirectory($this->path);
        foreach ($directorySequence as $directory)
        {
            $directoryCreator->generateDirectory($directory);
        }

        return true;
    }

    private function createBundle()
    {
        if(!is_array($this->bundle))
        {
            return false;
        }

        System::getInstance()->dump($this->bundle);
        foreach ($this->bundle as $bundleName => $bundle)
        {
            $directorySequence = array();

            //bundle directory
            array_push($directorySequence, $bundleName);
            //controllers directory
            array_push($directorySequence, $bundleName . '/Controllers');
            //controllers views
            array_push($directorySequence, $bundleName . '/Views');
            //controllers config
            array_push($directorySequence, $bundleName . '/Config');

            //Execute methods
            $this->generateDirectory($directorySequence);

            //create view
            if(isset($bundle, $bundle['views']))
            {
                $this->createViewsPHP($bundleName, $bundle['views']);
            }
            //create class and actions
            if(isset($bundle, $bundle['class']))
            {
                $this->createClass($bundleName, $bundle['class']);

            }
        }

        return true;
    }

    public function bundleGeneratorFromYmlAction()
    {
        /*
         *===============
         *    CONFIG
         *===============
         * -------------------------------------------------------
         * yml-path - path to configure generation yml file
         * place - cms [from $this->placesViews array]
         * create-path - path to directory when it will be create
         * -------------------------------------------------------
         *===============
         *EXAMPLE CONFIG
         *===============
            ?yml-path=%home_path%\BassKeyCMS\Config\PagesStructure\pages.yml
            &place=cms
            &create-path=..\BassKeyCMS\Pages
        */

        $ymlPath = Request::Get('yml-path');
        $this->path = urldecode(Request::Get('create-path'));
        $this->placeView = isset($this->placesViews, $this->placesViews[trim(Request::Get('place'))])
            ? $this->placesViews[trim(Request::Get('place'))] : '';

        if(empty($ymlPath) || empty($this->path))
        {
            echo "You must add path name";
            return false;
        }

        $ymlParser = new YmlParser();
        $ymlParser->getYmlByPath($ymlPath);
        $bundleArray = $ymlParser->parseYmlToArray();

        if(!isset($bundleArray['bundle']))
        {
            echo "File don't have bundle structure";
            return false;
        }

        $this->bundle = $bundleArray['bundle'];
        $this->createBundle();

        return true;
    }

}