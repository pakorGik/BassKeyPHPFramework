<?php

namespace BassKey\Components\System;

use BassKey\Components\System\Status;

class DirectorySystem
{
    private $directory;

    public function __construct($directory)
    {
        $this->directory = $directory;
    }

    private function removeDirectoryPermanently($dirPath)
    {
        if (! is_dir($dirPath)) {
            throw new \InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                $this->removeDirectoryPermanently($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    public function removeDirectoryAndFiles(): Status
    {
        $this->removeDirectoryPermanently($this->directory);
        return (new Status())->setStatusAsSuccess()->setInfo(array("directory" => $this->directory));
    }

    public function createDirectory($mode = 0777, $overwrite = false): Status
    {
        if(is_dir($this->directory) && $overwrite === false)
        {
            return (new Status())->setStatusAsError()->addError("Directory exist");
        }
        if(is_dir($this->directory) && $overwrite === true)
        {
            $this->removeDirectoryAndFiles();
        }

        $old = umask(0);
        mkdir($this->directory, 0777);
        umask($old);

        return (new Status())->setStatusAsSuccess()->addInfo("success", array(
            "directory" => $this->directory,
        ));
    }

}