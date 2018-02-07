<?php
/**
 * Created by PhpStorm.
 * User: hooklife
 * Date: 2017/1/12
 * Time: 12:00
 */
namespace  Hooklife\ThinkphpWechat;

use EasyWeChat\Foundation\Application;
use think\Config;
/**
 * Class Wechat  微信类
 */

class TPwechat
{
    protected static $app;

    public static function app()
    {
        if (!isset(self::$app)) {
            $options = Config::get('wechat');
//            var_dump($options);die;
            self::$app = new Application($options);
        }
        return self::$app;
    }

    public static function __callStatic($name, $arguments)
    {
        return self::app()->$name;
    }
    /*public function serve(){
        $server = self::$app->server;
        $server->setMessageHandler(function ($message) {
            　　return '你好';
        });
        $server->serve()->send();
    }*/
    public function oauth(){
        $oauth = self::$app->oauth;
        $user = $oauth->user();
        session('wechat_user',$user->toArray());
        $targetUrl = session('target_url');
        $this->redirect($targetUrl);
    }
}