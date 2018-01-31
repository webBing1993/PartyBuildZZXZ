<?php
/**
 * Created by PhpStorm.
 * User: laowang
 * Date: 2017/1/16
 * Time: 15:07
 */
namespace app\admin\controller;

use app\admin\model\Learns as LearnsModel;
use app\admin\model\Picture;
use app\admin\model\Push;
use app\admin\model\Audit;
use com\wechat\TPQYWechat;
use think\Config;
use think\Db;
/**
 * Class Learns
 * @package 十九大专题
 */
class Learns extends Admin {
    /**
     * 主页
     */
    public function index(){
        $map = array(
            'status' => array('egt',0),
            'type'=> array('in',[1,2])
        );
        $list = $this->lists('Learns',$map);
        int_to_string($list,array(
            'status' => array(0 =>"待审核",1=>"已发布"),
            'recommend' => array(0=>"否",1=>"是"),
            'type' => array(1=>"视频",2=>"文章")
        ));
        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 添加
     */
    public function add(){
        if(IS_POST){
            $data = input('post.');
            if(empty($data['id'])) {
                unset($data['id']);
            }
            if($data['type'] == 1){
                if($data['video_path'] == "" && $data['net_path'] == ""){
                    return $this->error("请上传视频文件或网址，如文件过大，请耐心等待..");
                }
            }
            $LearnsModel = new LearnsModel();
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $model = $LearnsModel->validate('Learns')->save($data);
            if($model){
                $auditmodel = new Audit();
                $audit['type'] = 3;
                $audit['table'] = 'learns';
                if($data['type'] == 1){
                    $audit['url'] = 'learns/video';
                }else{
                    $audit['url'] = 'learns/article';
                }
                $audit['aid'] = $LearnsModel->id;
                $audit['title'] = $data['title'];
                $audit['publisher'] = $data['publisher'];
                $audit['front_cover'] = $data['front_cover'];
                $auditmodel->save($audit);

                return $this->success('新增成功!',Url("Learns/index"));
            }else{
                return $this->error($LearnsModel->getError());
            }
        }else{
            $this->assign('msg','');

            return $this->fetch('edit');
        }
    }

    /**
     * 修改
     */
    public function edit(){
        if(IS_POST){
            $data = input('post.');
            if($data['type'] == 1){
                if($data['video_path'] == "" && $data['net_path'] == ""){
                    return $this->error("请上传视频文件或网址，如文件过大，请耐心等待..");
                }
            }
            $LearnsModel = new LearnsModel();
            $model = $LearnsModel->validate('Learns')->save($data,['id'=>input('id')]);
            if($model){
                //更新审核表内信息
                $audit['title'] = $data['title'];
                $audit['publisher'] = $data['publisher'];
                $audit['front_cover'] = $data['front_cover'];
                Db::table('pb_audit')->where('type', 3)->where('aid',input('id'))->update($audit);
                return $this->success('修改成功!',Url("Learns/index"));
            }else{
                return $this->get_update_error_msg($LearnsModel->getError());
            }
        }else{

            //根据id获取课程
            $id = input('id');
            if(empty($id)){
                return $this->error("系统错误,不存在该条数据!");
            }else{
                $msg = LearnsModel::get($id);
                $this->assign('msg',$msg);
            }
            return $this->fetch();
        }
    }

    /**
     * 删除
     */
    public function del(){
        $id = input('id');
        $data['status'] = '-1';
        $info = LearnsModel::where('id',$id)->update($data);
        if($info) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }
}