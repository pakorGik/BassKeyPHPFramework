<?='<?php'; ?>

namespace <?=$namespace; ?>;

use BassKey\Components\System\Controller;

<?php
    foreach ($use as $singleUse)
    {
        echo 'use ' . $singleUse;
    }
?>

class <?=$className ?> extends Controller
{
    <?php
    foreach ($functions as $singleFunction)
    {
        echo $singleFunction;
    }
    ?>
}

