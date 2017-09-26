<?php
/**
 * Created by PhpStorm.
 * User: laowang
 * Date: 2017/1/16
 * Time: 15:07
 */
namespace app\admin\controller;

use app\admin\model\Clean as LearnModel;
use app\admin\model\Picture;
use app\admin\model\Push;
use com\wechat\TPQYWechat;
use think\Config;

/**
 * Class Learn
 * @package 廉政文化
 */
class Clean extends Admin {
    /**
     * 主页
     */
    public function index(){
        $map = array(
            'status' => array('eq',1),  // 推送  未推送
        );
        $list = $this->lists('Clean',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>'已发布'),
            'recommend' => array(0=>"否",1=>"是"),
            'type' => array(1=>"今日说法",2=>"学思践悟",3=>"法律法规")
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
            }else{
                if($data['list_image'] == ""){
                    return $this->error("请上传文章顶部图片");
                }
            }
            $learnModel = new LearnModel();
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $model = $learnModel->validate('Learn.act')->save($data);
            if($model){
                return $this->success('新增成功!',Url("Clean/index"));
            }else{
                return $this->error($learnModel->getError());
            }
        }else{
            $this->default_pic();
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
            }else{
                if($data['list_image'] == ""){
                    return $this->error("请上传文章顶部图片");
                }
            }
            $learnModel = new LearnModel();
            $model = $learnModel->validate('Learn.act')->save($data,['id'=>input('id')]);
            if($model){
                return $this->success('修改成功!',Url("Clean/index"));
            }else{
                return $this->get_update_error_msg($learnModel->getError());
            }
        }else{
            $this->default_pic();
            //根据id获取课程
            $id = input('id');
            if(empty($id)){
                return $this->error("不存在该课程");
            }else{
                $msg = LearnModel::get($id);
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
        $info = LearnModel::where('id',$id)->update($data);
        if($info) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }

    /**
     * 推送列表
     */
    public function pushlist() {
        if(IS_POST){
            $id = input('id');
            //副图文本周内的新闻消息
            date_default_timezone_set("PRC");        //初始化时区
            $y = date("Y");        //获取当天的年份
            $m = date("m");        //获取当天的月份
            $d = date("d");        //获取当天的号数
            $todayTime= mktime(0,0,0,$m,$d,$y);        //将今天开始的年月日时分秒，转换成unix时间戳
            $time = date("N",$todayTime);        //获取星期数进行判断，当前时间做对比取本周一和上周一时间。
            //$t为本周周一，$s为上周周一
            switch($time){
                case 1: $t = $todayTime;
                    break;
                case 2: $t = $todayTime - 86400*1;
                    break;
                case 3: $t = $todayTime - 86400*2;
                    break;
                case 4: $t = $todayTime - 86400*3;
                    break;
                case 5: $t = $todayTime - 86400*4;
                    break;
                case 6: $t = $todayTime - 86400*5;
                    break;
                case 7: $t = $todayTime - 86400*6;
                    break;
                default:
            }
            $info = array(
                'id' => array('neq',$id),
//                'create_time' => array('egt',$t),
                'status' => 1,
            );
            $infoes = LearnModel::where($info)->select();
            foreach ($infoes as $value) {
                if($value['type'] == 1){
                    $value['type_text'] = "【今日说法】";
                } else if ($value['type'] == 2) {
                    $value['type_text'] = "【学思践悟】";
                } else if ($value['type'] == 3) {
                    $value['type_text'] = "【法律法规】";
                }
            }
            return $this->success($infoes);
        }else{
            //消息列表
            $map = array(
                'class' => 7,
                'status' => array('egt',-1),
            );
            $list = $this->lists('Push',$map);
            int_to_string($list,array(
                'status' => array(-1 => '不通过', 0 => '未审核', 1=> '已发送'),
            ));
            //数据重组
            foreach ($list as $value) {
                $msg = LearnModel::where('id',$value['focus_main'])->find();
                $value['title'] = $msg['title'];
            }
            $this->assign('list',$list);

            //主图文本周内的新闻消息
            date_default_timezone_set("PRC");        //初始化时区
            $y = date("Y");        //获取当天的年份
            $m = date("m");        //获取当天的月份
            $d = date("d");        //获取当天的号数
            $todayTime= mktime(0,0,0,$m,$d,$y);        //将今天开始的年月日时分秒，转换成unix时间戳
            $time = date("N",$todayTime);        //获取星期数进行判断，当前时间做对比取本周一和上周一时间。
            //$t为本周周一，$s为上周周一
            switch($time){
                case 1: $t = $todayTime;
                    break;
                case 2: $t = $todayTime - 86400*1;
                    break;
                case 3: $t = $todayTime - 86400*2;
                    break;
                case 4: $t = $todayTime - 86400*3;
                    break;
                case 5: $t = $todayTime - 86400*4;
                    break;
                case 6: $t = $todayTime - 86400*5;
                    break;
                case 7: $t = $todayTime - 86400*6;
                    break;
                default:
            }
            $info = array(
//                'create_time' => array('egt',$t),
                'status' => 1,
            );
            $infoes = LearnModel::where($info)->select();
            foreach ($infoes as $value) {
                if($value['type'] == 1){
                    $value['type_text'] = "【今日说法】";
                } else if ($value['type'] == 2) {
                    $value['type_text'] = "【学思践悟】";
                } else if ($value['type'] == 3) {
                    $value['type_text'] = "【法律法规】";
                }
            }
            $this->assign('info',$infoes);

            return $this->fetch();
        }
    }

    /**
     * 推送
     */
    public function push()
    {
        $data = input('post.');
        $httpUrl = config('http_url');
        $arr1 = $data['focus_main'];    //主图文id
        isset($data['focus_vice']) ? $arr2 = $data['focus_vice'] : $arr2 = "";    //副图文id
        if ($arr1 == -1) {
            return $this->error("请选择主图文!");
        } else {
            //主图文信息
            $focus1 = LearnModel::where('id', $arr1)->find();
            $title1 = $focus1['title'];
            if ($focus1['type'] == 1) {

                $type_name1 = "【今日说法】";
                $url1 = $httpUrl."/home/clean/video/id/" . $focus1['id'] . ".html";
            } else if ($focus1['type'] == 2) {

                $type_name1 = "【学思践悟】";
                $url1 = $httpUrl."/home/clean/article/id/" . $focus1['id'] . ".html";
            } else if ($focus1['type'] == 3){

                $type_name1 = "【法律法规】";
                $url1 = $httpUrl."/home/clean/article/id/" . $focus1['id'] . ".html";
            }
            $str1 = strip_tags($focus1['content']);
            $des1 = mb_substr($str1, 0, 100);
            $content1 = str_replace("&nbsp;", "", $des1);  //空格符替换成空
            $img1 = Picture::get($focus1['front_cover']);
            $path1 = $httpUrl . $img1['path'];
            $information1 = array(
                "title" => $type_name1 . $title1,
                "description" => $content1,
                "url" => $url1,
                "picurl" => $path1,
            );
        }

        $information = array();
        if (!empty($arr2)) {
            //副图文信息
            $information2 = array();
            foreach ($arr2 as $key => $value) {
                $focus = LearnModel::where('id', $value)->find();
                if ($focus['type'] == 1) {

                    $type_name = "【今日说法】";
                    $url = $httpUrl."/home/clean/video/id/" . $focus1['id'] . ".html";
                } else if ($focus['type'] == 2) {

                    $type_name = "【学思践悟】";
                    $url = $httpUrl."/home/clean/article/id/" . $focus1['id'] . ".html";
                } else if ($focus['type'] == 3) {

                    $type_name = "【法律法规】";
                    $url = $httpUrl."/home/clean/article/id/" . $focus1['id'] . ".html";
                }
                $title = $focus['title'];
                $str = strip_tags($focus['content']);
                $des = mb_substr($str, 0, 100);
                $content = str_replace("&nbsp;", "", $des);  //空格符替换成空
                $img = Picture::get($focus['front_cover']);
                $path = $httpUrl. $img['path'];
                $info = array(
                    "title" => $type_name . $title,
                    "description" => $content,
                    "url" => $url,
                    "picurl" => $path,
                );
                $information2[] = $info;
            }

            //数组合并，主图文放在首位
            foreach ($information2 as $k => $v) {
                $information[0] = $information1;
                $information[$k + 1] = $v;
            }
        } else {
            $information[0] = $information1;
        }

        //重组成article数据
        $send = array();
        $re[] = $information;
        foreach ($re as $key => $value) {
            $key = "articles";
            $send[$key] = $value;
        }

        //发送给企业号
        $Wechat = new TPQYWechat(Config::get('Clean'));
        $touser = config('touser');
        $newsConf = config('learn');
        $message = array(
            "touser" => $touser, //发送给全体，@all
            "msgtype" => 'news',
            "agentid" => $newsConf['agentid'],
            "news" => $send,
            "safe" => "0"
        );
        $msg = $Wechat->sendMessage($message);

        if ($msg['errcode'] == 0) {
            $data['focus_vice'] ? $data['focus_vice'] = json_encode($data['focus_vice']) : $data['focus_vice'] = null;
            $data['create_user'] = session('user_auth.username');
            $data['class'] = 7;
            //保存到推送列表
            $s = Push::create($data);
            if ($s) {
                return $this->success("发送成功");
            } else {
                return $this->error("发送失败");
            }
        } else {
            return $this->error("发送失败");
        }
    }

}