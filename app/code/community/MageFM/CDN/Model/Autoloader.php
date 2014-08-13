<?php

class MageFM_CDN_Model_Autoloader
{

    protected static $registered = false;

    public function registerAutoloader()
    {
        if (self::$registered) {
            return;
        }

        spl_autoload_register(array($this, 'autoload'), false, true);
        self::$registered = true;
    }

    public function autoload($className)
    {
        $fileName = implode('/', explode('\\', $className)) . '.php';
        $fileName = stream_resolve_include_path($fileName);

        if ($fileName) {
            include_once $fileName;
        }
    }

}
