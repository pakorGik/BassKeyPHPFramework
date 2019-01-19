<?php
/**
 * @author PaKor
 */

/**
 * Execute action in controller
 * @param string $logic Logic path to action
 */
function executeAction($logic)
{
    //TODO: DO IT!
}

function assets($url = "")
{
    echo \BassKey\GlobalVariables::getInstance()->get('ASSETS_URL') . "/$url";
}
