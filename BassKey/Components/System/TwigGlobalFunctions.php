<?php

namespace BassKey\Components\System;

use BassKey\GlobalVariables;

class TwigGlobalFunctions {

    public function TwigFunctions()
	{
		return array(
			
			new \Twig_SimpleFunction(
				'assets', 
				function ($file = "") 
				{
					return GlobalVariables::getInstance()->get('ASSETS_URL') . "/" . $file;
				}),
                        
            new \Twig_SimpleFunction(
                'getUrl', 
                function ($index = "") 
                {
                    if(isset(GlobalVariables::getInstance()->get("ROUTING_CONFIG")["routing"]) &&
                        is_array(GlobalVariables::getInstance()->get("ROUTING_CONFIG")["routing"]))
                    {
                        $routing = GlobalVariables::getInstance()->get("ROUTING_CONFIG")["routing"];

                        if(isset($routing[$index]) &&
                            is_array($routing[$index]) &&
                            isset($routing[$index]["adress"]))
                        {
                            if (is_array($routing[$index]["adress"]))
                            {
                                return rtrim(GlobalVariables::getInstance()->get('home'), "/") . "/" .$routing[$index]["adress"][0];
                            }
                            else
                            {
                                return rtrim(GlobalVariables::getInstance()->get('home'), "/") . "/" . $routing[$index]["adress"];
                            }
                        }
                    }
                    
                    return "";
                })
			
		);
	}

    public function AddFunction (\Twig_Environment &$twig)
    {
        foreach (self::TwigFunctions() as $function)
        {
            $twig->addFunction($function);
        }
    }
	
}
