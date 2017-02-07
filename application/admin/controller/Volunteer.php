<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/2/7
 * Time: 9:37
 */

namespace app\admin\controller;
use app\admin\model\VolunteerOrder;
use app\admin\model\VolunteerTeam;

/**
 * 志愿服务
 * Class Volunteer
 * @package app\admin\controller
 */
class Volunteer extends Admin {
    /**
     * 服务团队
     */
    public function team() {
        $map = array(
            'status' => 1,
        );
        $list = $this->lists('VolunteerTeam',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已发布",2=>"不通过"),
            'type' => array(1=>"爱心帮扶",2=>"环境美化",3=>"平安巡查"),
        ));
        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 团队添加
     */
    public function teamadd() {
        if(IS_POST) {
            $data = input('post.');
            unset($data['id']);
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $teamModel = new VolunteerTeam();
            $model = $teamModel->validate('Redlead')->save($data);
            if($model) {
                return $this->success("新增成功",Url('Volunteer/team'));
            }else{
                return $this->error($teamModel->getError());
            }
        }else {
            $this->default_pic();
            $this->assign('msg',null);
            return $this->fetch('teamedit');
        }
    }

    /**
     * 团队修改
     */
    public function teamedit() {
        if(IS_POST) {
            $data = input('post.');
            $data['update_time'] = time();
            $data['update_user'] = $_SESSION['think']['user_auth']['id'];
            $teamModel = new VolunteerTeam();
            $model = $teamModel->validate('Redlead')->save($data,['id'=>$data['id']]);
            if($model) {
                return $this->success("修改成功",Url('Volunteer/team'));
            }else{
                return $this->error($teamModel->getError());
            }
        }else {
            $this->default_pic();

            $id = input('id');
            $msg = VolunteerTeam::get($id);
            $this->assign('msg',$msg);

            return $this->fetch();
        }
    }

    /**
     * 团队删除
     */
    public function teamdel() {
        $id = input('id');
        $map = array(
            'status' => -1,
        );
        $teamModel = new VolunteerTeam();
        $model = $teamModel->where('id',$id)->update($map);
        if($model) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }

    /**
     * 服务订单
     */
    public function order() {
        $map = array(
            'status' => array('egt',1),
        );
        $list = $this->lists('VolunteerOrder',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"进行中",2=>"已领取"),
        ));
        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 订单新增
     */
    public function orderadd() {
        if(IS_POST) {
            $data = input('post.');
            unset($data['id']);
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $orderModel = new VolunteerOrder();
            $model = $orderModel->validate('VolunteerOrder')->save($data);
            if($model) {
                return $this->success("新增成功",Url('Volunteer/order'));
            }else{
                return $this->error($orderModel->getError());
            }
        }else {
            $this->default_pic();
            $this->assign('msg',null);
            return $this->fetch('orderedit');
        }
    }

    /**
     * 订单修改
     */
    public function orderedit() {
        if(IS_POST) {
            $data = input('post.');
            $data['update_time'] = time();
            $data['update_user'] = $_SESSION['think']['user_auth']['id'];
            $orderModel = new VolunteerOrder();
            $model = $orderModel->validate('VolunteerOrder')->save($data,['id'=>$data['id']]);
            if($model) {
                return $this->success("修改成功",Url('Volunteer/order'));
            }else{
                return $this->error($orderModel->getError());
            }
        }else {
            $this->default_pic();

            $id = input('id');
            $msg = VolunteerOrder::get($id);
            $this->assign('msg',$msg);

            return $this->fetch();
        }
    }

    /**
     * 订单删除
     */
    public function orderdel() {
        $id = input('id');
        $map = array(
            'status' => -1,
        );
        $teamModel = new VolunteerOrder();
        $model = $teamModel->where('id',$id)->update($map);
        if($model) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }

    /**
     * 订单领取列表
     */
    public function orderreceive() {
        $id = input('id');
        $map = array(
            'oid' => $id,
            'status' => array('eq',1),
        );
        $list = $this->lists('VolunteerOrderReceive',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已领取"),
        ));
        $this->assign('list',$list);
        return $this->fetch();
    }



}