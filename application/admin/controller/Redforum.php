<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/2/3
 * Time: 13:47
 */

namespace app\admin\controller;
use app\admin\model\Picture;
use app\admin\model\Push;
use app\admin\model\RedforumDetail;
use app\admin\model\RedforumNotice;
use app\admin\model\Redforum as RedforumModel;
use com\wechat\TPQYWechat;
use think\Config;

/**
 * 红领论坛
 * Class Redforum
 * @package app\admin\controller
 */
class Redforum extends Admin {
    /**
     * 通知主页
     */
    public function notice() {
        $map = array(
            'status' => 1
        );
        $list = $this->lists('RedforumNotice',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已发布",2=>"不通过"),
        ));
        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 通知新增
     */
    public function noticeadd() {
        if(IS_POST) {
            $data = input('post.');
            unset($data['id']);
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $data['time'] = strtotime($data['time']);
            $noticeModel = new RedforumNotice();
            $model = $noticeModel->validate('RedforumNotice')->save($data);
            if($model) {
                return $this->success("新增成功",Url('Redforum/notice'));
            }else{
                return $this->error($noticeModel->getError());
            }
        }else {
            $this->assign('msg',null);
            return $this->fetch('noticeedit');
        }
    }

    /**
     * 通知修改
     */
    public function noticeedit() {
        if(IS_POST) {
            $data = input('post.');
            $data['update_time'] = time();
            $data['update_user'] = $_SESSION['think']['user_auth']['id'];
            $data['time'] = strtotime($data['time']);
            $noticeModel = new RedforumNotice();
            $model = $noticeModel->validate('RedforumNotice')->save($data,['id'=>$data['id']]);
            if($model) {
                return $this->success("修改成功",Url('Redforum/notice'));
            }else{
                return $this->error($noticeModel->getError());
            }
        }else {
            $id = input('id');
            $msg = RedforumNotice::get($id);
            $this->assign('msg',$msg);
            return $this->fetch();
        }
    }

    /**
     * 通知删除
     */
    public function noticedel() {
        $id = input('id');
        $map = array(
            'status' => -1,
        );
        $noticeModel = new RedforumNotice();
        $model = $noticeModel->where('id',$id)->update($map);
        if($model) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }

    /**
     * 活动开展主题
     */
    public function forum() {
        $map = array(
            'status' => 1
        );
        $list = $this->lists('Redforum',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已发布",2=>"不通过"),
        ));
        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 主题新增
     */
    public function forumadd() {
        if(IS_POST) {
            $data = input('post.');
            unset($data['id']);
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $forumModel = new RedforumModel();
            $model = $forumModel->validate('Redforum')->save($data);
            if($model) {
                return $this->success("新增成功",Url('Redforum/forum'));
            }else{
                return $this->error($forumModel->getError());
            }
        }else {
            //获取通知
            $map = array(
                'status' => 1,
            );
            $notice = RedforumNotice::where($map)->order('create_time desc')->select();
            $this->assign('notice',$notice);

            $this->assign('msg',null);
            return $this->fetch('forumedit');
        }
    }

    /**
     * 主题修改
     */
    public function forumedit() {
        if(IS_POST) {
            $data = input('post.');
            $data['update_time'] = time();
            $data['update_user'] = $_SESSION['think']['user_auth']['id'];
            $forumModel = new RedforumModel();
            $model = $forumModel->validate('Redforum')->save($data,['id'=>$data['id']]);
            if($model) {
                return $this->success("修改成功",Url('Redforum/forum'));
            }else{
                return $this->error($forumModel->getError());
            }
        }else {
            //获取通知
            $map = array(
                'status' => 1,
            );
            $notice = RedforumNotice::where($map)->order('create_time desc')->select();
            $this->assign('notice',$notice);

            $id = input('id');
            $msg = RedforumModel::get($id);
            $this->assign('msg',$msg);
            return $this->fetch();
        }
    }

