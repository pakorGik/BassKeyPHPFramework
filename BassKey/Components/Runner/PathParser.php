<?php

namespace BassKey\Components\Runner;

use BassKey\AppKernel;
use BassKey\GlobalVariables;
use BassKey\System;

class PathParser
{
    //TODO: Create Dynamic parameter address

    /**
     * @param $baseUrl
     * @return array
     *
     * Split address on parts
     *
     * --------------------------------------------------------
     * Example
     *
     * address: http://localhost/BassKeyPHP/admin/page/
     * in config defined baseUrl as: 'localhost/BassKeyPHP/'
     *---------------------------------------------------------
     *
     */
    public function splitAddress($baseUrl): array
    {
        //dump from example: '/BassKeyPHP/admin/page/'
        $fullAddressWithoutDomain = str_replace("index.php", "", $_SERVER['REQUEST_URI']);

        //dump from example: '/BassKeyPHP/'
        $baseAddress = str_replace($_SERVER['HTTP_HOST'], "", $baseUrl);

        //dump from example: '/admin/page/'
        $subAddress = str_replace($baseAddress, "/", $fullAddressWithoutDomain);
        $subAddress = preg_replace('/\?.*/', "", $subAddress);

        return array(
            'fullAddress' => $fullAddressWithoutDomain,
            'baseAddress' => $baseAddress,
            'subAddress' => $subAddress,
        );
    }

    private function addControllerOrView(RoutingElement &$routingElement, $configuration)
    {
        //if isset file, type add to object
        if(isset($configuration['fileType']))
        {
            $routingElement->setFileType($configuration['fileType']);
        }

        // if isset array 'defaults' and have key '_controller'
        if(isset($configuration["defaults"])
            && is_array($configuration["defaults"])
            && isset($configuration["defaults"]['_controller']))
        {
            $routingElement->setController($configuration["defaults"]['_controller']);
        }

        // if isset array 'defaults' and have key '_view'
        if(isset($configuration["defaults"])
            && is_array($configuration["defaults"])
            && isset($configuration["defaults"]['_view']))
        {
            $routingElement->setView($configuration["defaults"]['_view']);
        }

        // if isset key '_controller'
        if(isset($configuration["_controller"]) && is_string($configuration["_controller"]))
        {
            $routingElement->setController($configuration["_controller"]);
        }

        // if isset key '_view'
        if(isset($configuration["_view"]) && is_string($configuration["_view"]))
        {
            $routingElement->setView($configuration["_view"]);
        }
    }

    private function getRoutingList(array $routingArray): array
    {
        $routingList = array();

        foreach ($routingArray as $nameElement => $routing)
        {
            /**
             * Check isset one of keys: ['defaults', '_controller', '_view']
             * Check isset 'address' key
             */
            if(!is_array($routing) || !isset($routing["address"])
                || (!isset($routing["defaults"]) && !isset($routing["_controller"]) && !isset($routing["_view"])) )
            {
                continue;
            }

            //create Routing Element
            $routingElement = new RoutingElement();

            //set routing name
            $routingElement->setName($nameElement);

            //if address is array
            if(is_array($routing["address"]))
            {
                $routingElement->setAddressList($routing["address"]);
            }

            //if address is string
            if(is_string($routing["address"]))
            {
                $routingElement->setAddressList(array($routing["address"]));
            }

            //add controller or view
            $this->addControllerOrView($routingElement, $routing);

            array_push($routingList, $routingElement);
        }

        return $routingList;
    }

    public function getRoutingElements($pageStructure): array
    {
        $pagesFromConfig = KeySearch::getContentWhereKey($pageStructure, 'routing');

        $routingElements = array();

        foreach($pagesFromConfig as $pages)
        {
            $routingElements = array_merge($routingElements, $this->getRoutingList($pages));
        }

        return $routingElements;
    }
	
}
