<?php
/**
 * Created by PhpStorm.
 * User: daire
 * Date: 2018/1/12
 * Time: 9:28
 */

namespace app\home\controller;


class Approved extends Base
{
    public function index(){
        $userId = session('userId');
        $map = array(
//            'status' => ['egt',0],
            'userid' => $userId,
        );
        $list = ApprovedModel::where($map)->order('id desc')->limit(10)->select();
        $status_arr = array(
            '-1' => '不同意',
            '0' => '审批中',
            '1' => '同意',
        );
        foreach ($list as $v){
            $v['status_text'] = $status_arr[$v['status']];
            $v['status_class'] = $v['status'] == 1 ? 'green' : 'red';
        }
        $this->assign('list',$list);
        return $this->fetch();
    }
    /**
     * 加载更多
     */
    public function indexmore(){
        $userId = session('userId');
        $len = input('length');
        $size = input('size');
        $map = array(
            'userid' => $userId,
        );
        $list = ApprovedModel::where($map)->order('id desc')->limit($len,$size)->select();
        $status_arr = array(
            '-1' => '不同意',
            '0' => '审批中',
            '1' => '同意',
        );
        foreach($list as $v){
            $v['status_text'] = $status_arr[$v['status']];
            $v['status_class'] = $v['status'] == 1 ? 'green' : 'red';
            $v['time'] = date("Y-m-d H:i",$v['create_time']);
            /*if($value['start_time'] < time()) {
                $value['is'] = 1;
            }else{
                $value['is'] = 0;
            }*/
        }
        if($list){
            return $this->success("加载成功",'',$list);
        }else{
            return $this->error("加载失败");
        }
    }

    /**
     * 请假申请
     */
    public function leave(){
        if(IS_POST) {
            $userId = session('userId');
            $data = input('post.');
            $data['userid'] = $userId;
            $data['type'] = 1;
            /*$department = Db::table('pb_wechat_department_user')
                ->alias('a')
                ->join('pb_wechat_department b','a.departmentid = b.id','LEFT')
                ->where('a.userid',$userId)
                ->order('a.id desc')
                ->find();
            $data['department_name'] = $department['name'];*/
            $data['publisher'] = WechatUser::where(['userid' => $userId])->value('name');
            $data['title'] = "请假";
            $data['start_time'] = strtotime($data['start_time']);
            $data['end_time'] = strtotime($data['end_time']);
            if(ceil($data['duration']) <= 2){
                $data['tag'] = 2;
            }else{
                $data['tag'] = 1;
            }
            $data['create_user'] = $userId;
            $data['create_time'] = time();
            $data['status'] = 0;
            $approvedModel = new ApprovedModel();
            $model = $approvedModel->create($data);
            if($model) {
                $this->push_review("【请假申请】", $data['tag']);
                return $this->success("提交成功");
            }else{
                return $this->error("提交失败");
            }
        }else{
            return $this->fetch();
        }
    }
    /**
     * 请假详情
     */
    public function leavedetail(){
        $userId = session('userId');
        $id = input('id');
        $approvedModel = new ApprovedModel();
        $model = $approvedModel->get($id);
        $status_arr = array(
            '-1' => '不同意',
            '0' => '审批中',
            '1' => '同意',
        );
        $model['status_text'] = $status_arr[$model['status']];
        $model['status_class'] = $model['status'] == 1 ? 'green' : 'red';
        $this->assign('model',$model);
        return $this->fetch();
    }
    /**
     * 出差申请
     */
    public function trip(){
        if(IS_POST) {
            $userId = session('userId');
            $data = input('post.');
            $data['userid'] = $userId;
            $data['type'] = 2;
            /*$department = Db::table('pb_wechat_department_user')
                ->alias('a')
                ->join('pb_wechat_department b','a.departmentid = b.id','LEFT')
                ->where('a.userid',$userId)
                ->order('a.id desc')
                ->find();
            $data['department_name'] = $department['name'];*/
            $data['publisher'] = WechatUser::where(['userid' => $userId])->value('name');
            $data['title'] = "出差";
            $data['start_time'] = strtotime($data['start_time']);
            $data['end_time'] = strtotime($data['end_time']);
            $data['tag'] = 1;
            $data['create_user'] = $userId;
            $data['create_time'] = time();
            $data['status'] = 0;
            $approvedModel = new ApprovedModel();
            $model = $approvedModel->create($data);
            if($model) {
                $this->push_review("【出差申请】", $data['tag']);
                return $this->success("提交成功");
            }else{
                return $this->error("提交失败");
            }
        }else{
            return $this->fetch();
        }
    }
    /**
     * 出差详情
     */
    public function tripdetail(){
        $userId = session('userId');
        $id = input('id');
        $approvedModel = new ApprovedModel();
        $model = $approvedModel->get($id);
        $status_arr = array(
            '-1' => '不同意',
            '0' => '审批中',
            '1' => '同意',
        );
        $model['status_text'] = $status_arr[$model['status']];
        $model['status_class'] = $model['status'] == 1 ? 'green' : 'red';
        $this->assign('model',$model);
        return $this->fetch();
    }
    /**
     * 报销申请
     */
    public function reimbursement(){
        if(IS_POST) {
            $userId = session('userId');
            $data = input('post.');
            $data['userid'] = $userId;
            $data['type'] = 3;
            /*$department = Db::table('pb_wechat_department_user')
                ->alias('a')
                ->join('pb_wechat_department b','a.departmentid = b.id','LEFT')
                ->where('a.userid',$userId)
                ->order('a.id desc')
                ->find();
            $data['department_name'] = $department['name'];*/
            $data['publisher'] = WechatUser::where(['userid' => $userId])->value('name');
            $data['title'] = "报销";
            $data['tag'] = 1;
            $data['create_user'] = $userId;
            $data['create_time'] = time();
            $data['status'] = 0;
            $approvedModel = new ApprovedModel();
            $model = $approvedModel->create($data);
            if($model) {
                $this->push_review("【报销申请】", $data['tag']);
                return $this->success("提交成功");
            }else{
                return $this->error("提交失败");
            }
        }else{
            return $this->fetch();
        }
    }
    /**
     * 报销详情
     */
    public function reimbursementdetail(){
        $userId = session('userId');
        $id = input('id');
        $approvedModel = new ApprovedModel();
        $model = $approvedModel->get($id);
        $status_arr = array(
            '-1' => '不同意',
            '0' => '审批中',
            '1' => '同意',
        );
        $model['status_text'] = $status_arr[$model['status']];
        $model['status_class'] = $model['status'] == 1 ? 'green' : 'red';
        $this->assign('model',$model);
        return $this->fetch();
    }
    /**
     * 文本卡片推送公用方法
     */
    public function push_review($pre, $tag){
        $httpUrl = config('http_url');
        $send = array(
            "title" => "审核通知",
            "description" => "您有一条".$pre."未审核<br>请尽快审核",
            "url" => $httpUrl."/home/approved/reviewlist",
        );
        //发送给企业号
        $Wechat = new TPQYWechat(Config::get('review'));
        $newsConf = config('review');
        $message = array(
            "msgtype" => 'textcard',
            "agentid" => $newsConf['agentid'],
            "textcard" => $send,
            "safe" => "0"
        );
        if($httpUrl == "http://dzgcpb.0571ztnet.com"){
            $message['totag'] = $tag;
        }else{
//            $message['totag'] = $tag;
            $message['touser'] = config('touser');//发送给全体，@all
        }
        return $Wechat->sendMessage($message);
    }
    /**
     * @return mixed
     * 未审核列表
     */
    public function reviewlist() {
//        $userId = session('userId');
//        $tag = WechatUserTag::where(['userid' => $userId, 'tagid' => 1])->find();
//        if(!$tag){
//            $tag = WechatUserTag::where(['userid' => $userId, 'tagid' => 2])->find();
//            if(!$tag){
//                return $this->redirect("index/index");
//            }else{
//                $user_tag = 2;
//            }
//        }else{
//            $user_tag = 1;
//        }
//        $map = array(
//            'tag' => $user_tag,
//            'status' => 0,
//        );
//        $list = ApprovedModel::where($map)->order('id desc')->limit(10)->select();
//        $this->assign('list',$list);
        return $this->fetch();
    }
    public function lists() {

        return $this->fetch();
    }

