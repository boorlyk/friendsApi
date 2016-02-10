<?php
/**
 * Created by PhpStorm.
 * User: boorlyk
 * Date: 2/9/16
 * Time: 12:00 PM
 */

namespace FriendsApi\entities;


class UserEntity
{

    protected $id;
    protected $name;
    protected $friends;
    protected $friendRequests;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getFriends()
    {
        return $this->friends;
    }

    /**
     * @param mixed $friends
     */
    public function setFriends($friends)
    {
        $this->friends = $friends;
    }

    /**
     * @return mixed
     */
    public function getFriendRequests()
    {
        return $this->friendRequests;
    }

    /**
     * @param mixed $friendRequests
     */
    public function setFriendRequests($friendRequests)
    {
        $this->friendRequests = $friendRequests;
    }
}