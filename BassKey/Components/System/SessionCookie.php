<?php

namespace BassKey\Components\System;

class SessionCookie {
    
    public static function Start()
    {
        if (session_id() == '') 
        { 
            session_start();
        }
    }
    
    public static function SetVariable($name, $value)
    {
        $_SESSION[$name] = $value;
    }
    
    public static function GetVariable($name)
    {
        return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
    }
    
    public static function Clear()
    {
        session_unset();
    }    
    
    public static function Destroy()
    {
        session_destroy(); 
    }
    public static function SessionIsset()
    {
        if (session_id() == '') 
        {
            return false;
        }
        else
        {
            return true;
        }
    }
}
