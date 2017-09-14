<?php
/**
 * 会议签到
 * Created by PhpStorm.
 * User: sitff
 * Date: 2017/8/31
 * Time: 下午2:53
 */
namespace app\admin\controller;
use app\admin\model\Meet as MeetModel;

class Meet extends Admin
{

    /**
     * 会议签到首页
     */
    public function index()
    {
        $map = ['status' => ['egt',0]];
        $list = $this->lists('Meet',$map);
        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 添加跟修改
     */
    public function add()
    {
        if(IS_POST){
            $data = input('post.');
            $meetModel = new MeetModel();
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $data = $this->changeTime($data);
            if(empty($data['id'])) {

                $statusMsg = '新增';
                unset($data['id']);
                $res = $meetModel->validate('Meet')->save($data);
            } else {

                $statusMsg = '修改';
                $id = $data['id'];
                unset($data['id']);
                $res = $meetModel->validate('Meet')->save($data,['id' => $id]);
            }

            if($res){

                return $this->success($statusMsg.'成功!');
            } else if ($res === 0){

                return $this->success('没有修改内容!');
            } else {

                return $this->error($meetModel->getError());
            }

        }else{

            $id = input('id');
            $msg = '';
            if (!empty($id)) {
                $msg = MeetModel::get($id);
            }
            $this->assign('msg',$msg);
            return $this->fetch();
        }

    }

    /**
     * 时间戳转化
     */
    public function changeTime($data)
    {
        if ($data['meet_time']) {
            $data['meet_time'] = strtotime($data['meet_time']);
        }

        return $data;
    }

    /**
     * 删除
     */
    public function del()
    {
        $id = input('id');
        $data['status'] = '-1';
        $info = MeetModel::where('id',$id)->update($data);
        if($info) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }

}