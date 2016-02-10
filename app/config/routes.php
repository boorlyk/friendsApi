<?php
use Phalcon\Mvc\Router;

$router = new Router(false);


/**
 * GET /users/{user_id}/friends — list of friends
 * GET /users/{user_id}/friends-requests — list of friends requests
 * GET /users/{user_id}/friends-tree — list of friends requests
 * PUT /users/{user_id}/friends/{friend_id]/?accept=true|false
 * POST /users/{user_id}/friends/?user_id = {friendId}
 *
 *
 */

$router->add(
    "/users",
    [
        'controller' => 'users',
        'action'     => 'add'
    ],
    ['POST']
);
$router->add(
    "/users/{user_id}/friends",
    [
        'controller' => 'users',
        'action'     => 'friendsList'
    ],
    ['GET']
);

$router->add(
    "/users/{user_id}/friends-requests",
    'Users::friendsRequests',
    ['GET']
);

$router->add(
    "/ping",
    [
        'controller' => 'users',
        'action'     => 'ping'
    ],
    ['GET']
);

$router->add("/users/{user_id}/friends-tree", 'Users::friendsTree', ['GET']);
$router->add("/users/{user_id}/friends", 'Users::addFriend', ['POST']);
$router->add("/users/{user_id}/friends/{friend_id}", 'Users::friendship', ['PUT']);

//$router->handle();
return $router;
