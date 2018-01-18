<?php
/**
 * Created by PhpStorm.
 * User: daire
 * Date: 2017/12/25
 * Time: 14:51
 */

namespace app\home\controller;
use app\home\model\Like;
use think\Db;
use think\Request;
use think\Controller;
use app\home\model\Browse;
use app\home\model\Comment;
use app\admin\model\Picture;
use app\home\model\WechatUser;
use app\home\model\MediationCase;
use app\home\model\MediationUser;

class Mediation extends Base
{
    public function index(){
        $this->anonymous();
        //数据列表
        $map = [
            'status' => ['egt',0],
        ];
        $list1 = MediationCase::where($map)->limit(5)->order('id desc')->select();
        $this->assign('list1',$list1);

        $list2 = MediationUser::where($map)->order('id desc')->select();
        $this->assign('list2',$list2);
        return $this->fetch();
    }
    /**
     * 加载更多
     */
    public function indexmore(){
        $len = input('length');
        $uid = input('uid');
        $map = array(
            'status' => ['egt',0],
        );
        if($uid){
            $map['uid'] = $uid;
        }
        $list = MediationCase::where($map)->order('id desc')->limit($len,6)->select();
        foreach($list as $value){
            $value['time'] = date("Y-m-d",$value['create_time']);
        }
        if($list){
            return $this->success("加载成功",'',$list);
        }else{
            return $this->error("加载失败");
        }
    }

    /**
     * 调解员介绍页
     */
    public function medcase(){
        $map = [
            'id' => input('id'),
        ];
        $model = MediationUser::where($map)->find();
        $this->assign('model',$model);

        $map = [
            'uid' => input('id'),
            'status' => ['egt',0],
        ];
        $list = MediationCase::where($map)->limit(5)->order('id desc')->select();
        $this->assign('list',$list);
        return $this->fetch();
    }
    public function application(){
        return $this->fetch();
    }
    public function details(){
        return $this->fetch();
    }
    public function newdetail(){
        return $this->fetch();
    }
    public function applicationdetail(){
        return $this->fetch();
    }
    public function tjydetails(){
        return $this->fetch();
    }
    public function yhdetails(){
        return $this->fetch();
    }
    public function evaluate(){
        return $this->fetch();
    }
}