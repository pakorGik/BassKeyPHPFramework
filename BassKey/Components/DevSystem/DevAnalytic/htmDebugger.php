<?php

namespace BassKey\Components\DevSystem\DevAnalytic;

use BassKey\AppKernel;

class htmDebugger
{
    public function screenInformation()
    {
        $appKernelObjects = AppKernel::GetObjectsBundles();
        require_once __DIR__ . "/Views/screenBelt.php";
    }
}