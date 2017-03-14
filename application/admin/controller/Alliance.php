<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/1/16
 * Time: 14:50
 */

namespace app\admin\controller;
use app\admin\model\AllianceArrange;
use app\admin\model\AllianceShow;
use app\admin\model\Push;
use com\wechat\TPQYWechat;
use think\Config;

/**
 * 地信红盟控制器
 * Class RedAlliance
 * @package app\admin\controller
 */
class Alliance extends Admin {
    /**
     * 活动安排
     */
    public function arrange() {
        $map = array(
            'status' => array('egt',0)
        );
        $list = $this->lists('AllianceArrange',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已发布",2=>"不通过"),
        ));
        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 活动安排新增
     */
    public function arrangeadd() {
        if(IS_POST) {
            $data = input('post.');
            unset($data['id']);
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $data['time'] = strtotime($data['time']);
            $arrangeModel = new AllianceArrange();
            $model = $arrangeModel->validate('Alliance.arrange')->save($data);
            if($model) {
                return $this->success("新增成功",Url('Alliance/arrange'));
            }else{
                return $this->error($arrangeModel->getError());
            }
        }else {
            $this->assign('msg',null);
            return $this->fetch('arrangeedit');
        }
    }

    /**
     * 活动安排修改
     */
    public function arrangeedit() {
        if(IS_POST) {
            $data = input('post.');
            $data['update_time'] = time();
            $data['update_user'] = $_SESSION['think']['user_auth']['id'];
            $data['time'] = strtotime($data['time']);
            $arrangeModel = new AllianceArrange();
            $model = $arrangeModel->validate('Alliance.arrange')->save($data,['id'=>$data['id']]);
            if($model) {
                return $this->success("修改成功",Url('Alliance/arrange'));
            }else{
                return $this->error($arrangeModel->getError());
            }
        }else {
            $id = input('id');
            $msg = AllianceArrange::get($id);
            $this->assign('msg',$msg);
            return $this->fetch();
        }
    }

