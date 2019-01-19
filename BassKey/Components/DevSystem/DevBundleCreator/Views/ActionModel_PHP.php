<?php
    $functionAccess = trim($functionAccess);
    $static = ($static === true) ? ' static ' : ' ';
?>
<?=$functionAccess; ?><?=$static; ?>function <?=$functionName; ?>($parameters = <?= $parametersPage == null ? 'array()'
        : "'" . serialize($parametersPage) . "'"; ?>)
    {
        $parameters = unserialize($parameters);
        $this->RenderPhp("<?=$viewPath; ?>", $parameters);
    }
