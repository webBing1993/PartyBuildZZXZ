<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/1/23
 * Time: 16:20
 */

namespace app\admin\controller;
use app\admin\model\Picture;
use app\admin\model\Push;
use app\admin\model\Redlead as RedleadModel;
use com\wechat\TPQYWechat;
use think\Config;

/**
 * 红领带动
 * Class Redlead
 * @package app\admin\controller
 */
class Redlead extends Admin {
    /**
     * 主页
     */
    public function index() {
        $map = array(
            'status' => 1
        );
        $list = $this->lists('Redlead',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已发布",2=>"不通过"),
            'type' => array(1=>"领衔攻坚",2=>"岗位师范",3=>"师徒结对",4=>"志愿帮扶"),
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
            $leadModel = new RedleadModel();
            $model = $leadModel->validate('Redlead')->save($data);
            if($model) {
                return $this->success("新增成功",Url('Redlead/index'));
            }else{
                return $this->error($leadModel->getError());
            }
        }else {
            $this->default_pic();
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
            $leadModel = new RedleadModel();
            $model = $leadModel->validate('Redlead')->save($data,['id'=>$data['id']]);
            if($model) {
                return $this->success("修改成功",Url('Redlead/index'));
            }else{
                return $this->error($leadModel->getError());
            }
        }else {
            $this->default_pic();

            $id = input('id');
            $msg = RedleadModel::get($id);
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
        $leadModel = new RedleadModel();
        $model = $leadModel->where('id',$id)->update($map);
        if($model) {
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
            $t = $this->week_time();
            $info = array(
                'id' => array('neq',$id),
                'create_time' => array('egt',$t),
                'status' => 1,
            );
            $infoes = RedleadModel::where($info)->select();
            int_to_string($infoes,array(
                'type' => array(1 => '领衔攻坚', 2 => '岗位师范', 3=> '师徒结对', 4=>'志愿帮扶'),
            ));
            return $this->success($infoes);
        }else{
            //消息列表
            $map = array(
                'class' => 5,
                'status' => array('egt',-1),
            );
            $list = $this->lists('Push',$map);
            int_to_string($list,array(
                'status' => array(-1 => '不通过', 0 => '未审核', 1=> '已发送'),
            ));
            //数据重组
            foreach ($list as $value) {
                $msg = RedleadModel::where('id',$value['focus_main'])->find();
                $value['title'] = $msg['title'];
            }
            $this->assign('list',$list);
            //主图文本周内的新闻消息
            $t = $this->week_time();    //获取本周一时间
            $info = array(
                'create_time' => array('egt',$t),
                'status' => 1,
            );
            $infoes = RedleadModel::where($info)->select();
            int_to_string($infoes,array(
                'type' => array(1 => '领衔攻坚', 2 => '岗位师范', 3=> '师徒结对', 4=>'志愿帮扶'),
            ));
            $this->assign('info',$infoes);

            return $this->fetch();
        }
    }

    /**
     * 推送
     */
    public function push(){
        $data = input('post.');
        $arr1 = $data['focus_main'];    //主图文id
        isset($data['focus_vice']) ? $arr2 = $data['focus_vice'] : $arr2 = "";    //副图文id
        if($arr1 == -1){
            return $this->error("请选择主图文!");
        }else{
            //主图文信息
            $focus1 = RedleadModel::where('id',$arr1)->find();
            $title1 = $focus1['title'];
            $str1 = strip_tags($focus1['content']);
            $des1 = mb_substr($str1,0,100);
            $content1 = str_replace("&nbsp;","",$des1);  //空格符替换成空
            $url1 = "http://dqpb.0571ztnet.com/home/college/leaddetail/id/".$focus1['id'].".html";
            switch ($focus1['type']) {
                case 1:
                    $pre1 = "【领衔攻坚】";
                    break;
                case 2:
                    $pre1 = "【岗位师范】";
                    break;
                case 3:
                    $pre1 = "【师徒结对】";
                    break;
                case 4:
                    $pre1 = "【志愿帮扶】";
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
                $focus = RedleadModel::where('id',$value)->find();
                $title = $focus['title'];
                $str = strip_tags($focus['content']);
                $des = mb_substr($str,0,100);
                $content = str_replace("&nbsp;","",$des);  //空格符替换成空
                $url = "http://dqpb.0571ztnet.com/home/college/leaddetail/id/".$focus['id'].".html";
                switch ($focus['type']) {
                    case 1:
                        $pre = "【领衔攻坚】";
                        break;
                    case 2:
                        $pre = "【岗位师范】";
                        break;
                    case 3:
                        $pre = "【师徒结对】";
                        break;
                    case 4:
                        $pre = "【志愿帮扶】";
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
            "agentid" => 4,
            "news" => $send,
            "safe" => "0"
        );
        $msg = $Wechat->sendMessage($message);

        if ($msg['errcode'] == 0){
            $data['focus_vice'] ? $data['focus_vice'] = json_encode($data['focus_vice']) : $data['focus_vice'] = null;
            $data['create_user'] = session('user_auth.username');
            $data['status'] = 1;
            $data['class'] = 5;
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