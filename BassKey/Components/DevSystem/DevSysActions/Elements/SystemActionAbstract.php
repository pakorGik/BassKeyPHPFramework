<?php

namespace BassKey\Components\DevSystem\DevSysActions\Elements;

//TODO: sprawdzic poprawnosc deskrypcii

/**
 *  Abstract to executors system action
 */
abstract class SystemActionAbstract
{

    /**
     * @param string $actionName
     */
    private $actionName;
    /**
     * method to overight
    */
    public function execute(){ }

    /**
     * @return string
     */
    public function getActionName()
    {
        return $this->actionName;
    }

    /**
     * @param string $actionName
     */
    public function setActionName($actionName)
    {
        $this->actionName = $actionName;
    }

}