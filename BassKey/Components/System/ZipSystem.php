<?php

namespace BassKey\Components\System;


class ZipSystem
{
    private $fileLocation;
    private $destination;

    public function __construct(string $fileLocation, string $destination)
    {
        $this->fileLocation = $fileLocation;
        $this->destination = $destination;
    }

    public function unZip(): Status
    {
        $status = (new Status())->setStatusAsSuccess();
        $zip = new \ZipArchive;
        if (file_exists($this->fileLocation) && $zip->open($this->fileLocation))
        {
            $zip->extractTo($this->destination);
            $zip->close();
            return $status->setInfo(array(
                "file" => $this->fileLocation,
                "destination" => $this->destination,
            ));
        }

        return $status->setStatusAsError()
            ->addError("Cannot open file")
            ->addError(array(
                "file" => $this->fileLocation,
                "destination" => $this->destination,
            ));
    }
}