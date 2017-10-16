<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/1/22
 * Time: 13:48
 */

namespace app\admin\controller;
use app\admin\model\Picture;
use app\admin\model\Push;
use app\admin\model\RedtutorCourse;
use app\admin\model\RedtutorNotice;
use app\admin\model\RedtutorTutor;
use com\wechat\TPQYWechat;
use think\Config;

/**
 * 红领导师。
 * Class Redtutor
 * @package app\admin\controller
 */
class Redtutor extends Admin {
    /**
     * 通知列表
     */
    public function notice() {
        $map = array(
            'status' => 1
        );
        $list = $this->lists('RedtutorNotice',$map);
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
            if (!empty($data['time'])) {
                $data['time'] = strtotime($data['time']);
            }
            $noticeModel = new RedtutorNotice();
            $model = $noticeModel->validate('RedtutorNotice')->save($data);
            if($model) {
                return $this->success("新增成功",Url('Redtutor/notice'));
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
            if (!empty($data['time'])) {
                $data['time'] = strtotime($data['time']);
            }
            $noticeModel = new RedtutorNotice();
            $model = $noticeModel->validate('RedtutorNotice')->save($data,['id'=>$data['id']]);
            if($model) {
                return $this->success("修改成功",Url('Redtutor/notice'));
            }else{
                return $this->error($noticeModel->getError());
            }
        }else {
            $id = input('id');
            $msg = RedtutorNotice::get($id);
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
        $noticeModel = new RedtutorNotice();
        $model = $noticeModel->where('id',$id)->update($map);
        if($model) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }

    /**
     * 导师列表
     */
    public function tutor() {
        $map = array(
            'status' => 1
        );
        $list = $this->lists('RedtutorTutor',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已发布",2=>"不通过"),
            'type' => array(1=>"党建理论",2=>"专业技术",3=>"政策法规",4=>"道德模范"),
        ));
        $this->assign('list',$list);
        
        return $this->fetch();
    }

    /**
     * 导师新增
     */
    public function tutoradd() {
        if(IS_POST) {
            $data = input('post.');
            unset($data['id']);
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $tutorModel = new RedtutorTutor();
            $model = $tutorModel->validate('RedtutorTutor')->save($data);
            if($model) {
                return $this->success("新增成功",Url('Redtutor/tutor'));
            }else{
                return $this->error($tutorModel->getError());
            }
        }else {
            $this->default_pic();
            $this->assign('msg',null);
            return $this->fetch('tutoredit');
        }
    }

    /**
     * 导师编辑
     */
    public function tutoredit() {
        if(IS_POST) {
            $data = input('post.');
            $data['update_time'] = time();
            $data['update_user'] = $_SESSION['think']['user_auth']['id'];
            $tutorModel = new RedtutorTutor();
            $model = $tutorModel->validate('RedtutorTutor')->save($data,['id'=>$data['id']]);
            if($model) {
                return $this->success("修改成功",Url('Redtutor/tutor'));
            }else{
                return $this->error($tutorModel->getError());
            }
        }else {
            $this->default_pic();
            $id = input('id');
            $msg = RedtutorTutor::get($id);
            $this->assign('msg',$msg);
            return $this->fetch();
        }
    }

