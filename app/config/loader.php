<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerDirs(
    array(
        $config->application->controllersDir,
        "FriendsApi\\library" => $config->application->libraryDir
    )
)->register();

include_once APP_PATH."/vendor/autoload.php";

require_once $config->application->libraryDir.'/Autoloader.php';

spl_autoload_register([new FriendsApi\library\Autoloader(), 'load']);


