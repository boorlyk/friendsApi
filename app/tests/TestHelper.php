<?php

error_reporting(E_ALL);

define('APP_PATH', realpath('./../../'));

use Phalcon\Di;

try {

    /**
     * Read the configuration
     */
    $config = include APP_PATH . "/app/config/config.php";

    /**
     * Read auto-loader
     */
    include APP_PATH . "/app/config/loader.php";

    /**
     * Read services
     */
    include APP_PATH . "/app/config/services.php";

    include_once APP_PATH."/vendor/autoload.php";

    Di::reset();

    Di::setDefault($di);

} catch (\Exception $e) {
    echo $e->getMessage() . '<br>';
    echo '<pre>' . $e->getTraceAsString() . '</pre>';
}
