<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2016/11/2
 * Time: 13:21
 */
namespace app\home\controller;

use think\Controller;

/**
 * 红领学院
 * Class College
 * @package app\home\controller
 */
class College extends Base{
    /**
     * 主页
     */
    public function index() {

        return $this->fetch();
    }

    /**
     * 导师页面
     */
    public function tutor() {

        return $this->fetch();
    }

    /**
     * 导师详情
     */
    public function tutordetail() {

        return $this->fetch();
    }

    /**
     * 通知详情
     */
    public function tutornotice() {

        return $this->fetch();
    }

    /**
     * 通知列表
     */
    public function tutornoticelist() {

        return $this->fetch();
    }

    /**
     * 红领带动详情
     */
    public function leaddetail() {

        return $this->fetch();
    }

    /**
     * 红领带动列表
     */
    public function leadlist() {

        return $this->fetch();
    }

    /**
     * 红领论坛通知
     */
    public function forumnotice() {

        return $this->fetch();
    }

    /**
     * 红领论坛详情
     */
    public function forum() {
        
        return $this->fetch();
    }
    
    /**
     * 情况详情
     */
    public function forumdetail() {
        
        return $this->fetch();
    }
    
    
}