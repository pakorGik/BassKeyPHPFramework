<?php

namespace BassKey\Components\System;

use BassKey\Components\System\Status;

class FileUploader
{
    public function uploadFile($fileParameterName, $uploadDirectory, $enabledFormats,
                               $maxSize, $isImage = false, $overwriteFile = false): Status
    {
        $status = (new Status())->setStatusAsSuccess();

        $target_file = $uploadDirectory . "\\" . basename($_FILES[$fileParameterName]["name"]);
        $uploadOk = 1;
        $fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        // Check if image file is a actual image or fake image
        if($isImage === true && \getimagesize($_FILES[$fileParameterName]["tmp_name"]) == false)
        {
            $status->setStatusAsError();
            $status->addError("File is not img");
            return $status;
        }
        // Check if file already exists
        if (file_exists($target_file) && $overwriteFile === false)
        {
            $status->setStatusAsError();
            $status->addError("File already exists");
            return $status;
        }
        // Check file size
        if ($_FILES[$fileParameterName]["size"] > $maxSize)
        {
            $status->setStatusAsError();
            $status->addError("File is to large");
            $status->addError("Enabled file size is: $maxSize");
            return $status;
        }
        // Allow certain file formats
        $formatChecker = false;
        foreach ($enabledFormats as $key => $format)
        {
            if($format == $fileType)
            {
                $formatChecker = true;
            }
            if($key+1 === count($enabledFormats) && $formatChecker === false)
            {
                $status->setStatusAsError();
                $status->addError("Not allowed format type");
                $status->addError("Allowed types : [". implode(",", $enabledFormats)."]");
                return $status;
            }
        }

        if (!move_uploaded_file($_FILES[$fileParameterName]["tmp_name"], $target_file))
        {
            $status->setStatusAsError();
            $status->addError("Error upload this file, check php.ini settings");
            return $status;
        }

        $status->addInfo("The file ". basename( $_FILES[$fileParameterName]["name"]). " has been uploaded.");
        $status->addInfo(array(
            "file" => basename( $_FILES[$fileParameterName]["name"]),
            "file_path" => $target_file
        ));

        return $status;
    }
}
