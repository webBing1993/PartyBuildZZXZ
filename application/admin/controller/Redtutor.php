<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/1/22
 * Time: 13:48
 */

namespace app\admin\controller;
use app\admin\model\RedtutorCourse;
use app\admin\model\RedtutorNotice;
use app\admin\model\RedtutorTutor;

/**
 * 红领导师。
 * Class Redtutor
 * @package app\admin\controller
 */
class Redtutor extends Admin {
    /**
     * 通知列表
     */
    public function notice() {
        $map = array(
            'status' => 1
        );
        $list = $this->lists('RedtutorNotice',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已发布",2=>"不通过"),
        ));
        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 通知新增
     */
    public function noticeadd() {
        if(IS_POST) {
            $data = input('post.');
            unset($data['id']);
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $data['time'] = strtotime($data['time']);
            $noticeModel = new RedtutorNotice();
            $model = $noticeModel->validate('RedtutorNotice')->save($data);
            if($model) {
                return $this->success("新增成功",Url('Redtutor/notice'));
            }else{
                return $this->error($noticeModel->getError());
            }
        }else {
            $this->assign('msg',null);
            return $this->fetch('noticeedit');
        }
    }

    /**
     * 通知修改
     */
    public function noticeedit() {
        if(IS_POST) {
            $data = input('post.');
            $data['update_time'] = time();
            $data['update_user'] = $_SESSION['think']['user_auth']['id'];
            $data['time'] = strtotime($data['time']);
            $noticeModel = new RedtutorNotice();
            $model = $noticeModel->validate('RedtutorNotice')->save($data,['id'=>$data['id']]);
            if($model) {
                return $this->success("修改成功",Url('Redtutor/notice'));
            }else{
                return $this->error($noticeModel->getError());
            }
        }else {
            $id = input('id');
            $msg = RedtutorNotice::get($id);
            $this->assign('msg',$msg);
            return $this->fetch();
        }
    }

    /**
     * 通知删除
     */
    public function noticedel() {
        $id = input('id');
        $map = array(
            'status' => -1,
        );
        $noticeModel = new RedtutorNotice();
        $model = $noticeModel->where('id',$id)->update($map);
        if($model) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }

    /**
     * 导师列表
     */
    public function tutor() {
        $map = array(
            'status' => 1
        );
        $list = $this->lists('RedtutorTutor',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已发布",2=>"不通过"),
            'type' => array(1=>"党建理论",2=>"专业技术",3=>"政策法规",4=>"道德模范"),
        ));
        $this->assign('list',$list);
        
        return $this->fetch();
    }

    /**
     * 导师新增
     */
    public function tutoradd() {
        if(IS_POST) {
            $data = input('post.');
            unset($data['id']);
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $tutorModel = new RedtutorTutor();
            $model = $tutorModel->validate('RedtutorTutor')->save($data);
            if($model) {
                return $this->success("新增成功",Url('Redtutor/tutor'));
            }else{
                return $this->error($tutorModel->getError());
            }
        }else {
            $this->default_pic();
            $this->assign('msg',null);
            return $this->fetch('tutoredit');
        }
    }

    /**
     * 导师编辑
     */
    public function tutoredit() {
        if(IS_POST) {
            $data = input('post.');
            $data['update_time'] = time();
            $data['update_user'] = $_SESSION['think']['user_auth']['id'];
            $tutorModel = new RedtutorTutor();
            $model = $tutorModel->validate('RedtutorTutor')->save($data,['id'=>$data['id']]);
            if($model) {
                return $this->success("修改成功",Url('Redtutor/tutor'));
            }else{
                return $this->error($tutorModel->getError());
            }
        }else {
            $this->default_pic();
            $id = input('id');
            $msg = RedtutorTutor::get($id);
            $this->assign('msg',$msg);
            return $this->fetch();
        }
    }

    /**
     * 导师删除
     */
    public function tutordel() {
        $id = input('id');
        $map = array(
            'status' => -1,
        );
        $tutorModel = new RedtutorTutor();
        $model = $tutorModel->where('id',$id)->update($map);
        if($model) {
            RedtutorCourse::where('tid',$id)->update($map); //删除关联课程
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }

    /**
     * 课程列表
     */
    public function course() {
        $map = array(
            'status' => 1
        );
        $list = $this->lists('RedtutorCourse',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已发布",2=>"不通过"),
        ));
        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 课程新增
     */
    public function courseadd() {
        if(IS_POST) {
            $data = input('post.');
            unset($data['id']);
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $courseModel = new RedtutorCourse();
            $model = $courseModel->validate('RedtutorCourse')->save($data);
            if($model) {
                return $this->success("新增成功",Url('Redtutor/course'));
            }else{
                return $this->error($courseModel->getError());
            }
        }else {
            //获取导师列表
            $map = array('status' => 1,);
            $list = RedtutorTutor::where($map)->field('id,name')->select();
            $this->assign('tutor',$list);

            $this->assign('msg',null);
            return $this->fetch('courseedit');
        }
    }

    /**
     * 课程修改
     */
    public function courseedit() {
        if(IS_POST) {
            $data = input('post.');
            $data['update_time'] = time();
            $data['update_user'] = $_SESSION['think']['user_auth']['id'];
            $courseModel = new RedtutorCourse();
            $model = $courseModel->validate('RedtutorCourse')->save($data,['id'=>$data['id']]);
            if($model) {
                return $this->success("修改成功",Url('Redtutor/course'));
            }else{
                return $this->error($courseModel->getError());
            }
        }else {
            //获取导师列表
            $map = array('status' => 1,);
            $list = RedtutorTutor::where($map)->field('id,name')->select();
            $this->assign('tutor',$list);

            $id = input('id');
            $msg = RedtutorCourse::get($id);
            $this->assign('msg',$msg);

            return $this->fetch();
        }
    }

    /**
     * 课程删除
     */
    public function coursedel() {

    }





}