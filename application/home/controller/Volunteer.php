<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2016/11/2
 * Time: 13:21
 */
namespace app\home\controller;
use app\home\model\Comment;
use app\home\model\Like;
use app\home\model\Picture;
use app\home\model\VolunteerOrder;
use app\home\model\VolunteerRecruit;
use app\home\model\VolunteerTeam;


class Volunteer extends Base{
    /**
     * 服务团队
     */
    public function team(){
        $teamModel = new VolunteerTeam();
        //type：1爱心帮扶
        $map1 = array(
            'status' => 1,
            'type' => 1,
        );
        $list1 = $teamModel->where($map1)->order('create_time desc')->limit(7)->select();
        $this->assign('list1',$list1);
        //type：2环境美化
        $map2 = array(
            'status' => 1,
            'type' => 2,
        );
        $list2 = $teamModel->where($map2)->order('create_time desc')->limit(7)->select();
        $this->assign('list2',$list2);
        //type：3平安巡查
        $map3 = array(
            'status' => 1,
            'type' => 3,
        );
        $list3 = $teamModel->where($map3)->order('create_time desc')->limit(7)->select();
        $this->assign('list3',$list3);
        return $this->fetch();
    }
    
    /**
     * 服务团队加载更多
     */
    public function listmore() {
        $len = input('length');
        $map =  array(
            'status' => 1,
            'type' => input('type'),
        );
        $teamModel = new VolunteerTeam();
        $list = $teamModel->where($map)->order('create_time desc')->limit($len,5)->select();
        foreach ($list as $value) {
            $img = Picture::get($value['list_image']);
            $value['path'] = $img['path'];
            $value['time'] = date('Y-m-d',$value['create_time']);
        }
        if($list) {
            return $this->success("加载成功","",$list);
        }else{
            return $this->error("加载失败");
        }
    }

    /**
     * 服务团队详情页
     */
    public function teamdetail(){
        $this->anonymous(); //判断是否是游客

        $uid = session('userId');
        $id = input('id');
        $teamModel = new VolunteerTeam();
        $teamModel::where('id',$id)->setInc('views');     //浏览加一

        $info = $teamModel->get($id);
        //点赞
        $likeModel = new Like();
        $like = $likeModel->getLike(12,$id,$uid);
        $info['is_like'] = $like;
        $this->assign('info',$info);

        //获取评论
        $commentModel = new Comment();
        $comment = $commentModel->getComment(12,$id,$uid);
        $this->assign('comment',$comment);

        return $this->fetch();
    }

    /**
     * 服务订单
     */
    public function order(){
        $orderModel = new VolunteerOrder();
        $map = array(
            'status' => array('egt',1)
        );
        $list = $orderModel->where($map)->order('create_time desc')->limit(7)->select();
        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 服务订单更多
     */
    public function ordermore() {
        $len = input('length');
        $map =  array(
            'status' => array('egt',1)
        );
        $orderModel = new VolunteerOrder();
        $list = $orderModel->where($map)->order('create_time desc')->limit($len,5)->select();
        foreach ($list as $value) {
            $img = Picture::get($value['list_image']);
            $value['path'] = $img['path'];
            $value['time'] = date('Y-m-d',$value['create_time']);
        }
        if($list) {
            return $this->success("加载成功","",$list);
        }else{
            return $this->error("加载失败");
        }
    }

    /**
     * 服务订单详情页
     */
    public function orderdetail(){
        return $this->fetch();
    }

    /**
     * 志愿招募
     */
    public function recruit(){
        $recruitModel = new VolunteerRecruit();
        $map = array(
            'status' => array('eq',1)
        );
        $list = $recruitModel->where($map)->order('create_time desc')->limit(7)->select();
        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 志愿招募
     */
    public function recruitmore() {
        $len = input('length');
        $map =  array(
            'status' => array('eq',1)
        );
        $recruitModel = new VolunteerRecruit();
        $list = $recruitModel->where($map)->order('create_time desc')->limit($len,5)->select();
        foreach ($list as $value) {
            $img = Picture::get($value['list_image']);
            $value['path'] = $img['path'];
            $value['time'] = date('Y-m-d',$value['create_time']);
        }
        if($list) {
            return $this->success("加载成功","",$list);
        }else{
            return $this->error("加载失败");
        }
    }

    /**
     * 志愿招募详情页
     */
    public function recruitdetail(){
        return $this->fetch();
    }

    /**
     * 志愿发布页
     */
    public function publish(){
        return $this->fetch();
    }
}
