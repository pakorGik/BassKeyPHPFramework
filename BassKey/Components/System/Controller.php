<?php

namespace BassKey\Components\System;

use BassKey\Components\DevSystem\DevAnalytic\htmDebugger;
use BassKey\GlobalVariables;

/**
 * Class Controller
 * @package BassKey\Components\System
 *
 */
class Controller
{
    /**
     * @var string $twigFunctions
     */
	public $pageName = "index";

    /**
     * @var array $twigFunctions
     */
	private $twigFunctions = array();

    private function getPath_Part_0($part0)
    {
        $parts0Parts = explode("/", $part0);

        // File is in src directory
        if (!preg_match('/^\%.*\%$/', $parts0Parts[0]))
        {
            return GlobalVariables::getInstance()->get("HOME_PATH") . "//src//" . $part0 . "//Views//";
        }

        // File is in different directory
        return GlobalVariables::getInstance()->get("HOME_PATH") . "/" . str_replace("%", "", $part0) . "//Views//";
    }

    private function getPathInfo($view)
    {
        $parts = explode(":", $view);
        $path = $this->getPath_Part_0($parts[0]);

        // if is logner path structure
        if(count($parts) > 2)
        {
            for ($i = 1; $i < count($parts) - 1; $i++)
            {
                $path .= $parts[$i] . "//";
            }
        }

        $fileName = $parts[count($parts) - 1];

        $pathInfo = array(
            "path"      => $path,
            "parts"     => $parts,
            "fileName"  => $fileName,
        );

        return $pathInfo;
    }
	
    public function RenderTwigSource($html, $parameters = array())
    {
        $loader = new \Twig_Loader_Array(array(
			$this->pageName => $html
        ));
		
        $twig = new \Twig_Environment($loader);

        echo $twig->render($this->pageName, $parameters);
    }

    public function GetTwigPath($view, $parameters = array())
    {
        $pathInfo = $this->getPathInfo($view);
        $path = $pathInfo['path'];
        $pathCache = $pathInfo['path'] . "cache";

        $fileName = $pathInfo['fileName'];
        $extension = array(".twig", ".twig.html", ".html.twig", ".html");

        foreach ($extension as $ext)
        {
            if(!file_exists($path . $fileName . $ext))
            {
                continue;
            }

            if (!file_exists($pathCache))
            {
                mkdir($pathCache, 0666, true);
            }

            return $this->addFunctionsAndRender($path, $fileName.$ext, $parameters);
        }

        return null;
    }

	public function RenderTwig($view, $parameters = array())
	{
	    echo $this->GetTwigPath($view, $parameters);
	}

	private function addFunctionsAndRender(string $path, string $fileName, array $parameters)
    {
        $path = str_replace("//", "/", $path);
        $loader = new \Twig_Loader_Filesystem($path);
        $twig = new \Twig_Environment($loader, array());

        $twigFunctions = new TwigGlobalFunctions();
        $twigFunctions->AddFunction($twig);

        try {
            return $twig->render($fileName, $parameters);
        }
        catch (\Exception $exception) {
            if(DEV_MODE === true)
            {
                echo $exception->getMessage();
            }
            return null;
        }
    }

    public function GetRenderTwigFromFile($fileName, $path, $parameters = array())
    {
        return $this->addFunctionsAndRender($path, $fileName, $parameters);
    }

	public function RenderPhp($view, $parameters = array())
    {
        $pathInfo = $this->getPathInfo($view);
        $path = $pathInfo['path'];
        $file = $path . $pathInfo['fileName'] . ".php";

        if (file_exists($file))
        {
            //show array as variables
            extract($parameters, EXTR_PREFIX_SAME, "wddx");

            //append global functions
            if(!function_exists("ExecuteAction"))
            {
                include(__DIR__ . "/TemplateFunctions.php");
            }

            //append template
            include($file);
        }
        else
        {
            die("brak pliku: " . $file);
            // TODO: ADD LOG
        }
	}

	public function GetRenderPhp($view, $parameters = array())
    {
        ob_start();
        $this->RenderPhp($view, $parameters);
        $file = ob_get_contents();
        ob_clean();

        return $file;
    }

	public function GetAssetsAddress($file = "")
	{
		return GlobalVariables::getInstance()->get("ASSETS_URL") . $file == "" ? "" : "/$file";
	}

	public function runDevDebugger()
    {
        if(DEV_MODE === true)
        {
            (new htmDebugger())->screenInformation();
        }
    }

    public function __construct() {}
    
}
