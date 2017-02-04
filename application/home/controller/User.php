<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/4/20
 * Time: 15:34
 */

namespace app\home\controller;
use app\home\model\Comment;
use app\home\model\Learn;
use app\home\model\Years;
use app\home\model\Notice;
use app\home\model\Picture;
use app\home\model\WechatUser;
use app\home\model\WechatUserTag;
use think\Controller;
use think\Db;

/**
 * Class User
 * 用户个人中心
 */
class User extends Base {
    /**
     * 个人中心主页
     */
    public function index(){
        //是否游客登录
        $this->anonymous();
        
        $userId = session('userId');
        $user = WechatUser::where('userid',$userId)->find();
        $this->assign('user',$user);

        //是否具备我的发布权限,具备为1，无则为0
        $map = array(
            'userid' => $userId,
            'tagid' => 5, //权限标签id
        );
        $info = WechatUserTag::where($map)->find();
        if($info) {
            $this->assign('is',1);
        }else{
            $this->assign('is',0);
        }
        return $this->fetch();
    }

    /**
     * 个人信息页
     */
    public function personal(){
        $userId = session('userId');
        $user = WechatUser::where('userid',$userId)->find();
        switch ($user['gender']) {
            case 0:
                $user['sex'] = "未定义";
                break;
            case 1:
                $user['sex'] = "男";
                break;
            case 2:
                $user['sex'] = "女";
                break;
            default:
                break;
        }
        $this->assign('user',$user);

        return $this->fetch();
    }

    /**
     * 设置头像
     */
    public function setHeader(){
        $userId = session('userId');
        $header = input('header');
        $map = array(
            'header' => $header,
        );
        $info = WechatUser::where('userid',$userId)->update($map);
        if($info){
            return $this->success("修改成功");
        }else{
            return $this->error("修改失败");
        }
    }

    /**
     * 我的笔记
     */
    public function mynotes(){

        $userid = session('userId');
        $noticeModel = new Notice();
        $map = array(
            'type' => array('in',[2,3,5,7]),
            'userid' => $userid,
            'status' => array('egt',0),
        );
        $list = $noticeModel->where($map)->order('create_time desc')->limit(7)->select();
        foreach ($list as $key => $value) {
            $value['time'] = date("Y-m-d",$value['create_time']);
            $img = Picture::get($value['front_cover']);
            $value['img'] = $img['path'];
        }
        $this->assign('list',$list);
        if(empty($list)){
            $this->assign('is_empty',1);    //为空
        }else{
            $this->assign('is_empty',0);
        }
        return $this->fetch();
    }

    /**
     * 我的发布
     */
    public function mypublish(){

        $noticeModel = new Notice();
        $userid = session('userId');
        $map = array(
            'type' => array('in',[1,4,6]),
            'userid' => $userid,
            'status' => array('egt',0),
        );
        $list = $noticeModel->where($map)->order('create_time desc')->limit(7)->select();
        $this->assign('list',$list);
        if(empty($list)){
            $this->assign('is_empty',1);    //为空
        }else{
            $this->assign('is_empty',0);
        }
        return $this->fetch();
    }

    /**
     * 我的笔记和发布删除功能
     */
    public function mydel() {
        $id = input('id');
        $map['status'] = -1;
        $model = Notice::where('id',$id)->update($map);
        if($model) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }

    /**
     * 笔记更多
     */
    public function notesmore() {
        $noticeModel = new Notice();
        $len = input('length');
        $userid = input('userid');
        $map = array(
            'type' => array('in',[2,3,5,7]),
            'userid' => $userid,
            'status' => array('egt',0),
        );
        $list = $noticeModel->where($map)->order('create_time desc')->limit($len,5)->select();
        foreach ($list as $value) {
            $value['time'] = date("Y-m-d",$value['create_time']);
            $img = Picture::get($value['front_cover']);
            $value['img'] = $img['path'];
        }
        if($list) {
            return $this->success("加载更多",'',$list);
        }else{
            return $this->error("加载失败");
        }
    }

    /**
     * 发布更多
     */
    public function publishmore() {
        $noticeModel = new Notice();
        $len = input('length');
        $userid = input('userid');
        $map = array(
            'type' => array('in',[1,4,6]),
            'userid' => $userid,
            'status' => array('egt',0),
        );
        $list = $noticeModel->where($map)->order('create_time desc')->limit($len,5)->select();
        foreach ($list as $value) {
            $value['time'] = date("Y-m-d",$value['create_time']);
        }
        if($list) {
            return $this->success("加载更多",'',$list);
        }else{
            return $this->error("加载失败");
        }
    }

    /**
     * 笔记预览
     */
    public function notesreview() {
        $id = input('id');
        $note = Notice::get($id);
        $note['images'] = json_decode($note['images']);

        $this->assign('note',$note);

        return $this->fetch();
    }

    /**
     * 发布预览
     */
    public function publishreview() {
        $id = input('id');
        $publish = Notice::get($id);
        $this->assign('publish',$publish);
        
        return $this->fetch();
    }
    
    /*
     * 我的消息
     */
    public function history(){
        $userId = session('userId');
        $Year = new Years();
        $map = array(
            "userid" => $userId,
        );
        $list = $Year->where($map)->order(['create_time'=>'desc'])->select();
        $this->assign('list',$list);
        return $this->fetch();
    }
    /**
     * 临时党员信息
     */
    public function eg() {
        return $this->fetch();
    }

}
