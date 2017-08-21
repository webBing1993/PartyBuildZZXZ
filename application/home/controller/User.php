<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/4/20
 * Time: 15:34
 */

namespace app\home\controller;
use app\home\model\Learn;
use app\home\model\Picture;
use app\home\model\WechatUser;
use app\home\model\WechatUserTag;
use app\home\model\Collect;
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

        // 党龄周年计算
        if (!empty($user['partytime'])) {

            $year = substr($user['partytime'],0,4);
            $thisYear = date('Y',time());
            $user['partyStand'] = $thisYear - $year .'年';
        } else {

            $user['partyStand'] = '';
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

    /***
     * 签到二维码
     */
    public function qcode()
    {
        $this->assign('userId',session('userId'));
        return $this->fetch();
    }


    /**
     * 我收藏的
     * type:1 第一聚焦,2 支部活动,3 两学一做
     */
    public function mycollect(){


        $userId = session('userId');
        $map = array(
            'uid' => $userId,
            'status' => array('egt',0)
        );
        $list = Collect::where($map)->order('id desc')->limit(7)->select();

        foreach ($list as $key => $value) {
            if ($value['type'] == 1) {
                $msg = Learn::where('id',$value['aid'])->find();
                $value['front_cover'] = $msg['front_cover'];
                $value['title'] = $msg['title'];
                $value['content'] = $msg['content'];
                $value['time'] = $msg['create_time'];
                $value['class'] = $msg['type'];
                $value['learn_id'] = $msg['id'];
            }
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
     * 收藏删除
     */
    public function collectdel(){
        $id = input('id');
        $map['status'] = "-1";
        $info = Collect::where('id',$id)->update($map);
        if($info) {

            return $this->success("删除成功","",1);
        }else{

            return $this->error("删除失败","",0);
        }
    }

    /**
     * 加载更多收藏
     */
    public function morecollect(){
        $userId = session('userId');
        $len = input('length');
        $map = array(
            'create_user' => $userId,
            'status' => array('egt',0),
        );
        $list = Collect::where($map)->order('id desc')->limit($len,5)->select();

        foreach ($list as $key => $value) {
            if ($value['type'] == 1) {
                $msg = Learn::where('id',$value['aid'])->find();
                $value['front_cover'] = $msg['front_cover'];
                $value['title'] = $msg['title'];
                $value['content'] = $msg['content'];
                $value['time'] = $msg['create_time'];
                $value['class'] = $msg['type'];
                $value['learn_id'] = $msg['id'];
            }
        }

        if($list){
            return $this->success("加载成功",'',$list);
        }else{
            return $this->error("加载失败");
        }

    }


}
