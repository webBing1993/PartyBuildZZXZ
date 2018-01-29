<?php
/**
 * Created by PhpStorm.
 * User: daire
 * Date: 2017/12/11
 * Time: 13:20
 */

namespace app\home\controller;
use app\home\model\Browse;
use app\home\model\Comment;
use app\home\model\Like;
use app\home\model\Picture;
use app\home\model\WechatUser;
use app\home\model\WechatUserTag;
use com\wechat\TPQYWechat;
use think\Config;
use app\home\model\Audit as AuditModel;

/**
 * Class Audit
 * @package 审核
 */
class Audit extends Base
{
    /**
     * 未审核
     */
    public function reviewlist(){
        $userId = session('userId');
        $tag = WechatUserTag::where(['userid' => $userId, 'tagid' => 1])->find();
        if(!$tag){
            return $this->redirect("verify/null");
        }else{
            $map = [
                'status' => 0,
                'type' => ['neq',4],
            ];
            $list = AuditModel::where($map)->order('id desc')->limit(10)->select();
            foreach ($list as $key => $value){
                $list[$key]['time'] = date("Y-m-d",$value['create_time']);
                $img = Picture::get($value['front_cover']);
                $list[$key]['path'] = $img['path'];
                $list[$key]['pre'] = AuditModel::STATU_ARRAY[$value['type']];
                $list[$key]['url'] = AuditModel::URL_ARRAY[$value['type']];
            }
            $this->assign('list',$list);

            $map1 = [
                'status' => 0,
                'type' => 4,
            ];
            $list1 = AuditModel::where($map1)->order('id desc')->limit(10)->select();
            foreach ($list1 as $key => $value){
                $list[$key]['time'] = date("Y-m-d",$value['create_time']);
                $img = Picture::get($value['front_cover']);
                $list[$key]['path'] = $img['path'];
                $list1[$key]['pre'] = AuditModel::STATU_ARRAY[$value['type']];
            }
            $this->assign('list1',$list1);
            return $this->fetch();
        }
    }

    /**
     * 已审核
     */
    public function passlist(){
        $userId = session('userId');
        $tag = WechatUserTag::where(['userid' => $userId, 'tagid' => 1])->find();
        if(!$tag){
            return $this->redirect("verify/null");
        }else{
            $map = [
                'status' => 1,
                'type' => ['neq',4],
            ];
            $list = AuditModel::where($map)->order('id desc')->limit(10)->select();
            foreach ($list as $key => $value){
                $list[$key]['time'] = date("Y-m-d",$value['create_time']);
                $img = Picture::get($value['front_cover']);
                $list[$key]['path'] = $img['path'];
                $list[$key]['pre'] = AuditModel::STATU_ARRAY[$value['type']];
            }
            $this->assign('list',$list);

            $map1 = [
                'status' => 1,
                'type' => 4,
            ];
            $list1 = AuditModel::where($map1)->order('id desc')->limit(10)->select();
            foreach ($list1 as $key => $value){
                $list[$key]['time'] = date("Y-m-d",$value['create_time']);
                $img = Picture::get($value['front_cover']);
                $list[$key]['path'] = $img['path'];
                $list1[$key]['pre'] = AuditModel::STATU_ARRAY[$value['type']];
            }
            $this->assign('list1',$list1);
            return $this->fetch();
        }
    }

    /**
     * 加载更多
     */
    public function indexmore(){
        $len = input('length');
        $type = input('type');
        $status = input('status');
        $map = [
            'status' => $status,
        ];
        if($type == 1){
            $map['type'] = 4;
        }else{
            $map['type'] = ['neq',4];
        }
        $list = AuditModel::where($map)->order('id desc')->limit($len,6)->select();
        foreach($list as $key => $value){
            $value['time'] = date("Y-m-d",$value['create_time']);
            $img = Picture::get($value['front_cover']);
            $value['path'] = $img['path'];
            $list[$key]['pre'] = AuditModel::STATU_ARRAY[$value['type']];
        }
        if($list){
            return $this->success("加载成功",'',$list);
        }else{
            return $this->error("加载失败");
        }
    }
    /**
     * 审核操作
     */
    public function review()
    {
        $id = input('id');
        $status = input('status');
        $info = ApprovedModel::update(['status' => $status], ['id' => $id]);
        if ($info) {
            return $this->success();
        } else {
        return $this->error();
        }
    }
}