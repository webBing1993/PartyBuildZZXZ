<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/10/8
 * Time: 10:29
 */

namespace app\admin\controller;

use app\admin\model\Notice as NoticeModel;
use app\admin\model\NoticeSend;
use app\admin\model\WechatDepartment;
use app\admin\model\WechatTag;
use app\admin\model\WechatUser;

/**
 * Class Notice
 * @package 支部活动
 */
class Notice extends Admin {
    /**
     * 相关通知
     * type: 1
     */
    public function index(){
        $map = array(
            'type' => 1,
            'status' => array('egt',0),
        );
        $list = $this->lists('Notice',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已发布",2=>"审核不通过",3=>"草稿"),
        ));

        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 会议情况
     * type: 2
     */
    public function meet(){
        $map = array(
            'type' => 2,
            'status' => array('egt',0),
        );
        $list = $this->lists('Notice',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已发布",2=>"审核不通过",3=>"草稿"),
        ));

        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 党课情况
     * type: 3
     */
    public function lecture(){
        $map = array(
            'type' => 3,
            'status' => array('egt',0),
        );
        $list = $this->lists('Notice',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已发布",2=>"审核不通过",3=>"草稿"),
        ));

        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 活动招募
     * type: 4
     */
    public function recruit(){
        $map = array(
            'type' => 4,
            'status' => array('egt',0),
        );
        $list = $this->lists('Notice',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已发布",2=>"审核不通过",3=>"草稿"),
        ));

        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 活动情况
     * type: 5
     */
    public function activity(){
        $map = array(
            'type' => 5,
            'status' => array('egt',0),
        );
        $list = $this->lists('Notice',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已发布",2=>"审核不通过",3=>"草稿"),
        ));

        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 相关通知和活动招募 添加
     */
    public function indexadd(){
        if(IS_POST) {
            $data = input('post.');
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $noticeModel = new NoticeModel();
            $data['carousel_images'] = json_encode($data['carousel_images']);  //将数组转为字符串存入数据库，用到时解码
            $data['start_time'] = strtotime($data['start_time']);
            $data['end_time'] = strtotime($data['end_time']);
            $id = $noticeModel->validate('Notice.act')->save($data);
            if($id){
                if($data['type'] == 1){
                    return $this->success("新增相关通知成功",Url('Notice/index'));
                }else{
                    return $this->success("新增活动通知成功",Url('Notice/recruit'));
                }
            }else{
                return $this->error($noticeModel->getError());
            }
        }else {
            $this->default_pic();

            $type = input('type');
            $this->assign('type',$type);
            return $this->fetch();
        }
    }

    /**
     * 相关通知和活动招募 修改
     */
    public function indexedit(){
        if(IS_POST) {
            $data = input('post.');
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $noticeModel = new NoticeModel();
            $data['carousel_images'] = json_encode($data['carousel_images']);  //将数组转为字符串存入数据库，用到时解码
            $data['start_time'] = strtotime($data['start_time']);
            $data['end_time'] = strtotime($data['end_time']);
            $id = $noticeModel->validate('Notice.act')->save($data,['id'=>input('id')]);
            if($id){
                if($data['type'] == 1){
                    return $this->success("修改相关通知成功",Url('Notice/index'));
                }else{
                    return $this->success("修改活动招募成功",Url('Notice/recruit'));
                }
            }else{
                return $this->error($noticeModel->getError());
            }
        }else{
            $this->default_pic();

            $id = input('id');
            $msg = NoticeModel::get($id);
            //轮播图片重组
            if($msg['carousel_images']){
                $images = json_decode($msg['carousel_images']);
                foreach($images as $k => $val){
                    $msg['carousel_images'.($k+1)] = $val;
                }
            }
            $this->assign('msg',$msg);
            return $this->fetch();
        }
    }

    /**
     * 三个情况添加
     */
    public function add(){
        if(IS_POST) {
            $data = input('post.');
            $noticeModel = new NoticeModel();
            if(empty($data['id'])) {
                unset($data['id']);
            }
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
//            $data['meet_time'] = strtotime($data['meet_time']);
            $model = $noticeModel->validate('Notice.other')->save($data);
            if($model){
               if ($data['type'] == 3){
                  return $this->success('新增党课情况成功',Url('Notice/lecture'));
               }else if($data['type'] == 5){
                   return $this->success('新增活动情况成功',Url('Notice/activity'));
               }else{
                   return $this->success('新增会议情况成功',Url('Notice/meet'));
               }
            }else{
                 return $this->error($noticeModel->getError());
            }
        }else{
            $this->default_pic();
            $msg = array();
            $msg['type'] = input('type');
            $msg['class'] = 1; // 1为添加 ，2为修改
            $this->assign('msg',$msg);

            return $this->fetch('edit');
        }
    }

    /**
     * 修改
     */
    public function edit(){
        if(IS_POST) {
            $data = input('post.');
            $noticeModel = new NoticeModel();
//            $data['meet_time'] = strtotime($data['meet_time']);
            $model = $noticeModel->validate('Notice.other')->save($data,['id'=> input('id')]);
            if($model){
                if ($data['type'] == 3){
                    return $this->success('修改党课情况成功',Url('Notice/lecture'));
                }else if($data['type'] == 5){
                    return $this->success('修复活动情况成功',Url('Notice/activity'));
                }else{
                    return $this->success('修改会议情况成功',Url('Notice/meet'));
                }
            }else{
                return $this->get_update_error_msg($noticeModel->getError());
            }
        }else{
            $this->default_pic();

            $id = input('id');
            $msg = NoticeModel::get($id);
            $msg['class'] = 2;
            $this->assign('msg',$msg);

            return $this->fetch();
        }
    }

    /**
     * 删除
     */
    public function del(){
        $id = input('id');
        $map['status'] = "-1";
        $info = NoticeModel::where('id',$id)->update($map);
        if($info) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }
    /*
     * 创意组织生活
     *  type:6
     */
    public function regular(){
        $map = array(
            'type' => 6,
            'status' => array('egt',0),
        );
        $list = $this->lists('Notice',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已发布",2=>"审核不通过",3=>"草稿"),
        ));
        $this->assign('list',$list);

        return $this->fetch();
    }
    /*
     * 创意组织生活  添加 修改
     */
    public function regularadd(){
        $id = input('param.id');
        if($id){
            // 修改
            if(IS_POST){
                $data = input('post.');
                $noticeModel = new NoticeModel();
                $data['create_user'] = $_SESSION['think']['user_auth']['id'];
                $model = $noticeModel->validate('Learn')->where(['id' => $id])->update($data);
                if($model){
                    return $this->success('修改成功',Url("Notice/regular"));
                }else{
                    return $this->error($noticeModel->getError());
                }
            }else{
                $msg = NoticeModel::where(['id' => $id,'status' => 1])->find();
                $this->assign('msg',$msg);
                return $this->fetch();
            }
        }else{
            // 添加
            if(IS_POST){
                $data = input('post.');
                if(empty($data['id'])) {
                    unset($data['id']);
                }
                $noticeModel = new NoticeModel();
                $data['create_user'] = $_SESSION['think']['user_auth']['id'];
                $model = $noticeModel->validate('Learn')->save($data);
                if($model){
                    return $this->success('新增成功!',Url("Notice/regular"));
                }else{
                    return $this->error($noticeModel->getError());
                }
            }else{
                $this->default_pic();
                $this->assign('msg','');
                return $this->fetch();
            }
        }
    }
}