    /**
     * 未审核列表 加载更多
     */
    public function reviewlistmore(){
        $len = input('length');
        $userId = session('userId');
        $tag = WechatUserTag::where(['userid' => $userId, 'tagid' => 1])->find();
        if(!$tag){
            $tag = WechatUserTag::where(['userid' => $userId, 'tagid' => 2])->find();
            if(!$tag){
                return $this->redirect("index/index");
            }else{
                $user_tag = 2;
            }
        }else{
            $user_tag = 1;
        }
        $map = array(
            'tag' => $user_tag,
            'status' => 0,
        );
        $list = ApprovedModel::where($map)->order('id desc')->limit($len,6)->select();
        foreach($list as $v){
            $v['time'] = date("Y-m-d H:i",$v['create_time']);
            /*if($value['start_time'] < time()) {
                $value['is'] = 1;
            }else{
                $value['is'] = 0;
            }*/
        }
        if($list){
            return $this->success("加载成功",'',$list);
        }else{
            return $this->error("加载失败");
        }
    }

    /**
     * 审核操作
     */
    public function review()
    {
//        $id = input('id');
//        $status = input('status');
//        $info = ApprovedModel::update(['status' => $status], ['id' => $id]);
//        if ($info) {
//            return $this->success();
//        } else {
            return $this->error();
//        }
    }

    /**
     * @return mixed
     * 已审核列表
     */
    public function passlist() {
        $userId = session('userId');
        $tag = WechatUserTag::where(['userid' => $userId, 'tagid' => 1])->find();
        if(!$tag){
            $tag = WechatUserTag::where(['userid' => $userId, 'tagid' => 2])->find();
            if(!$tag){
                return $this->redirect("index/index");
            }else{
                $user_tag = 2;
            }
        }else{
            $user_tag = 1;
        }
        $map = array(
            'tag' => $user_tag,
            'status' => ['neq', 0],
        );
        $list = ApprovedModel::where($map)->order('id desc')->limit(10)->select();
        $this->assign('list',$list);
        return $this->fetch();
    }
    /**
     * 已审核列表 加载更多
     */
    public function passlistmore(){
        $len = input('length');
        $userId = session('userId');
        $tag = WechatUserTag::where(['userid' => $userId, 'tagid' => 1])->find();
        if(!$tag){
            $tag = WechatUserTag::where(['userid' => $userId, 'tagid' => 2])->find();
            if(!$tag){
                return $this->redirect("index/index");
            }else{
                $user_tag = 2;
            }
        }else{
            $user_tag = 1;
        }
        $map = array(
            'tag' => $user_tag,
            'status' => ['neq', 0],
        );
        $list = ApprovedModel::where($map)->order('id desc')->limit($len,6)->select();
        foreach($list as $v){
            $v['time'] = date("Y-m-d H:i",$v['create_time']);
            /*if($value['start_time'] < time()) {
                $value['is'] = 1;
            }else{
                $value['is'] = 0;
            }*/
        }
        if($list){
            return $this->success("加载成功",'',$list);
        }else{
            return $this->error("加载失败");
        }
    }

}