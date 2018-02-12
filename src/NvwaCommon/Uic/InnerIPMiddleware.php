<?php
/**
 * Created by IntelliJ IDEA.
 * User: r
 * Date: 16/7/15
 * Time: 下午12:13
 */

namespace NvwaCommon\Uic;

use Closure;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;


class InnerIPMiddleware
{

    /**
     * RemoteUserMiddleware constructor.
     */
    public function __construct()
    {

    }


    public function isInnerIP($ip){
        //过滤非外网IP;
        $res = filter_var($ip,FILTER_VALIDATE_IP,FILTER_FLAG_NO_PRIV_RANGE|FILTER_FLAG_NO_RES_RANGE);
        if($res !== $ip){
            return true;
        }else{
            return false;
        }
    }




    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {


        $http_ip = $request->server("HTTP_IP");
        $remote_adr  = $request->server("REMOTE_ADDR");
        $ip = $http_ip;
        if(empty($ip)){
            $ip = $remote_adr;
        }
        if(!$this->isInnerIP($ip)){
            return ["code"=>403,"msg"=>"不对外提供服务"];
        }

        return $next($request);
    }

    /**
     * 解密token里的信息,拼装得到RemoteUser对象;
     * @param string $token
     */
    protected function buildRemoteUser($token)
    {
        $remoteUserInfo = JWT::decode($token, $this->secret, array('HS256'));
        $remoteUser = new RemoteUser();
        $remoteUser->setId($remoteUserInfo->id);
        $remoteUser->setName($remoteUserInfo->name);
        $remoteUser->setEmail($remoteUserInfo->email);
        if (isset($remoteUserInfo->roleNames)) {
            $remoteUser->setRoleNames($remoteUserInfo->roleNames);
        }
        RemoteUser::setCurrentUser($remoteUser);
    }

    /**
     * 将用户重定向到某一个特定的地址
     *
     * @param Request $request
     * @param string $serverSideLoginUrl
     * @param string $app
     * @return mixed
     */
    protected function redirectToServerSide(Request $request)
    {
        return redirect($this->serverSide . '?' . ServerSide::$redirectToArgumentName . '=' . urlencode($request->url()) . '&' . ServerSide::$appArgumentName . '=' . $this->app);
    }
}