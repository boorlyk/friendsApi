<?php

error_reporting(E_ALL);

define('APP_PATH', realpath(__DIR__.'/../../'));

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

    include_once APP_PATH . "/vendor/autoload.php";

    Di::reset();

    Di::setDefault($di);

    $usersCount = 10000;
    $relationsCount = 100000;

    $i = 0;
    while ($i < 10000) {
        $url = '/users';
        $name = 'UT' . rand(999, 9999) . '-' . microtime();
        $service = new \FriendsApi\services\UsersService();
        $userEntity = $service->addUser($name);
        error_log('users added:' . $i);
        $i++;
    }

    $i = 0;

    while ($i < 100000) {
        $userService = new \FriendsApi\services\UsersService();
        $userEntity = $userService->findRandUser();
        $friendUserEntity = $userService->findRandUser();


        $userId = $userEntity->getId();
        $url = '/users/' . $userId . '/friends';
        $params = ['friend_id' => $friendUserEntity->getId()];


        $userService->sendFriendRequest($userEntity->getId(), $friendUserEntity->getId());
        $userService->acceptFriendRequest($userEntity->getId(), $friendUserEntity->getId(), true);
        error_log('relations created '.$i);
        $i++;
    }
} catch (\Exception $e) {
    echo $e->getMessage() . '<br>';
    echo '<pre>' . $e->getTraceAsString() . '</pre>';
}