    /**
     * 活动安排删除
     */
    public function arrangedel() {
        $id = input('id');
        $map = array(
            'status' => -1,
        );
        $arrangeModel = new AllianceArrange();
        $model = $arrangeModel->where('id',$id)->update($map);
        if($model) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }

    }

    /**
     * 活动展示主页
     */
    public function show() {
        $map = array(
            'status' => array('egt',0)
        );
        $list = $this->lists('AllianceShow',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已发布",2=>"不通过"),
            'type' => array(1=>"周轮值",2=>"月主题",3=>"季交流",4=>"年考核"),
        ));
        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 活动展示新增
     */
    public function showadd() {
        if(IS_POST) {
            $data = input('post.');
            unset($data['id']);
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $showModel = new AllianceShow();
            $model = $showModel->validate('Alliance.show')->save($data);
            if($model) {
                return $this->success("新增成功",Url('Alliance/show'));
            }else{
                return $this->error($showModel->getError());
            }
        }else {
            $this->assign('msg',null);
            return $this->fetch('showedit');
        }
    }

    /**
     * 活动展示修改
     */
    public function showedit() {
        if(IS_POST) {
            $data = input('post.');
            $data['update_time'] = time();
            $data['update_user'] = $_SESSION['think']['user_auth']['id'];
            $showModel = new AllianceShow();
            $model = $showModel->validate('Alliance.show')->save($data,['id'=>$data['id']]);
            if($model) {
                return $this->success("修改成功",Url('Alliance/show'));
            }else{
                return $this->error($showModel->getError());
            }
        }else {
            $id = input('id');
            $msg = AllianceShow::get($id);
            $this->assign('msg',$msg);
            return $this->fetch();
        }
    }

    /**
     * 活动展示删除
     */
    public function showdel() {
        $id = input('id');
        $map = array(
            'status' => -1,
        );
        $showModel = new AllianceShow();
        $model = $showModel->where('id',$id)->update($map);
        if($model) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }

    /**
     * 安排列表
     */
    public function arrangelist() {
        if(IS_POST){
            $id = input('id');
            //副图文本周内的新闻消息
            $t = $this->week_time();
            $info = array(
                'id' => array('neq',$id),
                'create_time' => array('egt',$t),
                'status' => 1,
            );
            $infoes = AllianceArrange::where($info)->select();
            return $this->success($infoes);
        }else{
            //消息列表
            $map = array(
                'class' => 8,
                'status' => array('egt',-1),
            );
            $list = $this->lists('Push',$map);
            int_to_string($list,array(
                'status' => array(-1 => '不通过', 0 => '未审核', 1=> '已发送'),
            ));
            //数据重组
            foreach ($list as $value) {
                $msg = AllianceArrange::where('id',$value['focus_main'])->find();
                $value['title'] = $msg['title'];
            }
            $this->assign('list',$list);
            //主图文本周内的新闻消息
            $t = $this->week_time();    //获取本周一时间
            $info = array(
                'create_time' => array('egt',$t),
                'status' => 1,
            );
            $infoes = AllianceArrange::where($info)->select();
            $this->assign('info',$infoes);
            return $this->fetch();
        }
    }

    /**
     * 安排推送
     */
    public function arrangepush(){
        $data = input('post.');
        $arr1 = $data['focus_main'];    //主图文id
        isset($data['focus_vice']) ? $arr2 = $data['focus_vice'] : $arr2 = "";    //副图文id
        if($arr1 == -1){
            return $this->error("请选择主图文!");
        }else{
            //主图文信息
            $focus1 = AllianceArrange::where('id',$arr1)->find();
            $title1 = $focus1['title'];
            $str1 = strip_tags($focus1['theme']);
            $des1 = mb_substr($str1,0,100);
            $content1 = str_replace("&nbsp;","",$des1);  //空格符替换成空
            $url1 = "http://dqpb.0571ztnet.com/home/alliance/informdetail/id/".$focus1['id'].".html";
            $pre1 = "【活动安排】";
            $path1 = "http://dqpb.0571ztnet.com/home/images/arrange.png";
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
                $focus = AllianceArrange::where('id',$value)->find();
                $title = $focus['title'];
                $str = strip_tags($focus['theme']);
                $des = mb_substr($str,0,100);
                $content = str_replace("&nbsp;","",$des);  //空格符替换成空
                $url = "http://dqpb.0571ztnet.com/home/alliance/articledetail/id/".$focus['id'].".html";
                $pre = "【活动安排】";
                $path = "http://dqpb.0571ztnet.com/home/images/arrange.jpg";
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
            "agentid" => 2,
            "news" => $send,
            "safe" => "0"
        );
        $msg = $Wechat->sendMessage($message);

        if ($msg['errcode'] == 0){
            $data['focus_vice'] ? $data['focus_vice'] = json_encode($data['focus_vice']) : $data['focus_vice'] = null;
            $data['create_user'] = session('user_auth.username');
            $data['status'] = 1;
            $data['class'] = 8;
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
     * 展示列表
     */
    public function showlist() {
        if(IS_POST){
            $id = input('id');
            //副图文本周内的新闻消息
            $t = $this->week_time();
            $info = array(
                'id' => array('neq',$id),
                'create_time' => array('egt',$t),
                'status' => 1,
            );
            $infoes = AllianceShow::where($info)->select();
            int_to_string($infoes,array(
                'type' => array(1=>"周轮值",2=>"季交流",3=>"月主题",4=>"年考核"),
            ));
            return $this->success($infoes);
        }else{
            //消息列表
            $map = array(
                'class' => 9,
                'status' => array('egt',-1),
            );
            $list = $this->lists('Push',$map);
            int_to_string($list,array(
                'status' => array(-1 => '不通过', 0 => '未审核', 1=> '已发送'),
            ));
            //数据重组
            foreach ($list as $value) {
                $msg = AllianceShow::where('id',$value['focus_main'])->find();
                $value['title'] = $msg['title'];
            }
            $this->assign('list',$list);
            //主图文本周内的新闻消息
            $t = $this->week_time();    //获取本周一时间
            $info = array(
                'create_time' => array('egt',$t),
                'status' => 1,
            );
            $infoes = AllianceShow::where($info)->select();
            int_to_string($infoes,array(
                'type' => array(1=>"周轮值",2=>"季交流",3=>"月主题",4=>"年考核"),
            ));
            $this->assign('info',$infoes);
            return $this->fetch();
        }
    }

    /**
     * 展示推送
     */
    public function showpush(){
        $data = input('post.');
        $arr1 = $data['focus_main'];    //主图文id
        isset($data['focus_vice']) ? $arr2 = $data['focus_vice'] : $arr2 = "";    //副图文id
        if($arr1 == -1){
            return $this->error("请选择主图文!");
        }else{
            //主图文信息
            $focus1 = AllianceShow::where('id',$arr1)->find();
            $title1 = $focus1['title'];
            $str1 = strip_tags($focus1['content']);
            $des1 = mb_substr($str1,0,100);
            $content1 = str_replace("&nbsp;","",$des1);  //空格符替换成空
            $url1 = "http://dqpb.0571ztnet.com/home/alliance/articledetail/id/".$focus1['id'].".html";
            switch ($focus1['type']) {
                case 1:
                    $pre1 = "【周轮值】";
                    break;
                case 2:
                    $pre1 = "【季交流】";
                    break;
                case 3:
                    $pre1 = "【月主题】";
                    break;
                case 4:
                    $pre1 = "【年考核】";
                    break;
                default:
                    break;
            }
            $path1 = "http://dqpb.0571ztnet.com/home/images/show.jpg";
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
                $focus = AllianceShow::where('id',$value)->find();
                $title = $focus['title'];
                $str = strip_tags($focus['content']);
                $des = mb_substr($str,0,100);
                $content = str_replace("&nbsp;","",$des);  //空格符替换成空
                $url = "http://dqpb.0571ztnet.com/home/alliance/articledetail/id/".$focus['id'].".html";
                switch ($focus['type']) {
                    case 1:
                        $pre = "【周轮值】";
                        break;
                    case 2:
                        $pre = "【季交流】";
                        break;
                    case 3:
                        $pre = "【月主题】";
                        break;
                    case 4:
                        $pre = "【年考核】";
                        break;
                    default:
                        break;
                }
                $path = "http://dqpb.0571ztnet.com/home/images/show.png";
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
            "agentid" => 2,
            "news" => $send,
            "safe" => "0"
        );
        $msg = $Wechat->sendMessage($message);

        if ($msg['errcode'] == 0){
            $data['focus_vice'] ? $data['focus_vice'] = json_encode($data['focus_vice']) : $data['focus_vice'] = null;
            $data['create_user'] = session('user_auth.username');
            $data['status'] = 1;
            $data['class'] = 9;
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