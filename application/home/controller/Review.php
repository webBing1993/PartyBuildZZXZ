<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2016/11/2
 * Time: 13:21
 */
namespace app\home\controller;
use app\home\model\Department;
use app\home\model\WechatTest;
use app\home\model\WechatUser;

class Review extends Base{
    /*
     * 未审核
     */
    public function reviewlist(){
        return $this->fetch();
    }
    /*
     * 已审核
     */
    public function passlist(){
        return $this->fetch();
    }

}
