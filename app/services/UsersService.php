<?php
/**
 * Created by PhpStorm.
 * User: boorlyk
 * Date: 2/9/16
 * Time: 12:07 AM
 */

namespace FriendsApi\services;

use FriendsApi\repositories\UsersNeo4jRepository;
use FriendsApi\repositories\UsersRepository;

class UsersService
{

    /**
     * @var UsersRepository
     */
    private $usersRepository;

    /**
     * @return UsersRepository
     */
    public function getUsersRepository()
    {
        if ($this->usersRepository === null) {
//            $this->usersRepository = new UsersNeo4jRepository();
            $this->usersRepository = new UsersRepository();
        }
        return $this->usersRepository;
    }

    /**
     * @param UsersRepository $usersRepository
     */
    public function setUsersRepository($usersRepository)
    {
        $this->usersRepository = $usersRepository;
    }


    public function addUser($name)
    {
        $userId = $this->getUsersRepository()->newUser($name);
        return $this->getUsersRepository()->findUserById($userId);
    }

    public function findUser($userId)
    {
        $this->getUsersRepository()->findUserById($userId);
    }


    public function sendFriendRequest($userId, $friendId)
    {
        $this->getUsersRepository()->friendRequest($userId, $friendId);
    }

    public function friendsList($userId)
    {
        return $this->getUsersRepository()->findFriendsList($userId);
    }

    public function friendsGraph($userId, $nestingLevel = 1)
    {
        $friendsList = $this->getUsersRepository()->findFriendsList($userId);

        $levels = $this->friendsCircle($friendsList, $nestingLevel, $userId);

        return $levels;
    }



    private function friendsCircle($userIds, $levelCount = 1, $excludeUserId = 0, $levels = [], $level = 0)
    {
        if ($level == $levelCount || !count($userIds)) {
            return $levels;
        }

        $levels[$level] = [
            'friends' => $userIds,
            'level'   => $level + 1,
            'count'   => count($userIds)
        ];


        $nextLevelUserIds = $this->getUsersRepository()->findFriendsListByUserIds($userIds);

        $nextLevelUserIds = array_diff($nextLevelUserIds, [$excludeUserId]);
        foreach ($levels as $levelItem) {
            $nextLevelUserIds = array_diff($nextLevelUserIds, $levelItem['friends']);
        }

        $levels = $this->friendsCircle(
            array_values($nextLevelUserIds), $levelCount, $excludeUserId, $levels, $level + 1
        );
        return $levels;
    }

    public function friendRequestsList($userId, $limit = 1000, $offset = 0)
    {
        return $this->getUsersRepository()->findFriendRequestList($userId);
    }

    public function acceptFriendRequest($userId, $friendId, $accept = true)
    {
        if ($accept === true) {
            $this->getUsersRepository()->approveFriendRequest($userId, $friendId);
        } else {
            $this->getUsersRepository()->declineFriendRequest($userId, $friendId);
        }
    }

    /**
     * @return \FriendsApi\entities\UserEntity
     * @throws \FriendsApi\exceptions\UserNotFoundException
     */
    public function findRandUser()
    {
        $userId = $this->getUsersRepository()->findRandomUserId();

        return $this->getUsersRepository()->findUserById($userId);
    }
}