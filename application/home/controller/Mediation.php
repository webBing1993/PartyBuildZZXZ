<?php
/**
 * Created by PhpStorm.
 * User: daire
 * Date: 2017/12/25
 * Time: 14:51
 */

namespace app\home\controller;
use app\home\model\Like;
use app\home\model\WechatUserTag;
use think\Db;
use think\Request;
use think\Controller;
use app\home\model\Browse;
use app\home\model\Comment;
use app\admin\model\Picture;
use app\home\model\WechatUser;
use app\home\model\Mediation as MediationModel;
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


        $userId = session('userId');
        $tag = WechatUserTag::where(['userid' => $userId, 'tagid' => 1])->find();
        if($tag){
            $user_tag = 1;//管理员
        }else{
            $tag = WechatUserTag::where(['userid' => $userId, 'tagid' => 2])->find();
            if($tag){
                $user_tag = 2;//调解员
            }else{
                $user_tag = 3;//申请人
            }
        }
        if($user_tag == 3){//申请人
            $map_to = [
                'userid' => $userId,
            ];
            $list3 = MediationModel::where($map_to)->limit(10)->order('id desc')->select();
            foreach ($list3 as $value) {
                $value['status_text'] = MediationModel::TOTAL_STATU_ARRAY[$value['status']];
                $value['status_color'] = MediationModel::STATU_COLOR_ARRAY[$value['status']];
                if ($value['images']) {
                    $value['path'] = get_cover(json_decode($value['images'], true)[0], 'path');
                } else {
                    $value['path'] = '/home/images/lxyz/icon/default.png';
                }
            }

            $this->assign('list3',$list3);
        }elseif($user_tag == 2){//调解员
            $count_check = MediationModel::where(['mediatorid' => $userId, 'status' => MediationModel::STATUS_CHECK])->count();
            $count_confirm = MediationModel::where(['mediatorid' => $userId, 'status' => MediationModel::STATUS_CONFIRM])->count();
            $count_done = MediationModel::where(['mediatorid' => $userId, 'status' => ['egt',MediationModel::STATUS_FEEDBACK]])->count();

            $this->assign('count_check',$count_check);
            $this->assign('count_confirm',$count_confirm);
            $this->assign('count_done',$count_done);
        }else{//管理员
            $count_noapprove = MediationModel::where(['status' => MediationModel::STATUS_NOAPPROVE])->count();
            $count_check = MediationModel::where(['status' => MediationModel::STATUS_CHECK])->count();
            $count_confirm = MediationModel::where(['status' => MediationModel::STATUS_CONFIRM])->count();
            $count_done = MediationModel::where(['status' => ['egt',MediationModel::STATUS_FEEDBACK]])->count();

            $this->assign('count_noapprove',$count_noapprove);
            $this->assign('count_check',$count_check);
            $this->assign('count_confirm',$count_confirm);
            $this->assign('count_done',$count_done);
        }
        $this->assign('user_tag',$user_tag);
        return $this->fetch();
    }
    /**
     * 网上申请子页面列表
     */
    public function listdetails(){
        $userId = session('userId');
        $type = input('type');
        $tag = WechatUserTag::where(['userid' => $userId, 'tagid' => 1])->find();
        $map = [];
        if ($type == 1) {
            $map = [
                'status' => MediationModel::STATUS_NOAPPROVE,
            ];
        } elseif($type == 2) {
            $map = [
                'status' => MediationModel::STATUS_CHECK,
            ];
        } elseif($type == 3) {
            $map = [
                'status' => MediationModel::STATUS_CONFIRM,
            ];
        } elseif($type == 4) {
            $map = [
                'status' => ['egt',MediationModel::STATUS_FEEDBACK],
            ];
        }
        if (!$tag) {//调解员
            $map['mediatorid'] = $userId;
        }
        $list = MediationModel::where($map)->limit(10)->order('id desc')->select();
        foreach ($list as $value) {
            $value['status_text'] = MediationModel::TOTAL_STATU_ARRAY[$value['status']];
            $value['status_color'] = MediationModel::STATU_COLOR_ARRAY[$value['status']];
            if ($value['images']) {
                $value['path'] = get_cover(json_decode($value['images'], true)[0], 'path');
            } else {
                $value['path'] = '/home/images/lxyz/icon/default.png';
            }
        }
        $this->assign('type',$type);
        $this->assign('list',$list);
        return $this->fetch();
    }
    /**
     * 案例加载更多
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
     * 网上申请加载更多
     */
    public function mediationmore(){
        $userId = session('userId');
        $len = input('length');
        $type = input('type');
        $tag = WechatUserTag::where(['userid' => $userId, 'tagid' => 1])->find();
        if($tag){
            $user_tag = 1;//管理员
        }else{
            $tag = WechatUserTag::where(['userid' => $userId, 'tagid' => 2])->find();
            if($tag){
                $user_tag = 2;//调解员
            }else{
                $user_tag = 3;//申请人
            }
        }
        if ($user_tag == 3) {
            $map = array(
                'userid' => $userId,
            );
        }else{
            if ($type == 1) {
                $map = [
                    'status' => MediationModel::STATUS_NOAPPROVE,
                ];
            } elseif($type == 2) {
                $map = [
                    'status' => MediationModel::STATUS_CHECK,
                ];
            } elseif($type == 3) {
                $map = [
                    'status' => MediationModel::STATUS_CONFIRM,
                ];
            } elseif($type == 4) {
                $map = [
                    'status' => ['egt',MediationModel::STATUS_FEEDBACK],
                ];
            }
            if ($user_tag == 2) {//调解员
                $map['mediatorid'] = $userId;
            }
        }
        $list = MediationModel::where($map)->limit($len,6)->order('id desc')->select();
        foreach ($list as $value) {
            $value['time'] = date("Y-m-d",$value['create_time']);
            $value['status_text'] = MediationModel::TOTAL_STATU_ARRAY[$value['status']];
            $value['status_color'] = MediationModel::STATU_COLOR_ARRAY[$value['status']];
            if ($value['images']) {
                $value['path'] = get_cover(json_decode($value['images'], true)[0], 'path');
            } else {
                $value['path'] = '/home/images/lxyz/icon/default.png';
            }
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
            'userid' => input('id'),
        ];
        $model = MediationUser::where($map)->find();
        $this->assign('model',$model);

        $map = [
            'uid' => input('id'),
            'status' => ['egt',0],
        ];
        $list = MediationCase::where($map)->limit(5)->order('id desc')->select();
        $this->assign('uid',input('id'));
        $this->assign('list',$list);
        return $this->fetch();
    }
    /**
     * 调解详情页
     */
    public function yhdetails(){
        $userId = session('userId');
        $id = input('id');
        $tag = WechatUserTag::where(['userid' => $userId, 'tagid' => 1])->find();
        if($tag){
            $user_tag = 1;//管理员
        }else{
            $tag = WechatUserTag::where(['userid' => $userId, 'tagid' => 2])->find();
            if($tag){
                $user_tag = 2;//调解员
            }else{
                $user_tag = 3;//申请人
            }
        }
        $model = MediationModel::get($id);
//        var_dump($model);die;
        $response = [];

        if($model['status'] == MediationModel::STATUS_NOAPPROVE){
            $response[1] = [
                'd_time' => date('m-d', $model['create_time']),
                's_time' => date('H:i', $model['create_time']),
                'num' => 1,
                'check' => 1,
                'status_text' => MediationModel::STATU_ARRAY[MediationModel::STATUS_COMMIT],
            ];
            $response[2] = [
                'd_time' => date('m-d', $model['check_time']),
                's_time' => date('H:i', $model['check_time']),
                'num' => 2,
                'check' => 1,
                'status_text' => MediationModel::STATU_ARRAY[MediationModel::STATUS_NOAPPROVE],
            ];

        }else{
            for($i=1; $i<=5; $i++){
                $response[$i]['d_time'] = null;
                $response[$i]['s_time'] = null;
                $time_field = MediationModel::STATU_TOTIME_ARRAY[$i];
                if (!empty($model[$time_field])) {
                    $response[$i]['d_time'] = date('m-d', $model[$time_field]);
                    $response[$i]['s_time'] = date('H:i', $model[$time_field]);
                }
                $response[$i]['num'] = $i;
                if ($model['status'] >= $i) {
                    $response[$i]['check'] = 1;
                    $response[$i]['status_text'] = MediationModel::STATU_ARRAY[$i];
                }else{
                    $response[$i]['check'] = 0;
                    $response[$i]['status_text'] = MediationModel::NEXT_STATU_ARRAY[$i-1];
                }

            }
        }
        $this->assign('response',$response);
        $this->assign('model',$model);
        $this->assign('user_tag',$user_tag);

        return $this->fetch();
    }

    public function application(){
        return $this->fetch();
    }
    public function newdetail(){
        return $this->fetch();
    }
    public function applicationdetail(){
        return $this->fetch();
    }
    public function evaluate(){
        return $this->fetch();
    }
}