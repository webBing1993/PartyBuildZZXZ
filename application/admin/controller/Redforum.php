<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/2/3
 * Time: 13:47
 */

namespace app\admin\controller;
use app\admin\model\RedforumDetail;
use app\admin\model\RedforumNotice;
use app\admin\model\Redforum as RedforumModel;

/**
 * 红领论坛
 * Class Redforum
 * @package app\admin\controller
 */
class Redforum extends Admin {
    /**
     * 通知主页
     */
    public function notice() {
        $map = array(
            'status' => 1
        );
        $list = $this->lists('RedforumNotice',$map);
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
            $noticeModel = new RedforumNotice();
            $model = $noticeModel->validate('RedforumNotice')->save($data);
            if($model) {
                return $this->success("新增成功",Url('Redforum/notice'));
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
            $noticeModel = new RedforumNotice();
            $model = $noticeModel->validate('RedforumNotice')->save($data,['id'=>$data['id']]);
            if($model) {
                return $this->success("修改成功",Url('Redforum/notice'));
            }else{
                return $this->error($noticeModel->getError());
            }
        }else {
            $id = input('id');
            $msg = RedforumNotice::get($id);
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
        $noticeModel = new RedforumNotice();
        $model = $noticeModel->where('id',$id)->update($map);
        if($model) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }

    /**
     * 活动开展主题
     */
    public function forum() {
        $map = array(
            'status' => 1
        );
        $list = $this->lists('Redforum',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已发布",2=>"不通过"),
        ));
        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 主题新增
     */
    public function forumadd() {
        if(IS_POST) {
            $data = input('post.');
            unset($data['id']);
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $forumModel = new RedforumModel();
            $model = $forumModel->validate('Redforum')->save($data);
            if($model) {
                return $this->success("新增成功",Url('Redforum/forum'));
            }else{
                return $this->error($forumModel->getError());
            }
        }else {
            //获取通知
            $map = array(
                'status' => 1,
            );
            $notice = RedforumNotice::where($map)->order('create_time desc')->select();
            $this->assign('notice',$notice);

            $this->assign('msg',null);
            return $this->fetch('forumedit');
        }
    }

    /**
     * 主题修改
     */
    public function forumedit() {
        if(IS_POST) {
            $data = input('post.');
            $data['update_time'] = time();
            $data['update_user'] = $_SESSION['think']['user_auth']['id'];
            $forumModel = new RedforumModel();
            $model = $forumModel->validate('Redforum')->save($data,['id'=>$data['id']]);
            if($model) {
                return $this->success("修改成功",Url('Redforum/forum'));
            }else{
                return $this->error($forumModel->getError());
            }
        }else {
            //获取通知
            $map = array(
                'status' => 1,
            );
            $notice = RedforumNotice::where($map)->order('create_time desc')->select();
            $this->assign('notice',$notice);

            $id = input('id');
            $msg = RedforumModel::get($id);
            $this->assign('msg',$msg);
            return $this->fetch();
        }
    }

    /**
     * 主题删除
     */
    public function forumdel() {
        $id = input('id');
        $map = array(
            'status' => -1,
        );
        $noticeModel = new RedforumModel();
        $model = $noticeModel->where('id',$id)->update($map);
        if($model) {
            RedforumDetail::where('rid',$id)->update($map);
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }

    /**
     * 详情主页
     */
    public function detail() {
        $map = array(
            'status' => 1
        );
        $list = $this->lists('RedforumDetail',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已发布",2=>"不通过"),
        ));
        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 详情新增
     */
    public function detailadd() {
        if(IS_POST) {
            $data = input('post.');
            unset($data['id']);
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $detailModel = new RedforumDetail();
            $model = $detailModel->validate('RedforumDetail')->save($data);
            if($model) {
                return $this->success("新增成功",Url('Redforum/detail'));
            }else{
                return $this->error($detailModel->getError());
            }
        }else {
            //获取主题
            $map = array(
                'status' => 1,
            );
            $theme = RedforumModel::where($map)->order('create_time desc')->select();
            $this->assign('theme',$theme);

            $this->assign('msg',null);
            return $this->fetch('detailedit');
        }
    }

    /**
     * 详情修改
     */
    public function detailedit() {
        if(IS_POST) {
            $data = input('post.');
            $data['update_time'] = time();
            $data['update_user'] = $_SESSION['think']['user_auth']['id'];
            $detailModel = new RedforumDetail();
            $model = $detailModel->validate('RedforumDetail')->save($data,['id'=>$data['id']]);
            if($model) {
                return $this->success("修改成功",Url('Redforum/detail'));
            }else{
                return $this->error($detailModel->getError());
            }
        }else {
            //获取主题
            $map = array(
                'status' => 1,
            );
            $theme = RedforumModel::where($map)->order('create_time desc')->select();
            $this->assign('theme',$theme);

            $id = input('id');
            $msg = RedforumDetail::get($id);
            $this->assign('msg',$msg);
            return $this->fetch();
        }
    }

    /**
     * 详情删除
     */
    public function detaildel() {
        $id = input('id');
        $map = array(
            'status' => -1,
        );
        $noticeModel = new RedforumDetail();
        $model = $noticeModel->where('id',$id)->update($map);
        if($model) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }

}