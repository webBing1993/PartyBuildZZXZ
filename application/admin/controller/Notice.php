<?php
/**
 * 会议签到
 * Created by PhpStorm.
 * User: sitff
 * Date: 2017/8/31
 * Time: 下午2:53
 */
namespace app\admin\controller;
use app\admin\model\Notice as NoticeModel;

class Notice extends Admin
{

    /**
     * 会议签到首页
     */
    public function index()
    {
        $map = ['status' => ['egt',0]];
        $list = $this->lists('Notice',$map);
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
            $noticeModel = new NoticeModel();
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $data = $this->changeTime($data);
            if(empty($data['id'])) {

                $statusMsg = '新增';
                unset($data['id']);
                $res = $noticeModel->validate('Notice')->save($data);
            } else {

                $statusMsg = '修改';
                $id = $data['id'];
                unset($data['id']);
                $res = $noticeModel->validate('Notice')->save($data,['id' => $id]);
            }

            if($res){

                return $this->success($statusMsg.'成功!');
            } else if ($res === 0){

                return $this->success('没有修改内容!');
            } else {

                return $this->error($noticeModel->getError());
            }

        }else{

            $id = input('id');
            $msg = '';
            if (!empty($id)) {
                $msg = NoticeModel::get($id);
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
        $info = NoticeModel::where('id',$id)->update($data);
        if($info) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }

}