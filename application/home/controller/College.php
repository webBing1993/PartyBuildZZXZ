<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2016/11/2
 * Time: 13:21
 */
namespace app\home\controller;

use app\home\model\Redforum;
use app\home\model\RedforumNotice;
use app\home\model\Redlead;
use app\home\model\RedtutorCourse;
use app\home\model\RedtutorNotice;
use app\home\model\RedtutorTutor;
use think\Controller;

/**
 * 红领学院
 * Class College
 * @package app\home\controller
 */
class College extends Base{
    /**
     * 主页
     */
    public function index() {
        //通知
        $map = array(
            'status' => 1,
        );
        $notice = RedtutorNotice::where($map)->order('create_time desc')->find();
        $this->assign('notice',$notice);

        $tutorModel = new RedtutorTutor();
        $courseModel = new RedtutorCourse();

        //党建理论 type:1
        $map1 = array(
            'type' => 1,
            'status' => 1,
        );
        $tutor1 = $tutorModel->where($map1)->order('create_time desc')->select();
        $course1 = array(); //循环获取所属导师的所有课程
        $c1 = array();
        foreach ($tutor1 as $key => $value) {
            $map = array(
                'status' => 1,
                'tid' => $value['id'],
            );
            $course = $courseModel->where($map)->order('create_time desc')->select();
            foreach ($course as $k => $v) {
                $c1[] = $v;
            }
        }
        foreach ($c1 as $k => $val) {
            if($k < 2) {
                $course1[] = $val;
            }
        }
        $this->assign('tutor1',$tutor1);
        $this->assign('course1',$course1);

        //专业技术 type:2
        $map2 = array(
            'type' => 2,
            'status' => 1,
        );
        $tutor2 = $tutorModel->where($map2)->order('create_time desc')->select();
        $course2 = array(); //循环获取所属导师的所有课程
        $c2 = array();
        foreach ($tutor2 as $key => $value) {
            $map = array(
                'status' => 1,
                'tid' => $value['id'],
            );
            $course = $courseModel->where($map)->order('create_time desc')->select();
            foreach ($course as $k => $v) {
                $c2[] = $v;
            }
        }
        foreach ($c2 as $k => $val) {
            if($k < 2) {
                $course2[$k] = $val;
            }
        }
        $this->assign('tutor2',$tutor2);
        $this->assign('course2',$course2);

        //政策法规 type:3
        $map3 = array(
            'type' => 3,
            'status' => 1,
        );
        $tutor3 = $tutorModel->where($map3)->order('create_time desc')->select();
        $course3 = array(); //循环获取所属导师的所有课程
        $c3 = array();
        foreach ($tutor3 as $key => $value) {
            $map = array(
                'status' => 1,
                'tid' => $value['id'],
            );
            $course = $courseModel->where($map)->order('create_time desc')->select();
            foreach ($course as $k => $v) {
                $c3[] = $v;
            }
        }
        foreach ($c3 as $k => $val) {
            if($k < 2) {
                $course3[$k] = $val;
            }
        }
        $this->assign('tutor3',$tutor3);
        $this->assign('course3',$course3);

        //道德模范 type:4
        $map4 = array(
            'type' => 4,
            'status' => 1,
        );
        $tutor4 = $tutorModel->where($map4)->order('create_time desc')->select();
        $course4 = array();//循环获取所属导师的所有课程
        $c4 = array();
        foreach ($tutor4 as $key => $value) {
            $map = array(
                'status' => 1,
                'tid' => $value['id'],
            );
            $course = $courseModel->where($map)->order('create_time desc')->select();
            foreach ($course as $k => $v) {
                $c4[] = $v;
            }
        }
        foreach ($c4 as $k => $val) {
            if($k < 2) {
                $course4[] = $val;
            }
        }
        $this->assign('tutor4',$tutor4);
        $this->assign('course4',$course4);

        //红领带动
        $leadModel = new Redlead();
        $info1 = array(
            'type' => 1,
            'status' => 1,
        );
        $lead1 = $leadModel->where($info1)->order('create_time desc')->limit(2)->select();
        $this->assign('lead1',$lead1);

        $info2 = array(
            'type' => 2,
            'status' => 1,
        );
        $lead2 = $leadModel->where($info2)->order('create_time desc')->limit(2)->select();
        $this->assign('lead2',$lead2);

        $info3 = array(
            'type' => 3,
            'status' => 1,
        );
        $lead3 = $leadModel->where($info3)->order('create_time desc')->limit(2)->select();
        $this->assign('lead3',$lead3);

        $info4 = array(
            'type' => 4,
            'status' => 1,
        );
        $lead4 = $leadModel->where($info4)->order('create_time desc')->limit(2)->select();
        $this->assign('lead4',$lead4);

        //红色论坛
        $forumModel = new Redforum();
        $fnoticeModel = new RedforumNotice();
        $msg = array(
            'status' => 1,
        );
        $fnotice = $fnoticeModel->where($msg)->order('create_time desc')->select();      //通知
        $this->assign('fnotice',$fnotice);
        $forum = $forumModel->where($msg)->order('create_time desc')->select();     //开展
        $this->assign('forum',$forum);

        return $this->fetch();
    }

    /**
     * 导师页面
     */
    public function tutor() {
        $id = input('id');
        $tutor = RedtutorTutor::where('id',$id)->find();
        $course = RedtutorCourse::where('tid',$id)->order('create_time desc')->select();
        $tutor['number'] = count($course);
        $tutor['course'] = $course;
        $this->assign('tutor',$tutor);
        
        return $this->fetch();
    }

    /**
     * 导师详情
     */
    public function tutordetail() {
        $id = input('id');
        $detail = RedtutorCourse::where('id',$id)->find();
        $this->assign('detail',$detail);

        return $this->fetch();
    }

    /**
     * 通知详情
     */
    public function tutornotice() {
        $id = input('id');
        $notice = RedtutorNotice::where('id',$id)->find();
        $this->assign('notice',$notice);

        return $this->fetch();
    }

    /**
     * 通知列表
     */
    public function tutornoticelist() {
        $map = array(
            'status' => 1,
        );
        $list = RedtutorNotice::where($map)->order('create_time desc')->select();
        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 红领带动详情
     */
    public function leaddetail() {

        return $this->fetch();
    }

    /**
     * 红领带动列表
     */
    public function leadlist() {

        return $this->fetch();
    }

    /**
     * 红领论坛通知
     */
    public function forumnotice() {

        return $this->fetch();
    }

    /**
     * 红领论坛详情
     */
    public function forum() {
        
        return $this->fetch();
    }
    
    /**
     * 情况详情
     */
    public function forumdetail() {
        
        return $this->fetch();
    }
    
    
}