<?php
namespace FriendsApi\repositories;


use Everyman\Neo4j\Client;
use Everyman\Neo4j\Cypher\Query;
use Everyman\Neo4j\Query\Row;
use FriendsApi\entities\UserEntity;
use FriendsApi\exceptions\RepositoryException;
use FriendsApi\exceptions\UserNotFoundException;
use Phalcon\Di;
use Phalcon\Exception;

class UsersNeo4jRepository
{
    /**
     * @var Client
     */
    protected static $connection = null;

    /**
     * @param Client $connection
     */
    public function setConnection($connection)
    {
        static::$connection = $connection;
    }

    /**
     * @return Client
     */
    public function getConnection()
    {
        if (static::$connection === null) {
            static::$connection = Di::getDefault()->getShared('Neo4jClient');
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

        $neo4jClient = $this->getConnection();
        $cypher = 'CREATE (friendsApi:user { name : {name}}) return id(friendsApi)';
        $query = new Query($neo4jClient, $cypher, ['name' => $name]);
        $result = $query->getResultSet();

        $userId = $result[0][0];
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
        $neo4jClient = $this->getConnection();

        $queryStr
            = 'MATCH friendsApi WHERE id(friendsApi) = {userId} return friendsApi.name as name, id(friendsApi) as id';
        $query = new Query($neo4jClient, $queryStr, ['userId' => $userId]);
        $rows = $query->getResultSet();
        if (!$rows->count()) {
            throw new UserNotFoundException("User {$userId} was not found");
        }

        $userEntity = new UserEntity();
        $userEntity->setId($rows[0][1]);
        $userEntity->setName($rows[0][0]);
        return $userEntity;
    }

    public function friendRequest($userId, $friendId)
    {
        $queryStr
            = "MATCH (user),(friend)
            WHERE id(user) = " . (int)$userId . " AND id(friend) = " . (int)$friendId . "
            CREATE UNIQUE (user)-[r:FRIEND_REQUEST]->(friend)
            RETURN r";

        $res = $this->queryResult(
            $queryStr, [
                'user_id'   => $userId,
                'friend_id' => $friendId
            ]
        );

        return count($res);
    }

    public function approveFriendRequest(
        $userId, $friendId
    ) {

        $queryStr
            = "MATCH user-[r:FRIEND_REQUEST]->friend
            WHERE id(user) = " . (int)$userId . " AND id(friend) = " . (int)$friendId . "
            CREATE (user)-[f:FRIEND]->(friend)
            RETURN f";

        $res = $this->queryResult($queryStr);

        $this->deleteFriendRequest($userId, $friendId);
    }


    public function declineFriendRequest(
        $userId, $friendId
    ) {
        $this->deleteFriendRequest($userId, $friendId);
    }

    private function deleteFriendRequest(
        $userId, $friendId
    ) {
        $queryStr
            = "MATCH user-[r:FRIEND_REQUEST]->friend
            WHERE id(user) = " . (int)$userId . " AND id(friend) = " . (int)$friendId . "
            DELETE r
            ";

        $res = $this->queryResult($queryStr);
        return;
    }

    public function findFriendRequestList($userId)
    {
        $queryStr
            = "MATCH user-[r:FRIEND_REQUEST]->friend
            WHERE id(user) = " . (int)$userId . "
            return  id(friend) as id,friend.name";

        $res = $this->queryResult($queryStr);
        $friendRequestsList = [];
        foreach ($res as $row) {
            $friendRequestsList [] = $row[0];
        }

        return $friendRequestsList;
    }

    public function findFriendsList($userId)
    {
        $queryStr
            = "MATCH user-[f:FRIEND]-friend
            WHERE id(user) = " . (int)$userId . "
            return  id(friend) as id,friend.name";

        $res = $this->queryResult($queryStr);
        $friendRequestsList = [];
        foreach ($res as $row) {
            $friendRequestsList [] = $row[0];
        }

        return $friendRequestsList;
    }

    /**
     * @param $userIds
     *
     * @return array
     * @throws BadParamException
     */
    public function findFriendsListByUserIds($userIds)
    {

        $queryStr
            = "MATCH user-[f:FRIEND]-friend
            WHERE id(user) IN [" . implode(',', $userIds) . "]
            return  id(friend) as id,friend.name";

        $res = $this->queryResult($queryStr);
        $friendRequestsList = [];
        foreach ($res as $row) {
            $friendRequestsList [] = $row[0];
        }

        return $friendRequestsList;
    }

    /**
     * @return integer
     */
    public function findRandomUserId()
    {
        $queryStr = 'MATCH friendsApi  with friendsApi, rand() as r return id(friendsApi) as id ORDER BY r LIMIT 1;';

        $res = $this->queryResult($queryStr);

        $userId = $res[0][0];
        return $userId;
    }

    private function queryResult($queryStr, $params = [])
    {
        $neo4jCLient = $this->getConnection();

        $query = new Query($neo4jCLient, $queryStr, $params);
        try {
            $res = $query->getResultSet();
        } catch (\Exception $e) {
            throw new RepositoryException('Trapped error while executing query:' . $e->getMessage());
        }

        $result = [];
        foreach ($res as $row) {
            $result[] = $row;
        }

        return $result;
    }
}