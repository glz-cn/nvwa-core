# nvwaCommon.NvwaApi

负责远程调用相关的接口


# nvwaCommon.NvwaApi.NvwaUic

负责远程取回用户信息相关的接口

## 服务器端使用方法
##### 1. 引入NvwaUic
执行``` composer require glz/nvwa-common:dev-master ```
##### 2. 设置app secret

修改.env文件,增加内容:

```ini
REMOTE_USER_SECRET=gdsdddddd333
```

##### 3. 给某个controller添加回调方法:
```php
    /**
    * 本方法服务于URL:remoteUser/login
    */
  public function login(Request $request){
        $currentUser = $request->user();
        if($currentUser){
            $serverSide = new ServerSide();
            return $serverSide->redirectToClientWithRemoteUserInfo($request);
        }else{
            return redirect("/auth/login?redirectTo=".urlencode($request->url()."?".$request->getQueryString()));
        }
        
    }
```
    客户端检查到用户未登陆时，会引导用户到服务器

## 客户端使用方法:

##### 1. 引入NvwaUic

执行``` composer require glz/nvwa-common:dev-master ```

##### 2. 设置中间件

修改```app/Http/Kernel.php```,修改变量```$routeMiddleware```，给这个成员变量添加一项:```'remoteUser'=> nvwaCommon.NvwaApi.NvwaUic\RemoteUserMiddleware::class```

##### 3. 在路由中引用中间件:

在routes里，引用这个middleware，示例如下:

```php
    Route::group(["middleware"=>'remoteUser'],function(){
        Route::get("/home",'\App\Http\Controllers\HomeController@home');
        //....及其他的urls的处理逻辑;
    });
```

##### 4.设置认证服务器的地址,密钥:

请在.env中设置这一项:

```ini
REMOTE_USER_SERVER_LOGIN_URL=http://dev.local.com/uic/remote/tryLogin
REMOTE_USER_APP=finance
REMOTE_USER_SECRET=gdsdddddd333
```
  注意,密钥和用户认证的服务端应该相同。

##### 5.在您的代码中取得用户信息

只需要调用```RemoteUser::getCurrentUser()```即能得到RemoteUser的实例（存储了当前存入的用户信息);

示例代码:
```php
    class HomeController extends Controller {
        public  function home(Request $request){
            $user =  RemoteUser::getCurrentUser();
            return $user->getId().":".$user->getName().":".$user->getEmail();
        }
    }
```



## 客户端的临时方法

 有时没有联网,或是服务器端有故障 ,怎么办呢?
 也有办法,让客户端并不实时到服务器去校验.
 步聚如下:
 
 ##### 1. 确保引入了nvwa-common:
 
```
composer require glz/nvwa-common:dev-master
```

##### 2. 设置.env文件,以开启假用户模式:
```
REMOTE_USER_FAKE_MODE=1
REMOTE_USER_FAKE_ID=2
REMOTE_USER_FAKE_NAME=xiaosi
REMOTE_USER_FAKE_EMAIL=xiaosi@gmail.com
```

好了,现在在你的代码里启用 ``` RemoteUser::getCurrentUser() ``` 就总是能返回一个用户对象了.
