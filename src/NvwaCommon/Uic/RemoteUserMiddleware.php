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
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Http\Request;


class RemoteUserMiddleware
{
    public static $sessionName = "uicUid";
    public $serverSide = "";
    public $secret = "";
    public $app = "";
    /**
     * RemoteUserMiddleware constructor.
     */
    public function __construct()
    {
        $this->serverSide=env("REMOTE_USER_SERVER_LOGIN_URL");
        $this->secret = env("REMOTE_USER_SECRET");
        $this->app = env("REMOTE_USER_APP");
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
        //如果开启了假用户模式,直接返回了
        if (env("REMOTE_USER_FAKE_MODE")) {
            return $next($request);
        }
        $uicToken = $request->input(ServerSide::$tokenArgumentName);
        if ($uicToken) {
            $request->session()->set(self::$sessionName, $uicToken);
        } else {
            $uicToken = $request->session()->get(self::$sessionName);
        }
        if($uicToken){
            try {
                $this->buildRemoteUser($uicToken);
            } catch (\Exception $e) {
                return $this->redirectToServerSide($request);
            }
        }
        if(!RemoteUser::getCurrentUser()){
            return $this->redirectToServerSide($request);
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