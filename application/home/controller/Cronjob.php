<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/2/17
 * Time: 16:28
 */

namespace app\home\controller;


use think\Controller;
use app\home\model\Years;
use com\wechat\TPQYWechat;
use think\Config;

class Cronjob extends Controller {
    /**
     * 每天定时发送  答题提醒
     */
    public function automatic_push(){
        //推送消息的详情
        $Wechat = new TPQYWechat(Config::get('party'));
        $title = '"每日一课"君已经等候您多时了...';
        $content = "休息一下,去答个题吧";
        $path = "http://dqpb.0571ztnet.com/home/images/user/relax.jpg";//图片链接
        $url = "http://dqpb.0571ztnet.com/home/constitution/course";  //答题页面链接
        $send = array(
            "articles" => array(
                array(
                    "title" => $title,
                    "description" => $content,
                    "url" => $url,
                    "picurl" => $path,
                )
            )
        );
        //发送
        $message = array(
//                'touser' => '18768112486',
           'touser' =>"@all",
            "msgtype" => 'news',
            "agentid" => 6,
            "news" => $send,
            "safe" => "0"
        );
        $Wechat->sendMessage($message);
    }
}