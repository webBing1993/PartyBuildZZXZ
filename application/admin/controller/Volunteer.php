<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/2/7
 * Time: 9:37
 */

namespace app\admin\controller;
use app\admin\model\Picture;
use app\admin\model\Push;
use app\admin\model\VolunteerOrder;
use app\admin\model\VolunteerOrderReceive;
use app\admin\model\VolunteerRecruit;
use app\admin\model\VolunteerRecruitReceive;
use app\admin\model\VolunteerTeam;
use com\wechat\TPQYWechat;
use think\Config;

/**
 * 志愿服务
 * Class Volunteer
 * @package app\admin\controller
 */
class Volunteer extends Admin {
    /**
     * 服务团队
     */
    public function team() {
        $map = array(
            'status' => 1,
        );
        $list = $this->lists('VolunteerTeam',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已发布",2=>"不通过"),
            'type' => array(1=>"爱心帮扶",2=>"环境美化",3=>"平安巡查"),
        ));
        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 团队添加
     */
    public function teamadd() {
        if(IS_POST) {
            $data = input('post.');
            unset($data['id']);
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $teamModel = new VolunteerTeam();
            $model = $teamModel->validate('Redlead')->save($data);
            if($model) {
                return $this->success("新增成功",Url('Volunteer/team'));
            }else{
                return $this->error($teamModel->getError());
            }
        }else {
            $this->default_pic();
            $this->assign('msg',null);
            return $this->fetch('teamedit');
        }
    }

    /**
     * 团队修改
     */
    public function teamedit() {
        if(IS_POST) {
            $data = input('post.');
            $data['update_time'] = time();
            $data['update_user'] = $_SESSION['think']['user_auth']['id'];
            $teamModel = new VolunteerTeam();
            $model = $teamModel->validate('Redlead')->save($data,['id'=>$data['id']]);
            if($model) {
                return $this->success("修改成功",Url('Volunteer/team'));
            }else{
                return $this->error($teamModel->getError());
            }
        }else {
            $this->default_pic();

            $id = input('id');
            $msg = VolunteerTeam::get($id);
            $this->assign('msg',$msg);

            return $this->fetch();
        }
    }

    /**
     * 团队删除
     */
    public function teamdel() {
        $id = input('id');
        $map = array(
            'status' => -1,
        );
        $teamModel = new VolunteerTeam();
        $model = $teamModel->where('id',$id)->update($map);
        if($model) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }

