<?php


namespace BassKey\Components\DevSystem\DevSysActions\Elements;

use BassKey\Components\System\Request;

/**
 * Class to detecting developer acition
 */
class SystemActionDetector
{
    private $actionName = null;

    private function methodFinder()
    {
        //TODO: use file mapper to check isset action
    }

    public function __construct()
    {
        $this->actionName = empty(Request::Get("BassKeyPhpDevSys")) ? Request::Get("BassKeyPhpDevSys")
         : (empty(Request::Post("BassKeyPhpDevSys")) ? Request::Post("BassKeyPhpDevSys") : null);

        if($this->actionName === null) {
            return false;
        }

        $this->methodFinder();
    }

}