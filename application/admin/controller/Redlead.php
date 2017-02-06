<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/1/23
 * Time: 16:20
 */

namespace app\admin\controller;
use app\admin\model\Redlead as RedleadModel;
/**
 * 红领带动
 * Class Redlead
 * @package app\admin\controller
 */
class Redlead extends Admin {
    /**
     * 主页
     */
    public function index() {
        $map = array(
            'status' => 1
        );
        $list = $this->lists('Redlead',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已发布",2=>"不通过"),
            'type' => array(1=>"领衔攻坚",2=>"岗位师范",3=>"师徒结对",4=>"志愿帮扶"),
        ));
        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 新增
     */
    public function add() {
        if(IS_POST) {
            $data = input('post.');
            unset($data['id']);
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $leadModel = new RedleadModel();
            $model = $leadModel->validate('Redlead')->save($data);
            if($model) {
                return $this->success("新增成功",Url('Redlead/index'));
            }else{
                return $this->error($leadModel->getError());
            }
        }else {
            $this->default_pic();
            $this->assign('msg',null);
            return $this->fetch('edit');
        }
    }

    /**
     * 修改
     */
    public function edit() {
        if(IS_POST) {
            $data = input('post.');
            $data['update_time'] = time();
            $data['update_user'] = $_SESSION['think']['user_auth']['id'];
            $leadModel = new RedleadModel();
            $model = $leadModel->validate('Redlead')->save($data,['id'=>$data['id']]);
            if($model) {
                return $this->success("修改成功",Url('Redlead/index'));
            }else{
                return $this->error($leadModel->getError());
            }
        }else {
            $this->default_pic();

            $id = input('id');
            $msg = RedleadModel::get($id);
            $this->assign('msg',$msg);

            return $this->fetch();
        }
    }

    /**
     * 删除
     */
    public function del() {
        $id = input('id');
        $map = array(
            'status' => -1,
        );
        $leadModel = new RedleadModel();
        $model = $leadModel->where('id',$id)->update($map);
        if($model) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }
}