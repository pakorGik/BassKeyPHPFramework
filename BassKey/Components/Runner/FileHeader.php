<?php

namespace BassKey\Components\Runner;


class FileHeader
{
    public function declareHeaderFileType($type): bool
    {
        $header = "";

        switch ($type)
        {
            case 'json':
                $header = $this->getJson();
                break;
            case 'js':
                $header = $this->getJs();
                break;
            case 'css':
                $header = $this->getCss();
                break;
            default:
                return false;
        }

        //declare file header
        header($header);

        return true;
    }

    private function getJson(): string
    {
        return "Content-Type: application/json; charset: UTF-8";
    }

    private function getJs(): string
    {
        return "Content-Type: application/javascript; charset: UTF-8";
    }

    private function getCss(): string
    {
        return "Content-type: text/css; charset: UTF-8";
    }
}
