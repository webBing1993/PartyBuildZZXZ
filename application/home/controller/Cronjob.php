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
        $hour = date("G",time());  //获取当前小时
        $Years = new Years();
        $dif = time()-7200;
        $map = array(
            'type' => 2,
            'create_time' => array('exp',"> $dif")
        );
        $Obj = $Years->where($map)->find();  //获取已经推送的数据
        $flag = 0;
        if(empty($Obj)){
            $flag = 1;
        }
        if($flag == 1) {
//        if(($hour >= 9 && $hour <= 11) && ($flag == 1) || ($hour >= 16 && $hour <= 18) && ($flag == 1)){
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
            $res = $Wechat->sendMessage($message);
            if($res){
                $Years = new Years();
                $big = time()-12*60*60;
                $con = array(
                    'type' =>2,
                    'create_time' => array('exp',"< $big")
                );
                $Years->where($con)->delete();  // 删除已经过期的数据
                //存储到数据库
                $Years->type = 2;
                $Years->save();
            }
        }
//        }
    }
}