<div class="bass-key-dev-mode html-debugger blue darken-1 z-depth-3 white-text">

    <style type="text/css">
        <?php
            require_once __DIR__ . "/assets/materialize.css";
            require_once __DIR__ . "/assets/style.css";
        ?>
    </style>

    <div class="row">
        <div class="col s12">
            <img class="logo" src="<?= \BassKey\GlobalVariables::getInstance()->get("HOME_URL") . '/BassKey/Components/DevSystem/DevAnalytic/Views/assets/img/bassKeyphp-logo-120px.png'; ?>" alt="BassKeyPHP">
            <p class="">Dev Mode</p>
            <?php
                foreach ($appKernelObjects as $key => $controller):
            ?>
                <p class="controller-name">Controller: <?=$key; ?></p>
                <p><?php \BassKey\System::getInstance()->dump($controller); ?></p>
            <?php
                endforeach;
            ?>
            <a href="?bassKeyPhpDevSys=BundleCreator" class="bundle-creator">Bundle Creator</a>
        </div>
    </div>

</div>