    /**
     * 服务订单
     */
    public function order() {
        $map = array(
            'status' => array('egt',1),
        );
        $list = $this->lists('VolunteerOrder',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"进行中",2=>"已领取"),
        ));
        foreach ($list as $key => $value) {
            $msg = array(
                'oid' => $value['id'],
                'status' => 1,
            );
            $info = VolunteerOrderReceive::where($msg)->select();
            if($info) {
                $value['is_enroll'] = 1;
            }else{
                $value['is_enroll'] = 0;
            }
        }
        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 订单新增
     */
    public function orderadd() {
        if(IS_POST) {
            $data = input('post.');
            unset($data['id']);
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $orderModel = new VolunteerOrder();
            $model = $orderModel->validate('VolunteerOrder')->save($data);
            if($model) {
                return $this->success("新增成功",Url('Volunteer/order'));
            }else{
                return $this->error($orderModel->getError());
            }
        }else {
            $this->default_pic();
            $this->assign('msg',null);
            return $this->fetch('orderedit');
        }
    }

    /**
     * 订单修改
     */
    public function orderedit() {
        if(IS_POST) {
            $data = input('post.');
            $data['update_time'] = time();
            $data['update_user'] = $_SESSION['think']['user_auth']['id'];
            $orderModel = new VolunteerOrder();
            $model = $orderModel->validate('VolunteerOrder')->save($data,['id'=>$data['id']]);
            if($model) {
                return $this->success("修改成功",Url('Volunteer/order'));
            }else{
                return $this->error($orderModel->getError());
            }
        }else {
            $this->default_pic();

            $id = input('id');
            $msg = VolunteerOrder::get($id);
            $this->assign('msg',$msg);

            return $this->fetch();
        }
    }

    /**
     * 订单删除
     */
    public function orderdel() {
        $id = input('id');
        $map = array(
            'status' => -1,
        );
        $teamModel = new VolunteerOrder();
        $model = $teamModel->where('id',$id)->update($map);
        if($model) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }

    /**
     * 订单领取列表
     */
    public function orderreceive() {
        $id = input('id');
        $map = array(
            'oid' => $id,
            'status' => array('eq',1),
        );
        $list = $this->lists('VolunteerOrderReceive',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已领取"),
        ));
        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 志愿招募
     */
    public function recruit() {
        $map = array(
            'status' => array('eq',1),
        );
        $list = $this->lists('VolunteerRecruit',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已发布",2=>"审核不通过"),
        ));
        foreach ($list as $key => $value) {
            $msg = array(
                'rid' => $value['id'],
                'status' => 1,
            );
            $info = VolunteerRecruitReceive::where($msg)->select();
            if($info) {
                $value['is_enroll'] = 1;
            }else{
                $value['is_enroll'] = 0;
            }
        }
        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 招募新增
     */
    public function recruitadd() {
        if(IS_POST) {
            $data = input('post.');
            unset($data['id']);
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $data['time'] = strtotime($data['time']);
            $recruitModel = new VolunteerRecruit();
            $model = $recruitModel->validate('VolunteerRecruit')->save($data);
            if($model) {
                return $this->success("新增成功",Url('Volunteer/recruit'));
            }else{
                return $this->error($recruitModel->getError());
            }
        }else {
            $this->default_pic();
            $this->assign('msg',null);
            return $this->fetch('recruitedit');
        }
    }

    /**
     * 招募修改
     */
    public function recruitedit() {
        if(IS_POST) {
            $data = input('post.');
            $data['update_time'] = time();
            $data['update_user'] = $_SESSION['think']['user_auth']['id'];
            $data['time'] = strtotime($data['time']);
            $recruitModel = new VolunteerRecruit();
            $model = $recruitModel->validate('VolunteerRecruit')->save($data,['id'=>$data['id']]);
            if($model) {
                return $this->success("修改成功",Url('Volunteer/recruit'));
            }else{
                return $this->error($recruitModel->getError());
            }
        }else {
            $this->default_pic();

            $id = input('id');
            $msg = VolunteerRecruit::get($id);
            $this->assign('msg',$msg);

            return $this->fetch();
        }
    }

    /**
     * 招募删除
     */
    public function recruitdel() {
        $id = input('id');
        $map = array(
            'status' => -1,
        );
        $recruitModel = new VolunteerRecruit();
        $model = $recruitModel->where('id',$id)->update($map);
        if($model) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }

    /**
     * 领取列表
     */
    public function recruitreceive() {
        $id = input('id');
        $map = array(
            'rid' => $id,
            'status' => array('eq',1),
        );
        $list = $this->lists('VolunteerRecruitReceive',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已领取"),
        ));
        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 推送列表
     */
    public function teamlist() {
        if(IS_POST){
            $id = input('id');
            //副图文本周内的新闻消息
            $t = $this->week_time();
            $info = array(
                'id' => array('neq',$id),
                'create_time' => array('egt',$t),
                'status' => 1,
            );
            $infoes = VolunteerTeam::where($info)->select();
            int_to_string($infoes,array(
                'type' => array(1 =>'爱心帮扶', 2 =>'环境美化', 3 =>'平安巡查'),
            ));
            return $this->success($infoes);
        }else{
            //消息列表
            $map = array(
                'class' => 10,
                'status' => array('egt',-1),
            );
            $list = $this->lists('Push',$map);
            int_to_string($list,array(
                'status' => array(-1 => '不通过', 0 => '未审核', 1=> '已发送'),
            ));
            //数据重组
            foreach ($list as $value) {
                $msg = VolunteerTeam::where('id',$value['focus_main'])->find();
                $value['title'] = $msg['title'];
            }
            $this->assign('list',$list);

            //主图文本周内的新闻消息
            $t = $this->week_time();    //获取本周一时间
            $info = array(
                'create_time' => array('egt',$t),
                'status' => 1,
            );
            $infoes = VolunteerTeam::where($info)->select();
            int_to_string($infoes,array(
                'type' => array(1 =>'爱心帮扶', 2 =>'环境美化', 3 =>'平安巡查'),
            ));
            $this->assign('info',$infoes);

            return $this->fetch();
        }
    }

    /**
     * 推送
     */
    public function teampush(){
        $data = input('post.');
        $arr1 = $data['focus_main'];    //主图文id
        isset($data['focus_vice']) ? $arr2 = $data['focus_vice'] : $arr2 = "";    //副图文id
        if($arr1 == -1){
            return $this->error("请选择主图文!");
        }else{
            //主图文信息
            $focus1 = VolunteerTeam::where('id',$arr1)->find();
            $title1 = $focus1['title'];
            $str1 = strip_tags($focus1['content']);
            $des1 = mb_substr($str1,0,100);
            $content1 = str_replace("&nbsp;","",$des1);  //空格符替换成空
            $url1 = "http://dqpb.0571ztnet.com/home/volunteer/teamdetail/id/".$focus1['id'].".html";
            switch ($focus1['type']) {
                case 1:
                    $pre1 = "【爱心帮扶】";
                    break;
                case 2:
                    $pre1 = "【环境美化】";
                    break;
                case 3:
                    $pre1 = "【平安巡查】";
                    break;
                default:
                    break;
            }
            $img1 = Picture::get($focus1['list_image']);
            $path1 = "http://dqpb.0571ztnet.com".$img1['path'];
            $information1 = array(
                "title" => $pre1.$title1,
                "description" => $content1,
                "url" => $url1,
                "picurl" => $path1,
            );
        }

        $information = array();
        if(!empty($arr2)) {
            //副图文信息
            $information2 = array();
            foreach ($arr2 as $key=>$value){
                $focus = VolunteerTeam::where('id',$value)->find();
                $title = $focus['title'];
                $str = strip_tags($focus['content']);
                $des = mb_substr($str,0,100);
                $content = str_replace("&nbsp;","",$des);  //空格符替换成空
                $url = "http://dqpb.0571ztnet.com/home/volunteer/teamdetail/id/".$focus1['id'].".html";
                switch ($focus['type']) {
                    case 1:
                        $pre = "【爱心帮扶】";
                        break;
                    case 2:
                        $pre = "【环境美化】";
                        break;
                    case 3:
                        $pre = "【平安巡查】";
                        break;
                    default:
                        break;
                }
                $img = Picture::get($focus['list_image']);
                $path = "http://dqpb.0571ztnet.com".$img['path'];
                $info = array(
                    "title" => $pre.$title,
                    "description" => $content,
                    "url" => $url,
                    "picurl" => $path,
                );
                $information2[] = $info;
            }

            //数组合并，主图文放在首位
            foreach ($information2 as $k=>$v){
                $information[0] = $information1;
                $information[$k+1] = $v;
            }
        }else{
            $information[0] = $information1;
        }

        //重组成article数据
        $send = array();
        $re[] = $information;
        foreach ($re as $key => $value){
            $key = "articles";
            $send[$key] = $value;
        }

        //发送给服务号
        $Wechat = new TPQYWechat(Config::get('party'));
        $message = array(
//            'totag' => "18", //审核标签用户
            "touser" => "18768112486",
//            "touser" => "@all",   //发送给全体，@all
            "msgtype" => 'news',
            "agentid" => 25,  // 志愿服务
            "news" => $send,
            "safe" => "0"
        );
        $msg = $Wechat->sendMessage($message);

        if ($msg['errcode'] == 0){
            $data['focus_vice'] ? $data['focus_vice'] = json_encode($data['focus_vice']) : $data['focus_vice'] = null;
            $data['create_user'] = session('user_auth.username');
            $data['status'] = 1;
            $data['class'] = 10;
            //保存到推送列表
            $s = Push::create($data);
            if ($s){
                return $this->success("发送成功");
            }else{
                return $this->error("发送失败");
            }
        }else{
            return $this->error("发送失败");
        }
    }

    /**
     * 推送列表
     */
    public function orderlist() {
        if(IS_POST){
            $id = input('id');
            //副图文本周内的新闻消息
            $t = $this->week_time();
            $info = array(
                'id' => array('neq',$id),
                'create_time' => array('egt',$t),
                'status' => 1,
            );
            $infoes = VolunteerOrder::where($info)->select();
            return $this->success($infoes);
        }else{
            //消息列表
            $map = array(
                'class' => 11,
                'status' => array('egt',-1),
            );
            $list = $this->lists('Push',$map);
            int_to_string($list,array(
                'status' => array(-1 => '不通过', 0 => '未审核', 1=> '已发送'),
            ));
            //数据重组
            foreach ($list as $value) {
                $msg = VolunteerOrder::where('id',$value['focus_main'])->find();
                $value['title'] = $msg['title'];
            }
            $this->assign('list',$list);

            //主图文本周内的新闻消息
            $t = $this->week_time();    //获取本周一时间
            $info = array(
                'create_time' => array('egt',$t),
                'status' => 1,
            );
            $infoes = VolunteerOrder::where($info)->select();
            $this->assign('info',$infoes);

            return $this->fetch();
        }
    }

    /**
     * 推送
     */
    public function orderpush(){
        $data = input('post.');
        $arr1 = $data['focus_main'];    //主图文id
        isset($data['focus_vice']) ? $arr2 = $data['focus_vice'] : $arr2 = "";    //副图文id
        if($arr1 == -1){
            return $this->error("请选择主图文!");
        }else{
            //主图文信息
            $focus1 = VolunteerOrder::where('id',$arr1)->find();
            $title1 = $focus1['title'];
            $str1 = strip_tags($focus1['content']);
            $des1 = mb_substr($str1,0,100);
            $content1 = str_replace("&nbsp;","",$des1);  //空格符替换成空
            $url1 = "http://dqpb.0571ztnet.com/home/volunteer/orderdetail/id/".$focus1['id'].".html";
            $pre1 = "【服务订单】";
            $img1 = Picture::get($focus1['list_image']);
            $path1 = "http://dqpb.0571ztnet.com".$img1['path'];
            $information1 = array(
                "title" => $pre1.$title1,
                "description" => $content1,
                "url" => $url1,
                "picurl" => $path1,
            );
        }

        $information = array();
        if(!empty($arr2)) {
            //副图文信息
            $information2 = array();
            foreach ($arr2 as $key=>$value){
                $focus = VolunteerOrder::where('id',$value)->find();
                $title = $focus['title'];
                $str = strip_tags($focus['content']);
                $des = mb_substr($str,0,100);
                $content = str_replace("&nbsp;","",$des);  //空格符替换成空
                $url = "http://dqpb.0571ztnet.com/home/volunteer/orderdetail/id/".$focus1['id'].".html";
                $pre = "【服务订单】";
                $img = Picture::get($focus['list_image']);
                $path = "http://dqpb.0571ztnet.com".$img['path'];
                $info = array(
                    "title" => $pre.$title,
                    "description" => $content,
                    "url" => $url,
                    "picurl" => $path,
                );
                $information2[] = $info;
            }

            //数组合并，主图文放在首位
            foreach ($information2 as $k=>$v){
                $information[0] = $information1;
                $information[$k+1] = $v;
            }
        }else{
            $information[0] = $information1;
        }

        //重组成article数据
        $send = array();
        $re[] = $information;
        foreach ($re as $key => $value){
            $key = "articles";
            $send[$key] = $value;
        }

        //发送给服务号
        $Wechat = new TPQYWechat(Config::get('party'));
        $message = array(
//            'totag' => "18", //审核标签用户
            "touser" => "18768112486",
//            "touser" => "@all",   //发送给全体，@all
            "msgtype" => 'news',
            "agentid" => 25,
            "news" => $send,
            "safe" => "0"
        );
        $msg = $Wechat->sendMessage($message);

        if ($msg['errcode'] == 0){
            $data['focus_vice'] ? $data['focus_vice'] = json_encode($data['focus_vice']) : $data['focus_vice'] = null;
            $data['create_user'] = session('user_auth.username');
            $data['status'] = 1;
            $data['class'] = 11;
            //保存到推送列表
            $s = Push::create($data);
            if ($s){
                return $this->success("发送成功");
            }else{
                return $this->error("发送失败");
            }
        }else{
            return $this->error("发送失败");
        }
    }

    /**
     * 推送列表
     */
    public function recruitlist() {
        if(IS_POST){
            $id = input('id');
            //副图文本周内的新闻消息
            $t = $this->week_time();
            $info = array(
                'id' => array('neq',$id),
                'create_time' => array('egt',$t),
                'status' => 1,
            );
            $infoes = VolunteerRecruit::where($info)->select();
            return $this->success($infoes);
        }else{
            //消息列表
            $map = array(
                'class' => 12,
                'status' => array('egt',-1),
            );
            $list = $this->lists('Push',$map);
            int_to_string($list,array(
                'status' => array(-1 => '不通过', 0 => '未审核', 1=> '已发送'),
            ));
            //数据重组
            foreach ($list as $value) {
                $msg = VolunteerRecruit::where('id',$value['focus_main'])->find();
                $value['title'] = $msg['title'];
            }
            $this->assign('list',$list);

            //主图文本周内的新闻消息
            $t = $this->week_time();    //获取本周一时间
            $info = array(
                'create_time' => array('egt',$t),
                'status' => 1,
            );
            $infoes = VolunteerRecruit::where($info)->select();
            $this->assign('info',$infoes);

            return $this->fetch();
        }
    }

    /**
     * 推送
     */
    public function recuitpush(){
        $data = input('post.');
        $arr1 = $data['focus_main'];    //主图文id
        isset($data['focus_vice']) ? $arr2 = $data['focus_vice'] : $arr2 = "";    //副图文id
        if($arr1 == -1){
            return $this->error("请选择主图文!");
        }else{
            //主图文信息
            $focus1 = VolunteerRecruit::where('id',$arr1)->find();
            $title1 = $focus1['title'];
            $str1 = strip_tags($focus1['content']);
            $des1 = mb_substr($str1,0,100);
            $content1 = str_replace("&nbsp;","",$des1);  //空格符替换成空
            $url1 = "http://dqpb.0571ztnet.com/home/volunteer/recruitdetail/id/".$focus1['id'].".html";
            $pre1 = "【志愿招募】";
            $img1 = Picture::get($focus1['list_image']);
            $path1 = "http://dqpb.0571ztnet.com".$img1['path'];
            $information1 = array(
                "title" => $pre1.$title1,
                "description" => $content1,
                "url" => $url1,
                "picurl" => $path1,
            );
        }

        $information = array();
        if(!empty($arr2)) {
            //副图文信息
            $information2 = array();
            foreach ($arr2 as $key=>$value){
                $focus = VolunteerRecruit::where('id',$value)->find();
                $title = $focus['title'];
                $str = strip_tags($focus['content']);
                $des = mb_substr($str,0,100);
                $content = str_replace("&nbsp;","",$des);  //空格符替换成空
                $url = "http://dqpb.0571ztnet.com/home/volunteer/recruitdetail/id/".$focus1['id'].".html";
                $pre = "【志愿招募】";
                $img = Picture::get($focus['list_image']);
                $path = "http://dqpb.0571ztnet.com".$img['path'];
                $info = array(
                    "title" => $pre.$title,
                    "description" => $content,
                    "url" => $url,
                    "picurl" => $path,
                );
                $information2[] = $info;
            }

            //数组合并，主图文放在首位
            foreach ($information2 as $k=>$v){
                $information[0] = $information1;
                $information[$k+1] = $v;
            }
        }else{
            $information[0] = $information1;
        }

        //重组成article数据
        $send = array();
        $re[] = $information;
        foreach ($re as $key => $value){
            $key = "articles";
            $send[$key] = $value;
        }

        //发送给服务号
        $Wechat = new TPQYWechat(Config::get('party'));
        $message = array(
//            'totag' => "18", //审核标签用户
            "touser" => "18768112486",
//            "touser" => "@all",   //发送给全体，@all
            "msgtype" => 'news',
            "agentid" => 25,
            "news" => $send,
            "safe" => "0"
        );
        $msg = $Wechat->sendMessage($message);

        if ($msg['errcode'] == 0){
            $data['focus_vice'] ? $data['focus_vice'] = json_encode($data['focus_vice']) : $data['focus_vice'] = null;
            $data['create_user'] = session('user_auth.username');
            $data['status'] = 1;
            $data['class'] = 12;
            //保存到推送列表
            $s = Push::create($data);
            if ($s){
                return $this->success("发送成功");
            }else{
                return $this->error("发送失败");
            }
        }else{
            return $this->error("发送失败");
        }
    }

}