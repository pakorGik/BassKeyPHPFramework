<?php

namespace BassKey;


class GlobalVariables
{
    private static $instance;

    private $variables = array();

    private function __construct() { }

    public function add(string $name, $value): void
    {
        $this->variables[$name] = $value;
    }

    public function get(string $name)
    {
        return $this->variables[$name] ?? null;
    }

    public static function getInstance()
    {
        if(self::$instance === null)
            self::$instance = new GlobalVariables();

        return self::$instance;
    }
}
