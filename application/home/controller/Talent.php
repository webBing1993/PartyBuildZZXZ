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

class Talent extends Base{
    /*
     * 人才创业
     */
    public function index(){
        return $this->fetch();
    }
    /*
     *人才创业列表
     */
    public function lists(){
        return $this->fetch();
    }
    /*
     *人才创业详情页
     */
    public function detail(){
        return $this->fetch();
    }
}
