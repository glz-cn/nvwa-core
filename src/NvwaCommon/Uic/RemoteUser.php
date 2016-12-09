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
            return $fakeUser;
        }
        return self::$currentUser;
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


}