<?php
namespace FriendsApi\controllers;

use FriendsApi\services\UsersService;
use Phalcon\Di;

class UsersController extends RestController
{
    public function pingAction()
    {
        echo "pong";
    }

    public function addAction()
    {
        $userName = $this->request->getPost('name');

        $service = new UsersService();
        $userEntity = $service->addUser($userName);

        return $this->returnResult(
            [
                'user' => [
                    'user_id' => $userEntity->getId(),
                    'name'    => $userEntity->getName()
                ]
            ]
        );
    }


    public function friendsListAction()
    {
        $userId = $this->dispatcher->getParam('user_id');

        $service = new UsersService();
        $friendsList = $service->friendsList($userId);

        $result = [
            'user' => [
                'user_id' => $userId,
                'friends' => $friendsList
            ]
        ];

        return $this->returnResult($result);
    }

    public function friendsRequestsAction()
    {
        $userId = $this->dispatcher->getParam('user_id');

        $service = new UsersService();
        $friendsRequests = $service->friendRequestsList($userId);

        $result = [
            'user' => [
                'user_id'          => $userId,
                'friends_requests' => $friendsRequests
            ]
        ];

        return $this->returnResult($result);
    }

    public function friendsTreeAction()
    {

        $userId = $this->dispatcher->getParam('user_id');
        $nestingLevel = $this->request->get('n', null, 10);
        $service = new UsersService();
        $levels = $service->friendsGraph($userId, $nestingLevel);
        return $this->returnResult($levels);
    }

    public function addFriendAction()
    {
        $userId = $this->dispatcher->getParam('user_id');
        $friendId = $this->request->getPost('friend_id');

        $service = new UsersService();
        $service->sendFriendRequest($userId, $friendId);

        $result = [
            'user_id'   => $userId,
            'friend_id' => $friendId
        ];
        return $this->returnResult($result);
    }


    public function friendshipAction()
    {
        $userId = $this->dispatcher->getParam('user_id');
        $friendId = $this->dispatcher->getParam('friend_id');
        $acceptFriendship = $this->request->getPut('accept');

        /**
         * for unit test, becuse with phalcon framework it's not ease emulate PUT request
         */
        if ($acceptFriendship === null) {
            $acceptFriendship  = $_REQUEST['accept'];
        }
        $service = new UsersService();
        $service->acceptFriendRequest($userId, $friendId, (bool)$acceptFriendship);
        $result = [''];

        return $this->returnResult($result);
    }
}

