<?php

namespace BassKey\Components\DevSystem\DevBundleCreator;

use BassKey\Components\System\Controller;

class MakesDirectory extends Controller
{
    private $path = '';

    public function generateDirectory($addressCreator)
    {
        //use / to explode directory sequence
        if(empty($addressCreator))
        {
            return false;
        }

        $sequence = explode('/', $addressCreator);

        $nextElements = '';
        foreach ($sequence as $key => $directory)
        {
            $finalDirectoryPath = $this->path . $nextElements . "/$directory";
            if (!file_exists($finalDirectoryPath))
            {
                mkdir($finalDirectoryPath, 7777, true);
            }
            $nextElements .= "/$directory";
        }

        return true;
    }

    public function __construct($path = '')
    {
        $this->path = $path;
    }

}