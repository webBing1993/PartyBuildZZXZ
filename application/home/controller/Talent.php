<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2016/11/2
 * Time: 13:21
 */
namespace app\home\controller;

use think\Controller;
use app\home\model\Talent as TalentModel;

class Talent extends Base{
    /*
     * 人才创业
     */
    public function index(){
        $talentModel = new TalentModel();
        //政策解读
        $map1 = array(
            'type' => 1,
            'status' => 1,
        );
        $list1 = $talentModel->where($map1)->order('create_time desc')->limit(2)->select();
        $this->assign('list1',$list1);

        //申请流程
        $map2 = array(
            'type' => 2,
            'status' => 1,
        );
        $list2 = $talentModel->where($map2)->order('create_time desc')->limit(2)->select();
        $this->assign('list2',$list2);

        //创业支持
        $map3 = array(
            'type' => 3,
            'status' => 1,
        );
        $list3 = $talentModel->where($map3)->order('create_time desc')->limit(2)->select();
        $this->assign('list3',$list3);
        return $this->fetch();
    }
    /*
     *人才创业列表
     */
    public function lists(){
        $talentModel = new TalentModel();
        $type = input('type');
        $map = array(
            'type' => $type,
            'status' => 1
        );
        $list = $talentModel->where($map)->order('create_time desc')->limit(10)->select();
        $this->assign('list',$list);
        return $this->fetch();
    }
    /*
     *人才创业详情页
     */
    public function detail(){
        $id = input('id');
        $talentModel = new TalentModel();
        $talent = $talentModel->get($id);
        $this->assign('talent',$talent);
        return $this->fetch();
    }
    
}
