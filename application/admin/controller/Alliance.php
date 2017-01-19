<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/1/16
 * Time: 14:50
 */

namespace app\admin\controller;
use app\admin\model\AllianceArrange;
use app\admin\model\AllianceShow;

/**
 * 地信红盟控制器
 * Class RedAlliance
 * @package app\admin\controller
 */
class Alliance extends Admin {
    /**
     * 活动安排
     */
    public function arrange() {
        $map = array(
            'status' => array('egt',0)
        );
        $list = $this->lists('AllianceArrange',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已发布",2=>"不通过"),
        ));
        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 活动安排新增
     */
    public function arrangeadd() {
        if(IS_POST) {
            $data = input('post.');
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $data['time'] = strtotime($data['time']);
            $arrangeModel = new AllianceArrange();
            $model = $arrangeModel->validate('Alliance.arrange')->save($data);
            if($model) {
                return $this->success("新增成功",Url('Alliance/arrange'));
            }else{
                return $this->error($arrangeModel->getError());
            }
        }else {
            $this->assign('msg',null);
            return $this->fetch('arrangeedit');
        }
    }

    /**
     * 活动安排修改
     */
    public function arrangeedit() {
        if(IS_POST) {
            $data = input('post.');
            $data['update_time'] = time();
            $data['update_user'] = $_SESSION['think']['user_auth']['id'];
            $data['time'] = strtotime($data['time']);
            $arrangeModel = new AllianceArrange();
            $model = $arrangeModel->validate('Alliance.arrange')->save($data,['id'=>$data['id']]);
            if($model) {
                return $this->success("修改成功",Url('Alliance/arrange'));
            }else{
                return $this->error($arrangeModel->getError());
            }
        }else {
            $id = input('id');
            $msg = AllianceArrange::get($id);
            $this->assign('msg',$msg);
            return $this->fetch();
        }
    }

    /**
     * 活动安排删除
     */
    public function arrangedel() {
        $id = input('id');
        $map = array(
            'status' => -1,
        );
        $arrangeModel = new AllianceArrange();
        $model = $arrangeModel->where('id',$id)->update($map);
        if($model) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }

    }

    /**
     * 活动展示主页
     */
    public function show() {
        $map = array(
            'status' => array('egt',0)
        );
        $list = $this->lists('AllianceShow',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已发布",2=>"不通过"),
            'type' => array(1=>"周轮值",2=>"月主题",3=>"季交流",4=>"年考核"),
        ));
        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 活动展示新增
     */
    public function showadd() {
        if(IS_POST) {
            $data = input('post.');
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $showModel = new AllianceShow();
            $model = $showModel->validate('Alliance.show')->save($data);
            if($model) {
                return $this->success("新增成功",Url('Alliance/show'));
            }else{
                return $this->error($showModel->getError());
            }
        }else {
            $this->assign('msg',null);
            return $this->fetch('showedit');
        }
    }

    /**
     * 活动展示修改
     */
    public function showedit() {
        if(IS_POST) {
            $data = input('post.');
            $data['update_time'] = time();
            $data['update_user'] = $_SESSION['think']['user_auth']['id'];
            $showModel = new AllianceShow();
            $model = $showModel->validate('Alliance.show')->save($data,['id'=>$data['id']]);
            if($model) {
                return $this->success("修改成功",Url('Alliance/show'));
            }else{
                return $this->error($showModel->getError());
            }
        }else {
            $id = input('id');
            $msg = AllianceShow::get($id);
            $this->assign('msg',$msg);
            return $this->fetch();
        }
    }

    /**
     * 活动展示删除
     */
    public function showdel() {
        $id = input('id');
        $map = array(
            'status' => -1,
        );
        $showModel = new AllianceShow();
        $model = $showModel->where('id',$id)->update($map);
        if($model) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }

}