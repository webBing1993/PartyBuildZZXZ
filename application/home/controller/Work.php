<?php
/**
 * Created by PhpStorm.
 * User: laowang
 * Date: 2017/1/17
 * Time: 14:49
 */
namespace app\home\controller;
use app\home\model\Notice;
/*
 * 两学一做  党史 通讯录
 */

class Work extends Base{
    /*
     * 党史
     */
    public function dangshi(){
        return  $this->fetch();
    }
    /*
     * 通讯录
     */
    public function structurelist(){
        return $this->fetch();
    }
    /*
     * 通讯录详情
     */
    public function structuredetail(){
        $party = input('party');
        $this->assign('party',$party);
        return $this->fetch();
    }
    /*
     * 党员签到 活动列表
     */
    public function sign(){
        $map1 = ['meet_time' => ['lt',time()]];
        $map2 = ['meet_time' => ['egt',time()]];
        $order = 'meet_time desc';
        $go = Notice::where($map2)->order($order)->limit(7)->select();
        $now = Notice::where($map1)->order($order)->limit(7)->select();

        $this->assign('go',$go);  // 已经结束
        $this->assign('now',$now);  // 进行中

        return $this->fetch();
    }
}