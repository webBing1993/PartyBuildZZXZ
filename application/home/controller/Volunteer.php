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

class Volunteer extends Base{
    /*
     * 服务团队
     */
    public function team(){
        return $this->fetch();
    }
    /*
     * 服务订单
     */
    public function order(){
        return $this->fetch();
    }
    /*
     * 志愿招募
     */
    public function recruit(){
        return $this->fetch();
    }
    /*
     * 服务团队详情页
     */
    public function teamdetail(){
        return $this->fetch();
    }
    /*
     * 服务订单详情页
     */
    public function orderdetail(){
        return $this->fetch();
    }
    /*
     * 志愿招募详情页
     */
    public function recruitdetail(){
        return $this->fetch();
    }
}
