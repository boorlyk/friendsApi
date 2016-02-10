<?php
namespace FriendsApi\tests\controllers;

use FriendsApi\services\UsersService;
use FriendsApi\tests\MainTestCase;
use Phalcon\Http\Request;
use Phalcon\Mvc\Dispatcher;

class UsersControllerTest extends MainTestCase
{
    public function testAddUser()
    {
        $i = 0;
        $url = '/users';
        $name = 'UT' . rand(999, 9999) . '-' . microtime();

        $response = $this->load($url, "POST", ['name' => $name]);
        $this->assertSuccessResponse($response);
        $this->assertArrayHasKey('user', $response['result']);
        $this->assertArrayHasKey('user_id', $response['result']['user']);
        $this->assertArrayHasKey('name', $response['result']['user']);
        $this->assertEquals($name, $response['result']['user']['name']);
    }

    public function testGetUserFriendsTree()
    {
        $service = new UsersService();
        $user = $service->findRandUser();
        $url = '/users/' . $user->getId() . '/friends-tree';

        $response = $this->load($url, 'GET', ['n' => 3]);
        $this->assertSuccessResponse($response);
    }


    public function testAddFriend()
    {
        $userService = new UsersService();
        $userEntity = $userService->findRandUser();
        $friendUserEntity = $userService->findRandUser();


        $userId = $userEntity->getId();
        $url = '/users/' . $userId . '/friends';
        $params = ['friend_id' => $friendUserEntity->getId()];


        $response = $this->load($url, 'POST', $params);
        $this->assertSuccessResponse($response);

        $this->setCrossTestParam("user_id", $userId);
        $this->setCrossTestParam("friend_id", $friendUserEntity->getId());
    }

    public function testGetUserFriendsRequests()
    {
        $userId = $this->getCrossTestParam('user_id');

        $url = '/users/' . $userId . '/friends-requests';

        $response = $this->load($url);
        $this->assertSuccessResponse($response);
        $this->assertArrayHasKey("user", $response['result'], 'User was not found in result');
        $this->assertArrayHasKey("user_id", $response['result']['user'], 'User id was not found in result');
        $this->assertArrayHasKey(
            'friends_requests', $response['result']['user'], 'Friends list was not found in result'
        );

        $expectedFriendId = $this->getCrossTestParam('friend_id');
        $this->assertEquals($userId, $response['result']['user']['user_id']);
        $this->assertGreaterThan(0, $response['result']['user']['friends_requests']);
        $this->assertContains($expectedFriendId, $response['result']['user']['friends_requests']);
    }

    public function testAcceptFriendship()
    {

        $userId = $this->getCrossTestParam("user_id");
        $friendId = $this->getCrossTestParam("friend_id");

        $url = '/users/' . $userId . '/friends/' . $friendId;
        $params = ['accept' => true];


        $response = $this->load($url, 'PUT', $params);
        $this->assertSuccessResponse($response);

    }

    public function testGetUserFriends()
    {
        $userId = $this->getCrossTestParam('user_id');
        $friendId = $this->getCrossTestParam('friend_id');


        $url = '/users/' . $userId . '/friends';

        $response = $this->load($url);

        $this->assertSuccessResponse($response);

        $this->assertArrayHasKey("user", $response['result'], 'User was not found in result');
        $this->assertArrayHasKey("user_id", $response['result']['user'], 'User id was not found in result');
        $this->assertArrayHasKey('friends', $response['result']['user'], 'Friends list was not found in result');
        $this->assertGreaterThan(0, $response['result']['user']['friends']);
        $this->assertContains($friendId, $response['result']['user']['friends']);
        $this->assertEquals($userId, $response['result']['user']['user_id']);
    }


    public function testDeclineFriendship()
    {

        $userService = new UsersService();
        $userEntity = $userService->findRandUser();
        $friendUserEntity = $userService->findRandUser();


        $userId = $userEntity->getId();
        $friendId = $friendUserEntity->getId();

        $url = '/users/' . $userId . '/friends';
        $params = ['friend_id' => $friendId];

        $response = $this->load($url, 'POST', $params);

        $this->assertSuccessResponse($response);


        $url = '/users/' . $userId . '/friends/' . $friendId;

        $params = ['accept' => false];

        $response = $this->load($url, 'PUT', $params);
        $this->assertSuccessResponse($response);

        $url = '/users/' . $userId . '/friends';

        $response = $this->load($url);

        $this->assertSuccessResponse($response);

        $this->assertArrayHasKey("user", $response['result'], 'User was not found in result');
        $this->assertArrayHasKey("user_id", $response['result']['user'], 'User id was not found in result');
        $this->assertArrayHasKey('friends', $response['result']['user'], 'Friends list was not found in result');
        $this->assertNotContains($friendId, $response['result']['user']['friends']);
        $this->assertEquals($userId, $response['result']['user']['user_id']);


        $url = '/users/' . $userId . '/friends-requests';

        $response = $this->load($url);

        $this->assertSuccessResponse($response);

        $this->assertArrayHasKey("user", $response['result'], 'User was not found in result');
        $this->assertArrayHasKey("user_id", $response['result']['user'], 'User id was not found in result');
        $this->assertArrayHasKey(
            'friends_requests', $response['result']['user'], 'Friends list was not found in result'
        );
        $this->assertNotContains($friendId, $response['result']['user']['friends_requests']);
        $this->assertEquals($userId, $response['result']['user']['user_id']);
    }
}