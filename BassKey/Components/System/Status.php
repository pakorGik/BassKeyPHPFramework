<?php

namespace BassKey\Components\System;

class Status
{
    private $status;
    private $errors = array();
    private $info = array();

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatusAsError()
    {
        $this->status = "error";
        return $this;
    }

    public function setStatusAsSuccess()
    {
        $this->status = "success";
        return $this;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function setErrors(array $errors)
    {
        $this->errors = $errors;
        return $this;
    }

    public function addError(...$errors)
    {
        foreach($errors as $error)
        {
            array_push($this->errors, $error);
        }
        return $this;
    }

    public function getInfo(): array
    {
        return $this->info;
    }

    public function setInfo(array $info)
    {
        $this->info = $info;
        return $this;
    }

    public function addInfo(...$infos)
    {
        foreach ($infos as $info)
        {
            array_push($this->info, $info);
        }
        return $this;
    }

    public function getJsonResult()
    {
        header('Content-Type: application/json');

        if($this->status === 'error')
        {
            return \json_encode(array(
                "status" => $this->status,
                "errors" => $this->getErrors(),
            ));
        }

        return \json_encode(array(
            "status" => $this->status,
            "information" => $this->getInfo(),
        ));
    }

}