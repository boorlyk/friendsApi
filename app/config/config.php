<?php

defined('APP_PATH') || define('APP_PATH', realpath('.'));

return new \Phalcon\Config(
    array(
        'redis'       => array(
            'host' => '127.0.0.1',
            'port' => '6379'
        ),
        'neo4j'       => array(
            'host' => '127.0.0.1',
            'port' => '7474'
        ),
        'application' => array(
            'controllersDir' => APP_PATH . '/app/controllers/',
            'libraryDir'     => APP_PATH . '/app/library/',
            'testsDir'       => APP_PATH . '/tests/',
            'baseUri'        => '/techery/',
        )
    )
);
