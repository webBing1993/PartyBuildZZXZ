<?php
/**
 * 参会情况
 * Created by PhpStorm.
 * User: ben
 * Date: 2018/1/26
 */

namespace app\admin\controller;
use app\admin\model\Meet as MeetModel;
use app\admin\model\Audit;

class Meet extends Admin
{

    /**
     * 会议签到首页
     */
    public function index()
    {
        $map = ['status' => ['egt',0]];
        $list = $this->lists('Meet',$map);
        int_to_string($list,array(
            'status' => array(0 =>"待审核",1=>"已发布"),
            'recommend' => array(0=>"否",1=>"是"),
        ));
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
            if(empty($data['id'])) {
                $statusMsg = '新增';
                unset($data['id']);
                $res = $meetModel->validate('Meet')->save($data);
                if($res){
                    $auditmodel = new Audit();
                    $audit['type'] = 2;
                    $audit['table'] = 'meet';
                    $audit['url'] = 'service/meetdetail';
                    $audit['aid'] = $meetModel->id;
                    $audit['title'] = $data['title'];
                    $audit['publisher'] = $data['publisher'];
                    $audit['front_cover'] = $data['front_cover'];
                    $auditmodel->save($audit);
                }
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