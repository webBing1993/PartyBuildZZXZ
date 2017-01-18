<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2016/11/2
 * Time: 13:21
 */
namespace app\home\controller;
use app\home\model\WechatTest;
use app\home\model\WechatUser;

class League extends Base{
    /*
     * 地信红盟主页
     */
    public function index(){
        return $this->fetch();
    }
    /*
     * 地信红盟详情页
     */
    public function detail(){
        return $this->fetch();
    }
    /*
     * 活动安排更多页
     */
    public function lists(){
        return $this->fetch();
    }

}