    /**
     * 导师删除
     */
    public function tutordel() {
        $id = input('id');
        $map = array(
            'status' => -1,
        );
        $tutorModel = new RedtutorTutor();
        $model = $tutorModel->where('id',$id)->update($map);
        if($model) {
            RedtutorCourse::where('tid',$id)->update($map); //删除关联课程
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }

    /**
     * 课程列表
     */
    public function course() {
        $map = array(
            'status' => 1
        );
        $list = $this->lists('RedtutorCourse',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已发布",2=>"不通过"),
        ));
        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 课程新增
     */
    public function courseadd() {
        if(IS_POST) {
            $data = input('post.');
            unset($data['id']);
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $courseModel = new RedtutorCourse();
            $model = $courseModel->validate('RedtutorCourse')->save($data);
            if($model) {
                return $this->success("新增成功",Url('Redtutor/course'));
            }else{
                return $this->error($courseModel->getError());
            }
        }else {
            $this->default_pic();

            //获取导师列表
            $map = array('status' => 1,);
            $list = RedtutorTutor::where($map)->field('id,name')->select();
            $this->assign('tutor',$list);

            $this->assign('msg',null);
            return $this->fetch('courseedit');
        }
    }

    /**
     * 课程修改
     */
    public function courseedit() {
        if(IS_POST) {
            $data = input('post.');
            $data['update_time'] = time();
            $data['update_user'] = $_SESSION['think']['user_auth']['id'];
            $courseModel = new RedtutorCourse();
            $model = $courseModel->validate('RedtutorCourse')->save($data,['id'=>$data['id']]);
            if($model) {
                return $this->success("修改成功",Url('Redtutor/course'));
            }else{
                return $this->error($courseModel->getError());
            }
        }else {
            $this->default_pic();

            //获取导师列表
            $map = array('status' => 1,);
            $list = RedtutorTutor::where($map)->field('id,name')->select();
            $this->assign('tutor',$list);

            $id = input('id');
            $msg = RedtutorCourse::get($id);
            $this->assign('msg',$msg);

            return $this->fetch();
        }
    }

    /**
     * 课程删除
     */
    public function coursedel() {
        $id = input('id');
        $map = array(
            'status' => -1,
        );
        $courseModel = new RedtutorCourse();
        $model = $courseModel->where('id',$id)->update($map);
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
//            $t = $this->week_time();
            $info = array(
                'id' => array('neq',$id),
//                'create_time' => array('egt',$t),
                'status' => 1,
            );
            $infoes = RedtutorCourse::where($info)->select();
            foreach ($infoes as $value) {
                $value['type_text'] = $value->tutor->name;  //获取导师名字
            }
            return $this->success($infoes);
        }else{
            //消息列表
            $map = array(
                'class' => 2,
                'status' => array('egt',-1),
            );
            $list = $this->lists('Push',$map);
            int_to_string($list,array(
                'status' => array(-1 => '不通过', 0 => '未审核', 1=> '已发送'),
            ));
            //数据重组
            foreach ($list as $value) {
                $msg = RedtutorCourse::where('id',$value['focus_main'])->find();
                $value['title'] = $msg['title'];
            }
            $this->assign('list',$list);
            //主图文本周内的新闻消息
//            $t = $this->week_time();    //获取本周一时间
            $info = array(
//                'create_time' => array('egt',$t),
                'status' => 1,
            );
            $infoes = RedtutorCourse::where($info)->select();
            foreach ($infoes as $value) {
                $value['type_text'] = $value->tutor->name;
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
        $httpUrl = config('http_url');
        $arr1 = $data['focus_main'];    //主图文id
        isset($data['focus_vice']) ? $arr2 = $data['focus_vice'] : $arr2 = "";    //副图文id
        if($arr1 == -1){
            return $this->error("请选择主图文!");
        }else{
            //主图文信息
            $focus1 = RedtutorCourse::where('id',$arr1)->find();
            $title1 = $focus1['title'];
            $str1 = strip_tags($focus1['content']);
            $des1 = mb_substr($str1,0,100);
            $content1 = str_replace("&nbsp;","",$des1);  //空格符替换成空
            $url1 = $httpUrl."/home/college/tutordetail/id/".$focus1['id'].".html";
            $pre1 = "【".$focus1->tutor->name."】";
            $img1 = Picture::get($focus1['list_image']);
            $path1 = $httpUrl.$img1['path'];
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
                $focus = RedtutorCourse::where('id',$value)->find();
                $title = $focus['title'];
                $str = strip_tags($focus['content']);
                $des = mb_substr($str,0,100);
                $content = str_replace("&nbsp;","",$des);  //空格符替换成空
                $url = $httpUrl."/home/college/tutordetail/id/".$focus['id'].".html";
                $pre = "【".$focus->tutor->name."】";
                $img = Picture::get($focus['list_image']);
                $path = $httpUrl.$img['path'];
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
        $Wechat = new TPQYWechat(Config::get('Learn'));
        $touser = config('touser');
        $newsConf = config('Learn');
        $message = array(
//            'totag' => "18", //审核标签用户
            "touser" => "$touser",
//            "touser" => "@all",   //发送给全体，@all
            "msgtype" => 'news',
            "agentid" => $newsConf['agentid'],
            "news" => $send,
            "safe" => "0"
        );
        $msg = $Wechat->sendMessage($message);

        if ($msg['errcode'] == 0){
            $data['focus_vice'] ? $data['focus_vice'] = json_encode($data['focus_vice']) : $data['focus_vice'] = null;
            $data['create_user'] = session('user_auth.username');
            $data['status'] = 1;
            $data['class'] = 2;
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