<?php
/**
 * Created by IntelliJ IDEA.
 * User: r
 * Date: 16/7/15
 * Time: 上午11:35
 */

namespace NvwaCommon\Uic;


class RemoteUser
{
    /**
     * @var RemoteUser
     */
    public static $currentUser;
    /**
     * @var integer
     */
    public $id;
    /**
     * @var string
     */
    public $email;
    /**
     * @var string
     */
    public $name;

    public $roleNames = [];


    public $sid = "";
    /**
     * @return RemoteUser
     */
    public static function getCurrentUser()
    {
        if (env("REMOTE_USER_FAKE_MODE")) {
            $fakeUser = new RemoteUser();
            if ($id = env("REMOTE_USER_FAKE_ID")) {
                $fakeUser->setId($id);
            } else {
                $fakeUser->setId(1);
            }
            if ($name = env("REMOTE_USER_FAKE_NAME")) {
                $fakeUser->setName($name);
            } else {
                $fakeUser->setName("Steve.Jobs");
            }
            if ($email = env("REMOTE_USER_FAKE_EMAIL")) {
                $fakeUser->setEmail($email);
            } else {
                $fakeUser->setEmail("steve.jobs@apple.com");
            }
            $roleNameString = env("REMOTE_USER_FAKE_ROLES");
            $roleNames = explode(",",$roleNameString);
            $fakeUser->setRoleNames($roleNames);
            return $fakeUser;
        }
        return self::$currentUser;
    }


    public function hasRole($roleName){
        return in_array($roleName, $this->getRoleNames());
    }

    /**
     * @param RemoteUser $currentUser
     */
    public static function setCurrentUser($currentUser)
    {
        self::$currentUser = $currentUser;
    }

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
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
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
     * @return array
     */
    public function getRoleNames()
    {
        return $this->roleNames;
    }

    /**
     * @param array $roleNames
     */
    public function setRoleNames($roleNames)
    {
        $this->roleNames = $roleNames;
    }

    /**
     * @return string
     */
    public function getSid()
    {
        return $this->sid;
    }

    /**
     * @param string $sid
     */
    public function setSid($sid)
    {
        $this->sid = $sid;
    }




}