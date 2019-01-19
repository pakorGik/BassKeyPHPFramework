<?php

namespace DemoBundle\Controllers;

use BassKey\Components\System\Controller;

class Demo extends Controller
{
    public function InitAction()
    {
        /*
         * Example parameters, transfer to view
         */
        $exampleParameters = array(
            "title" => "John",
            "students" => array("Peter", "Adam", "Tom"),
        );

        /*
         * Developer tool
         * Show controllers details
         * Uncomment to check
         */
//        $this->runDevDebugger();

        /*
         * Render view php type
         * Uncomment to check
         */
        $this->RenderPhp("DemoBundle:Demo:home", $exampleParameters);

        /*
         * Render view twig type
         * Uncomment to check
         */
//        $this->RenderTwig("DemoBundle:Demo:home", $exampleParameters);
    }
}
