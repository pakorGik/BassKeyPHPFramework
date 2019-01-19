<?php

namespace BassKey\Engine;


class MethodModel
{
    private $name = "";

    private $body = null;

    public function __construct()
    {
        $this->name = "";
        $this->body = function () { return null; };
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getBody(): \Closure
    {
        return $this->body;
    }

    public function setBody(\Closure $body): void
    {
        $this->body = $body;
    }
}
