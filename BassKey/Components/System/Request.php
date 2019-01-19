<?php

namespace BassKey\Components\System;


class Request
{
    public static function Get($name)
    {
        return filter_input(INPUT_GET, $name, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    public static function Post($name)
    {
        return filter_input(INPUT_POST, $name, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    public static function GetCookie($name)
    {
        return filter_input(INPUT_COOKIE, $name, FILTER_SANITIZE_SPECIAL_CHARS);
    }

}