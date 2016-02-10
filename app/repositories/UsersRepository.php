<?php
namespace FriendsApi\repositories;


use FriendsApi\entities\UserEntity;
use FriendsApi\exceptions\BadParamException;
use FriendsApi\exceptions\UserNotFoundException;
use Phalcon\Di;
use Phalcon\Exception;

class UsersRepository
{

    const USER_INCREMENT_KEY = 'incr_user_id';
    const USER_FRIEND_REQUESTS_KEY = 'friendRequests:';
    const USER_FRIENDS_KEY = 'userFriends:';
    const USER_KEY = 'users:';

    /**
     * @var \Redis
     */
    protected static $connection = null;

    /**
     * @param \Redis $connection
     */
    public function setConnection($connection)
    {
        static::$connection = $connection;
    }

    /**
     * @return \Redis
     */
    public function getConnection()
    {
        if (static::$connection === null) {
            static::$connection = Di::getDefault()->getShared('redis');
        }
        return static::$connection;
    }

    /**
     * @param $name
     *
     * @return int
     * @throws \RedisException
     */
    public function newUser($name)
    {
        $redisCli = $this->getConnection();
        $userId = $redisCli->incr(self::USER_INCREMENT_KEY);

        $res = $redisCli->hMset(self::USER_KEY . $userId, ['name' => $name]);

        if ($res !== true) {
            throw new \RedisException('User was not saved with error:' . $redisCli->getLastError());
        }
        return $userId;
    }

    /**
     * @param $userId
     *
     * @return UserEntity
     * @throws UserNotFoundException
     */
    public function findUserById($userId)
    {
        $redisCli = $this->getConnection();
        $user = $redisCli->hMGet('users:' . $userId, ['name']);

        if (!$user) {
            throw new UserNotFoundException("User {$userId} was not found");
        }

        $userEntity = new UserEntity();
        $userEntity->setId($userId);
        $userEntity->setName($user['name']);
        return $userEntity;
    }

    public function friendRequest($userId, $friendId)
    {
        $redisCli = $this->getConnection();
        $res = $redisCli->sadd(self::USER_FRIEND_REQUESTS_KEY . $userId, $friendId);

        if ($res !== 1) {
            throw new \RedisException('Friend Reqeust was not sent with error:' . $redisCli->getLastError());
        }
    }

    public function approveFriendRequest(
        $userId, $friendId
    ) {
        $redisCli = $this->getConnection();

        if ($redisCli->sIsMember(self::USER_FRIEND_REQUESTS_KEY . $userId, $friendId)) {
            $redisCli->sAdd(self::USER_FRIENDS_KEY . $userId, $friendId);
            $redisCli->sAdd(self::USER_FRIENDS_KEY . $friendId, $userId);
            $this->deleteFriendRequest($userId, $friendId);
        }
    }


    public function declineFriendRequest(
        $userId, $friendId
    ) {
        $this->deleteFriendRequest($userId, $friendId);
    }

    private function deleteFriendRequest(
        $userId, $friendId
    ) {
        $redisCli = $this->getConnection();

        if ($redisCli->sIsMember(self::USER_FRIEND_REQUESTS_KEY . $userId, $friendId)) {
            $redisCli->sRem(self::USER_FRIEND_REQUESTS_KEY . $userId, $friendId);
        }
    }

    public function findFriendRequestList($userId)
    {
        $friendRequestsList = $this->getConnection()->sMembers(self::USER_FRIEND_REQUESTS_KEY . $userId);
        return $friendRequestsList;
    }

    public function findFriendsList($userId)
    {
        $friendsList = $this->getConnection()->sMembers(self::USER_FRIENDS_KEY . $userId);
        return $friendsList;
    }

    /**
     * @param $userIds
     *
     * @return array
     * @throws BadParamException
     */
    public function findFriendsListByUserIds($userIds)
    {
        if (!$userIds || !is_array($userIds)) {
            throw new BadParamException('User ids is not valid:' . var_export($userIds, 1));
        }

        $redisCli = $this->getConnection();
        $keys = [];
        foreach ($userIds as $key) {
            $keys[] = self::USER_FRIENDS_KEY . $key;
        }

        $members = call_user_func([$redisCli, 'sUnion'], $keys);
        return $members;
    }

    /**
     * @return integer
     */
    public function findRandomUserId()
    {
        $count =  $this->getConnection()->get(self::USER_INCREMENT_KEY);
        return rand(0, $count);
    }
}