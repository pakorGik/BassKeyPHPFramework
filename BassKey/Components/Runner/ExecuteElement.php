<?php

namespace BassKey\Components\Runner;

use BassKey\AppKernel;
use BassKey\GlobalVariables;
use BassKey\System;

class ExecuteElement
{
    private const LOCATION_404_FILE = "/app/page404.php";
    private const VIEW_EXTENSIONS = array(
        ".class.php",
        ".php",
        ".html",
        ".css",
        ".js",
    );

    private function runController($controller, $parameters)
    {
        $parts = explode(":", $controller);

        $className = $parts[0] . "\\Controllers\\" . $parts[1];
        $functionName = $parts[2] . "Action";

        $BundlesObjArray = AppKernel::GetObjectsBundles();

        if(!array_key_exists($className, $BundlesObjArray))
        {
            AppKernel::SetObjectsBundles($className, new $className());
        }

        $bundleObject = AppKernel::GetObjectsBundles()[$className];
        call_user_func_array(array($bundleObject, $functionName), $parameters);
    }

    private function parsePathByRenderer(&$path)
    {
        $renderConstance = GlobalVariables::getInstance()->get("RENDER_CONSTANCE");

        foreach ($renderConstance as $name => $pathToConstance)
        {
            $path = str_replace($name, $pathToConstance, $path);
        }
        $path = str_replace("\\", "/", $path);
        $path = str_replace("//", "/", $path);
    }

    //TODO: Defined extension in config
    private function runView($bundlePath)
    {
        $this->parsePathByRenderer($bundlePath);

        foreach (self::VIEW_EXTENSIONS as $ext)
        {
            if(!file_exists(GlobalVariables::getInstance()->get("HOME_PATH") . $bundlePath . $ext))
            {
                continue;
            }

            include GlobalVariables::getInstance()->get("HOME_PATH") . $bundlePath . $ext;
            return true;
        }

        return false;
    }

    private function executeElement(RoutingElement $routingElement, $parameters = array())
    {
        if($routingElement->getController() !== null)
        {
            $this->runController($routingElement->getController(), $parameters);
            return true;
        }

        if($routingElement->getView() !== null)
        {
            $this->runView($routingElement->getView());
            return true;
        }

        return false;
    }

    private function getCleatArray(array $arr): array
    {
        $readyArray = array();

        foreach ($arr as $arrayKey => $arrayElement)
        {
            if (!is_array($arrayElement))
            {
                array_push($readyArray, $arrayElement);
            }
            else
            {
                $readyArray = array_merge($readyArray, $this->getCleatArray($arrayElement));
            }
        }

        return $readyArray;
    }

    private function checkAddressHaveParameters($address):array
    {
        $regexParametersInAddress = '/\$[a-zA-Z0-9]*/s';
        $parameters = array();

        //get parameters from address
        preg_match_all($regexParametersInAddress, $address, $parameters, PREG_SET_ORDER, 0);

        //formate array
        $parametersClear = $this->getCleatArray($parameters);

        return $parametersClear;
    }

    private function removeEmptyElementsOfArray(&$array)
    {
        foreach ($array as $key => $item)
        {
            if($item === '') {
                unset($array[$key]);
            }
        }

        $array = array_values($array);
    }

    //TODO: clear this code
    private function compareAddressAndConfig(array $addressesList, string $pageAddress)
    {
        foreach ($addressesList as $address)
        {
            //get parameters
            $parameters = $this->checkAddressHaveParameters($address);

            //parameters values
            $parametersValues = array();

            //check address have parameters
            if(count($parameters) > 0)
            {
                //get address with no parameters
                $address = preg_replace('/\$.*/s', "", $address);

                //get actual address with no parameters
                $addressElements = explode("/", $pageAddress);
                //remove empty elements form array
                $this->removeEmptyElementsOfArray($addressElements);

                $elementsToCheck = count($addressElements) - count($parameters);

                if($elementsToCheck < 0)
                {
                    continue;
                }

                for($elementsToCheck; $elementsToCheck <= count($addressElements); $elementsToCheck++)
                {
                    array_push($parametersValues, urldecode($addressElements[$elementsToCheck]));
                    unset($addressElements[$elementsToCheck]);
                }
                //set this clear parameters
                $pageAddress = implode("/",$addressElements);
            }

            if(trim($address, "//") == trim($pageAddress , "//"))
            {
                return $parametersValues;
            }
        }

        return false;
    }

    public function execute(array $addresses, array $routingElements): bool
    {
        foreach($routingElements as $key => $routing)
        {
            //check address isset in routing
            $result = $this->compareAddressAndConfig($routing->getAddressList(), $addresses['subAddress']);

            if($result !== false)
            {
                //set result as routing element object
                $selectedRoutingElement = $routingElements[$key];

                //declare header file
                $fileType = $selectedRoutingElement->getFileType();
                (new FileHeader())->declareHeaderFileType($fileType);

                //execute this element
                $this->executeElement($selectedRoutingElement, $result);
                return true;
            }
        }

        //PAGE DON'T EXIST
        header("HTTP/1.0 404 Not Found");
        include GlobalVariables::getInstance()->get("HOME_PATH") . self::LOCATION_404_FILE;
        return false;
    }

}