    /**
     * 主题删除
     */
    public function forumdel() {
        $id = input('id');
        $map = array(
            'status' => -1,
        );
        $noticeModel = new RedforumModel();
        $model = $noticeModel->where('id',$id)->update($map);
        if($model) {
            RedforumDetail::where('rid',$id)->update($map);
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }

    /**
     * 详情主页
     */
    public function detail() {
        $map = array(
            'status' => 1
        );
        $list = $this->lists('RedforumDetail',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已发布",2=>"不通过"),
        ));
        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 详情新增
     */
    public function detailadd() {
        if(IS_POST) {
            $data = input('post.');
            unset($data['id']);
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $detailModel = new RedforumDetail();
            $model = $detailModel->validate('RedforumDetail')->save($data);
            if($model) {
                return $this->success("新增成功",Url('Redforum/detail'));
            }else{
                return $this->error($detailModel->getError());
            }
        }else {
            //获取主题
            $map = array(
                'status' => 1,
            );
            $theme = RedforumModel::where($map)->order('create_time desc')->select();
            $this->assign('theme',$theme);

            $this->assign('msg',null);
            return $this->fetch('detailedit');
        }
    }

    /**
     * 详情修改
     */
    public function detailedit() {
        if(IS_POST) {
            $data = input('post.');
            $data['update_time'] = time();
            $data['update_user'] = $_SESSION['think']['user_auth']['id'];
            $detailModel = new RedforumDetail();
            $model = $detailModel->validate('RedforumDetail')->save($data,['id'=>$data['id']]);
            if($model) {
                return $this->success("修改成功",Url('Redforum/detail'));
            }else{
                return $this->error($detailModel->getError());
            }
        }else {
            //获取主题
            $map = array(
                'status' => 1,
            );
            $theme = RedforumModel::where($map)->order('create_time desc')->select();
            $this->assign('theme',$theme);

            $id = input('id');
            $msg = RedforumDetail::get($id);
            $this->assign('msg',$msg);
            return $this->fetch();
        }
    }

    /**
     * 详情删除
     */
    public function detaildel() {
        $id = input('id');
        $map = array(
            'status' => -1,
        );
        $noticeModel = new RedforumDetail();
        $model = $noticeModel->where('id',$id)->update($map);
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
            $infoes = RedforumNotice::where($info)->select();
            foreach ($infoes as $value) {
                $value['type_text'] = "论坛通知";
            }
            return $this->success($infoes);
        }else{
            //消息列表
            $map = array(
                'class' => 6,
                'status' => array('egt',-1),
            );
            $list = $this->lists('Push',$map);
            int_to_string($list,array(
                'status' => array(-1 => '不通过', 0 => '未审核', 1=> '已发送'),
            ));
            //数据重组
            foreach ($list as $value) {
                $msg = RedforumNotice::where('id',$value['focus_main'])->find();
                $value['title'] = $msg['title'];
            }
            $this->assign('list',$list);
            //主图文本周内的新闻消息
            $t = $this->week_time();    //获取本周一时间
            $info = array(
                'create_time' => array('egt',$t),
                'status' => 1,
            );
            $infoes = RedforumNotice::where($info)->select();
            foreach ($infoes as $value) {
                $value['type_text'] = "论坛通知";
            }
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
            $focus1 = RedforumNotice::where('id',$arr1)->find();
            $title1 = $focus1['title'];
            $str1 = strip_tags($focus1['content']);
            $des1 = mb_substr($str1,0,100);
            $content1 = str_replace("&nbsp;","",$des1);  //空格符替换成空
            $url1 = "http://dqpb.0571ztnet.com/home/college/forumnotice/id/".$focus1['id'].".html";
            $pre1 = "【论坛通知】";
            $path1 = "http://dqpb.0571ztnet.com/home/images/redforum.png";
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
                $focus = RedforumNotice::where('id',$value)->find();
                $title = $focus['title'];
                $str = strip_tags($focus['content']);
                $des = mb_substr($str,0,100);
                $content = str_replace("&nbsp;","",$des);  //空格符替换成空
                $url = "http://dqpb.0571ztnet.com/home/college/forumnotice/id/".$focus['id'].".html";
                $pre = "【论坛通知】";
                $path = "http://dqpb.0571ztnet.com/home/images/redforum.png";
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
        $Wechat = new TPQYWechat(Config::get('redcollege'));
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
            $data['class'] = 6;
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