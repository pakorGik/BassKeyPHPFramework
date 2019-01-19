<?php

namespace BassKey\Components\Runner;

use BassKey\System;

class Initiator
{
    /**
     * @param $baseUrl
     * @param $routingConfiguration
     * Run BassKey Framework
     */
    public function runFramework($baseUrl, $routingConfiguration): void
    {
        $pathParser = new PathParser();

        //get address parts
        $addressesElements = $pathParser->splitAddress($baseUrl);

        //get routing elements TODO: make cache it
        $routingElements = $pathParser->getRoutingElements($routingConfiguration);
//        System::getInstance()->dump($routingElements);

        //execute selected element
        $executeElement = new ExecuteElement();
        $executeElement->execute($addressesElements, $routingElements);
    }
}
