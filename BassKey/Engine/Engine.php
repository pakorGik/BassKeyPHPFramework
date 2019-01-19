<?php

namespace BassKey\Engine;

class Engine
{
    /**
     * @var MethodList
     */
    private $methodList = null;

    protected function __construct()
    {
        if($this->methodList === null)
        {
            $this->methodList = new MethodList();
        }
    }

    public function addMethod(string $name, $body)
    {
        $method = new MethodModel();
        $method->setName($name);
        $method->setBody($body);

        $this->methodList->setMethod($method);
    }

    public function __call($name, $arguments)
    {
        return $this->methodList->getMethod($name)->getBody()($arguments);
    }
}
