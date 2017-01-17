<?php
/**
 * Created by PhpStorm.
 * User: laowang
 * Date: 2017/1/17
 * Time: 14:49
 */
namespace app\home\controller;
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
}