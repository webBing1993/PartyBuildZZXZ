<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/1/16
 * Time: 15:25
 */

namespace app\admin\controller;
use app\admin\model\Talent as TalentModel;
use think\Controller;

/**
 * 人才创业控制器
 *
 * Class EntrepreneurialTalent
 * @package app\admin\controller
 */
class Talent extends Admin {
    /**
     * 主页
     */
    public function index() {
        $map = array(
            'status' => 1,
        );
        $list = $this->lists('Talent',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已发布",2=>"不通过"),
            'type' => array(1=>"政策解读",2=>"申请流程",3=>"创业支持"),
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
            $talentModel = new TalentModel();
            $model = $talentModel->validate('Talent')->save($data);
            if($model) {
                return $this->success("新增成功",Url('Talent/index'));
            }else{
                return $this->error($talentModel->getError());
            }
        }else {
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
            $talentModel = new TalentModel();
            $model = $talentModel->validate('Talent')->save($data,['id'=>$data['id']]);
            if($model) {
                return $this->success("修改成功",Url('Talent/index'));
            }else{
                return $this->error($talentModel->getError());
            }
        }else {
            $id = input('id');
            $msg = TalentModel::get($id);
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
        $talentModel = new TalentModel();
        $model = $talentModel->where('id',$id)->update($map);
        if($model) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }


}