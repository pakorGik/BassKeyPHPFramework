<?php

namespace BassKey\Engine;


class MethodList
{
    private $methodList = array();

    public function getMethod($name): MethodModel
    {
       return isset($this->methodList[$name]) ?
           $this->methodList[$name] :
           new MethodModel();
    }

    public function setMethod(MethodModel $method)
    {
        $this->methodList[$method->getName()] = $method;
    }

    public function getMethodList(): array
    {
        return $this->methodList;
    }
}
