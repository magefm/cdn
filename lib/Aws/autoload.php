<?php

spl_autoload_register(function($className){
    $fileName = implode('/', explode('\\', $className)) . '.php';
    $fileName = stream_resolve_include_path($fileName);

    if ($fileName) {
        include_once $fileName;
    }
});