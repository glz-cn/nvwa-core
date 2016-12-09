<?php
/**
 * Created by IntelliJ IDEA.
 * User: r
 * Date: 2016/12/6
 * Time: 下午8:51
 */

namespace NvwaCommon\Misc;


class GlzForumApi
{
    protected $forumConfig;
    protected $_usersCache = array();

    public function __construct()
    {
        $this->forumConfig = array(
            'db_siteid' => env("db_siteid"),
            'db_sitehash' => env("db_sitehash"),
            'db_cookiepre' => env('db_cookiepre'),
            'db_hash' => env('db_hash'),
            'db_ifsafecv' => intval(env('db_ifsafecv')),
        );
    }

    public function getPwCookieUserId()
    {
        $pwUserCookie = $this->PwGetCookie('winduser');
        if (!$pwUserCookie) {
            return false;
        }
        $cookieUser = explode("\t", addslashes($this->PwStrCode($this->PwGetCookie('winduser'), 'DECODE')));
        return intval($cookieUser[0]);
    }
    
    public function PwGetCookie($cookieName)
    {
        $cookiePre = $this->PwCookiePre();
        if (isset($_COOKIE[$cookiePre . '_' . $cookieName])) {
            return $_COOKIE[$cookiePre . '_' . $cookieName];
        } else {
            return "";
        }
    }

    public function PwCookiePre()
    {
        $config = $this->getConfig();
        return ($config['db_cookiepre']) ? $config['db_cookiepre'] : substr(md5($config['db_sitehash']), 0, 5);
    }

    public function getConfig($key = '')
    {
        if ($this->forumConfig === null) {
            throw new \Exception("init failed.");
        }
        return $key ? $this->forumConfig[$key] : $this->forumConfig;
    }

    public function PwStrCode($string, $action = 'ENCODE')
    {
        if ($action != 'ENCODE') {
            $string = base64_decode($string);
        }
        $code = '';
        $key = substr(md5($_SERVER['HTTP_USER_AGENT'] . $this->getConfig('db_hash')), 8, 18);
        $keyLen = strlen($key);
        $strLen = strlen($string);
        for ($i = 0; $i < $strLen; $i++) {
            $k = $i % $keyLen;
            $code .= $string[$i] ^ $key[$k];
        }
        return ($action != 'DECODE' ? base64_encode($code) : $code);
    }

    /**
     * 加密密码
     *
     * @global array $pwServer
     * @global string $db_hash
     * @param string $pwd 密码
     * @return string
     */
    public function PwPwdCode($pwd)
    {
        return md5($_SERVER['HTTP_USER_AGENT'] . $pwd . $this->getConfig('db_hash'));
    }

    /**
     * 获取客户端唯一hash
     *
     * @param string $str 附加信息
     * @param string $app
     * @return string
     */
    public function PwGetVerify($str, $app = null)
    {
        empty($app) && $app = $this->getConfig('db_siteid');
        return substr(md5($str . $app . $_SERVER['HTTP_USER_AGENT']), 8, 8);
    }

}