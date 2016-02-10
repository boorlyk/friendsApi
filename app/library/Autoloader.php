<?php

namespace FriendsApi\library;


class Autoloader
{
    public function load($className)
    {
        $rootDirectory = realpath(__DIR__ . "/../");
        $path = explode('\\', $className);

        if ($path[0] !== 'FriendsApi') {
            return;
        }

        unset($path[0]);

        $filePath = $rootDirectory . '/' . implode('/', $path) . '.php';
        if (is_file($filePath)) {
            include_once $filePath;
        }
    }
}