<?php
/**
 * Created by IntelliJ IDEA.
 * User: r
 * Date: 16/7/15
 * Time: 上午11:36
 */

namespace NvwaCommon\Uic;


use Firebase\JWT\JWT;
use Illuminate\Http\Request;

/**
 * @todo 根据app参数 的不同,调用不同的secret来加密;
 * Class ServerSide
 * @package NvwaCommon\Uic
 */
class ServerSide
{
    public static $tokenArgumentName = "uicToken";
    public static $redirectToArgumentName = "redirectTo";
    public static $appArgumentName = "app";
    public $secret;

    /**
     * ServerSide constructor.
     */
    public function __construct()
    {
        $this->secret = env("REMOTE_USER_SECRET");
    }


    /**
     * 重定向到客户端应用指定的地址,并且附带上用户信息串;
     * @param Request $request
     * @return mixed
     */
    public function redirectToClientWithRemoteUserInfo(Request $request)
    {
        $url = $this->urlToClientSite($request, $this->getUicToken($request, $this->secret));
        return redirect($url);
    }

    /**
     *  构造一个返回到客户端网站的URL地址串,这个串将会带有uicToken参数,这个参数里存储了用户信息;
     *
     * @param Request $request
     * @param string $token
     * @return string
     */
    public  function urlToClientSite(Request $request,string $token){
        $returnUrl = $request->input(self::$redirectToArgumentName);

        if(strpos($returnUrl,"?")>0){
            if(strpos($returnUrl,"?")==(strlen($returnUrl)-1)){
                return $returnUrl . self::$tokenArgumentName . "=" . urlencode($token);
            }else{
                return $returnUrl . "&" . self::$tokenArgumentName . "=" . urlencode($token);
            }
        }else{
            return $returnUrl . "?" . self::$tokenArgumentName . "=" . urlencode($token);
        }
    }

    /**
     * 将用户的信息传给
     *
     * @param Request $request
     * @param string $secret
     * @return string
     */
    public  function getUicToken(Request $request,string $secret){
        $currentUser = $request->user();
        if($currentUser){
            $roles = [];
            foreach($currentUser->roles as $role){
                $roles[] = $role->english_name;
            }
            $remoteUser = new RemoteUser();
            $remoteUser->setId($currentUser->id);
            $remoteUser->setEmail($currentUser->email);
            $remoteUser->setName($currentUser->name);
            $remoteUser->setRoleNames($roles);
            return JWT::encode($remoteUser,$secret);
        }else{
            return "";
        }
    }
}