<?php
/**
 * Services are globally registered in this file
 *
 * @var \Phalcon\Config $config
 */

use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Flash\Direct as Flash;
use Everyman\Neo4j\Client;

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->setShared(
    'url', function () use ($config) {
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
}
);

/**
 * Setting up the view component
 */
$di->setShared(
    'view', function () use ($config) {

    $view = new View();
    $view->disable();
    return $view;
}
);

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->setShared(
    'modelsMetadata', function () {
    return new MetaDataAdapter();
}
);

$di->set(
    'router',
    function () {
        require __DIR__ . '/routes.php';
        return $router;
    }
);

$di->setShared(
    'redis',
    function () use ($config) {
        $redis = new Redis();
        $redis->connect(
            $config->redis->host,
            $config->redis->port
        );
        return $redis;
    }
);

$di->setShared(
    'Neo4jClient',
    function () use ($config) {
        $neo4jClient = new \Everyman\Neo4j\Client(
            $config->neo4j->host,
            $config->neo4j->port
        );
        $neo4jClient->getTransport()->setAuth('neo4j','toor');
//        die;
        return $neo4jClient;
    }
);
$di->get('dispatcher')->setDefaultNamespace('FriendsApi\\controllers');
