<?php

namespace BassKey\Components\DevSystem\DevBundleCreator;

use BassKey\Components\System\Controller;

class MakesFile extends Controller
{
    private $path = '';

    public function generateFile($file, $content, $override = false)
    {
        if(empty($file))
        {
            return false;
        }

        $file = $this->path . $file;

        if (file_exists($file) && $override === false)
        {
            return false;
        }
        else if(file_exists($file) && $override === true)
        {
            unlink($file);
        }

        $fp = fopen($file, 'w');
        fwrite($fp, $content);
        fclose($fp);
        chmod($file, 0777);

        return true;
    }

    public function __construct($path = '')
    {
        $this->path = $path;
    }
}