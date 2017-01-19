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

class Topic extends Base{
    /*
     * 专题讨论/党性体验主页
     */
    public function index(){
        return $this->fetch();
    }

    /*
     * 详情页
     */
    public function detail(){
        return $this->fetch();
    }
}
