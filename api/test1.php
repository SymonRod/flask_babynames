<?php
    $config=$_SERVER['DOCUMENT_ROOT'].'/etc/config.php';
    echo($config);
    include ($config);
    echo('Host: '.$host);