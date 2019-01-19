<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?=$title ?></title>
        <link rel="stylesheet" href="<?php assets("css/materialize.min.css"); ?>" />
    </head>
    <body>
        <h1 class="center">Welcome in Demo BassKeyPHP Framework!</h1>
        <div class="collection">
            <p>Example list:</p>
            <ul>
                <?php foreach ($students as $student): ?>
                    <li class="collection-item"><?=$student; ?></li>
                <?php endforeach; ?>
            </ul>
         </div>
    </body>
</html>