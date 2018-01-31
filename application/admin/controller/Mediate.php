<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/01/29
 * Time: 15:43
 */
namespace app\admin\controller;

use app\admin\model\Mediation_case as Mediation_caseModel;
use think\Db;
use app\admin\model\Mediation_user as Mediation_userModel;

/**
 * Class Mediate
 * @package 调解案例   控制器
 */
class Mediate extends Admin
{
    /**
     * 主页列表
     */
    public function index()
    {
        $map = array(
            'status' => array('eq', 0),
        );
        $list = $this->lists('Mediation_case', $map);
        foreach($list as $key=>$v){
            $list2 = Db::table('pb_mediation_user')->where('status', 0)->where('userid',$v['uid'])->find();
            $list[$key]['uid']=$list2['name'];
        }
        int_to_string($list, array(
            'status' => array(0 => "已发布"),
            'recommend' => [0 => "否", 1 => "是"]
        ));
        
        $this->assign('list', $list);
        return $this->fetch();
    }

    /**
     * 新闻添加
     */
    public function add()
    {
        if (IS_POST) {
            $data = input('post.');
            if (empty($data['id'])) {
                unset($data['id']);
            }
            /*$list222 = Db::table('pb_mediation_user')->where('userid', $data['mediatorid'])->find();
            $data['mediator'] = $list222['name'];*/
            $mediation_caseModel = new Mediation_caseModel();
            $info = $mediation_caseModel->validate('Mediation')->save($data);

            if ($info) {
                return $this->success("新增成功", Url('Mediate/index'));
            } else {
                return $this->error($mediation_caseModel->getError());
            }
        } else {
            $list = Db::table('pb_mediation_user')->where('status', 0)->select();
          
            $this->assign('list', $list);
            $this->assign('msg', '');

            return $this->fetch('edit');
        }
    }

    /**
     * 修改
     */
    public function edit()
    {
        if (IS_POST) {
            $data = input('post.');
            $mediation_caseModel = new Mediation_caseModel();
            $info = $mediation_caseModel->validate('Mediation')->save($data, ['id' => input('id')]);
            if ($info) {
                return $this->success("修改成功", Url("Mediate/index"));
            } else {
                return $this->get_update_error_msg($mediation_caseModel->getError());
            }
        } else {
            $id = input('id');
            $msg = Mediation_caseModel::get($id);
            $list = Db::table('pb_mediation_user')->where('status', 0)->select();

            $this->assign('list', $list);
            $this->assign('msg', $msg);

            return $this->fetch();
        }
    }

    /**
     * 删除功能
     */
    public function del()
    {
        $id = input('id');
        $data['status'] = '-1';
        $info = Mediation_caseModel::where('id', $id)->update($data);
        if ($info) {
            return $this->success("删除成功");
        } else {
            return $this->error("删除失败");
        }

    }

    //调解员模块的人员列表
    public function user()
    {
        $map = array(
            'status' => array('eq', 0),
        );
        $list = $this->lists('Mediation_user', $map);
        int_to_string($list, array(
            'status' => array(0 => "已发布"),
            'gender' => [0 => "未定义", 1 => "男", 2 => "女"]
        ));

        $this->assign('list', $list);
        return $this->fetch();
    }

    //调解员模块的人员添加
    public function useradd()
    {
        if (IS_POST) {
            $data = input('post.');
            if (!empty($data['name'])){
                $list=Db::table('pb_wechat_user')->where('name',$data['name'])->find();
                if ($list){
                    $data['userid']=$list['userid'];
                }else{
                    return $this->error("调解员姓名错误");
                }
            }
            if (empty($data['id'])) {
                unset($data['id']);
            }
            $mediation_userModel = new Mediation_userModel();
            $info = $mediation_userModel->validate('Mediationuser')->save($data);
            if ($info) {
                return $this->success("新增成功", Url('Mediate/user'));
            } else {
                return $this->error($mediation_userModel->getError());
            }
        } else {

            $this->assign('msg', '');

            return $this->fetch('useredit');
        }
    }

    //调解员模块的人员修改
    public function useredit()
    {
        if (IS_POST) {
            $data = input('post.');
            $mediation_userModel = new Mediation_userModel();
            $info = $mediation_userModel->validate('Mediationuser')->save($data, ['id' => input('id')]);
            if ($info) {
                return $this->success("修改成功", Url("Mediate/user"));
            } else {
                return $this->get_update_error_msg($mediation_userModel->getError());
            }
        } else {
            $id = input('id');
            $msg = Mediation_userModel::get($id);

            $this->assign('msg', $msg);

            return $this->fetch();
        }
    }

    //调解员模块的人员删除
    public function userdel()
    {
        $id = input('id');
        $data['status'] = '-1';
        $info = Mediation_userModel::where('id', $id)->update($data);
        if ($info) {
            return $this->success("删除成功");
        } else {
            return $this->error("删除失败");
        }

    }
   
}