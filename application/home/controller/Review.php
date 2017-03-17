<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/11/3
 * Time: 11:27
 */

namespace app\home\controller;
use app\admin\model\NoticeSend;
use app\admin\model\Push;
use app\admin\model\PushReview;
use app\home\model\News;
use app\home\model\Notice;
use app\home\model\Picture;
use app\home\model\VolunteerRecruit;
use app\home\model\VolunteerRecruitReview;
use app\home\model\WechatUser;
use app\home\model\Years;
use com\wechat\TPQYWechat;
use think\Config;

/**
 * Class Review
 * 审核列表
 */
class Review extends Base {
    /**
     * 新闻 待审核列表
     */
    public function reviewlist() {
        $map = array(
            'class' => 1, 
            'status' => array('eq',0),
        );
        $list = Push::where($map)->order('create_time desc')->select();
        foreach ($list as $value) {
            $News = new News();
            $news = $News->where('id',$value['focus_main'])->find();
            $value['front_cover'] = $news['front_cover'];
            $value['title'] = $news['title'];
            $value['publisher'] = $news['publisher'];
            $value['type_name'] = "第一聚焦";
        }
        $this->assign('list',$list);
        return $this->fetch();
    }
    /*
     * 审核 详细页
     */
    public function detail(){
        $id = input('param.id');
        $map = array(
            'id'  => $id,
        );
        $list = News::where($map)->find();
        $this->assign('list',$list);
        return $this->fetch();
    }
    /*
     * 新闻审核 推送
     */
    public function review(){
        $userId = session('userId');
        $user = WechatUser::where('userid', $userId)->find();
        $username = $user['name'];
        $msg = input('post.');
        $push = Push::where('id',$msg['id'])->find();
        //新建pushreview数据
        $data1 = array(
            'push_id' => $msg['id'],
            'user_id' => $userId,
            'username' => $username,
            'status' => $msg['status'],
        );
        $this_info['status'] = $msg['status'];   //更新push表状态
        if($msg['status'] == 1 ){  // 审核通过
            $new = PushReview::create($data1);
            if($new){
                // 发送消息
                $arr1 = $push['focus_main'];    //主图文id
                !empty($push['focus_vice']) ? $arr2 = json_decode($push['focus_vice']) : $arr2 = "";    //副图文id
                //主图文信息
                $focus1 = News::where('id', $arr1)->find();
                $pre1 = "【第一聚焦】";
                $title1 = $focus1['title'];
                $str1 = strip_tags($focus1['content']);
                $des1 = mb_substr($str1, 0, 40);
                $content1 = str_replace("&nbsp;", "", $des1);  //空格符替换成空
                $url1 = "http://dqpb.0571ztnet.com/home/news/detail/id/" . $focus1['id'] . ".html";
                $img1 = Picture::get($focus1['front_cover']);
                $path1 = "http://dqpb.0571ztnet.com" . $img1['path'];
                $information1 = array(
                    "title" => $pre1 . $title1,
                    "description" => $content1,
                    "url" => $url1,
                    "picurl" => $path1,
                );
                $information = array();
                if (!empty($arr2)) {
                    //副图文信息
                    $information2 = array();
                    foreach ($arr2 as $key => $value) {
                        $focus = News::where('id', $value)->find();
                        $pre = "【第一聚焦】";
                        $title = $focus['title'];
                        $str = strip_tags($focus['content']);
                        $des = mb_substr($str, 0, 40);
                        $content = str_replace("&nbsp;", "", $des);  //空格符替换成空
                        $url = "http://dqpb.0571ztnet.com/home/news/detail/id/" . $focus['id'] . ".html";
                        $img = Picture::get($focus['front_cover']);
                        $path = "http://dqpb.0571ztnet.com" . $img['path'];
                        $info = array(
                            "title" => $pre. $title,
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
                //发送给服务号
                $Wechat = new TPQYWechat(Config::get('party'));
                $message = array(
//                   'totag' => "4", //审核标签用户
                'touser' =>'15036667391',
//                   "touser" => "@all",   //发送给全体，@all
                    "msgtype" => 'news',
                    "agentid" =>15,  // 小镇动态
                    "news" => $send,
                    "safe" => "0"
                );
                $suc = $Wechat->sendMessage($message);
                if ($suc['errcode'] == 0) {
                    Push::where('id',$msg['id'])->update($this_info);    //改变推送状态
                    return $this->success("审核通过，已推送消息");
                };
            }
        }else{  //不通过
            $new = PushReview::create($data1);   //记录
            if ($new) {
                Push::where('id',$msg['id'])->update($this_info);
                News::where(array('id'=>$push['focus_main']))->update(array('status'=>3));  // 改变状态
                if($push['focus_vice']){
                    foreach(json_decode($push['focus_vice']) as $value){
                        News::where(array('id'=>$value))->update(array('status'=>3));  // 改变状态
                    }
                }
                return $this->error("审核不通过");
            }
        }
    }
    /*
     * 新闻 已审核列表
     */
    public function passlist(){
        $map = array(
            'status' => array('in',[1,-1]),
        );
        $list = Push::where($map)->order('create_time desc')->select();
        foreach ($list as $value) {
                //新闻推送
                $focus = News::where('id',$value['focus_main'])->find();  //图文信息
                $value['title'] = $focus['title'];
                $value['image'] = $focus['front_cover'];
                $value['class_name'] = "[第一聚焦]";
            $push = PushReview::where('push_id',$value['id'])->find();
            $value['review_time'] = $push['review_time'];
            $value['username'] = $push['username'];
        }
        $this->assign('list',$list);
        return $this->fetch();
    }
    /**
     * 文章 审核
     */
    public function reviews(){
        $id = input('id');
        $map['status'] = input('status');
        $userid = input('userid');
        $model = Notice::where('id',$id)->update($map);
        $msg = Notice::get($id);
        if($model) {
            if($map['status'] == 1) {
                $content = "恭喜您提交的文章【".$msg['title']."】已成功通过审核！";
                $Wechat = new TPQYWechat(Config::get('party'));
                $message = array(
                    'touser' => $userid,
                    "msgtype" => 'text',
                    "agentid" => 8, // 个人中心
                    "text" => array(
                        "content" => $content
                    ),
                    "safe" => "0"
                );
                $suc = $Wechat->sendMessage($message);  //审核通过，向用户推送提示

                //部门id
                if($msg['departmentid']){
                    $dp = json_decode($msg['departmentid']);
                    foreach($dp as $value){
                        $con = array(
                            'notice_id' => $id,
                            'departmentid' => $value
                        );
                        NoticeSend::create($con);
                    }
                }
            }else{
                $content = "很抱歉，您提交的文章【".$msg['title']."】未能通过审核！";
                $Wechat = new TPQYWechat(Config::get('party'));
                $message = array(
                    'touser' => $userid,
                    "msgtype" => 'text',
                    "agentid" => 8,  // 个人中心
                    "text" => array(
                        "content" => $content
                    ),
                    "safe" => "0"
                );
                $suc = $Wechat->sendMessage($message);  //审核不通过推送
            }
            //推送成功后数据显示在个人中心我的消息列表中
            if($suc) {
                $MsgModel = new Years();
                $name = WechatUser::where('userid',$userid)->find();
                $arr = array(
                    'userid' => $userid,
                    'name' => $name['name'],
                    'type' => 1,
                    'years' => $content,
                );
                $info = $MsgModel->create($arr);
                if($info) {
                    return $this->success("审核完成");
                }else{
                    return $this->error("创建失败");
                }
            }
        }else{
            return $this->error("审核失败");
        }
    }

    /**
     * 文章  待审核列表 (前端上传)
     */
    public function reviewlists(){
        $map = array(
            'status' => array('eq',0),

        );
        $list = Notice::where($map)->order('create_time desc')->select();
        if($list){
            foreach ($list as $value) {
                switch ($value['type']) {   //获取类型名称
                    case 1:
                        $value['type_name'] = "相关通知";
                        break;
                    case 2:
                        $value['type_name'] = "会议情况";
                        break;
                    case 3:
                        $value['type_name'] = "党课情况";
                        break;
                    case 4:
                        $value['type_name'] = "活动招募";
                        break;
                    case 5:
                        $value['type_name'] = "活动情况";
                        break;
                    default:
                        break;
                }
                $u = WechatUser::where('userid',$value['userid'])->find();  //获取用户名
                $value['username'] = $u['name'];
            }
            $this->assign('list',$list);
        }else{
            $this->assign('list',"");
        }
        return $this->fetch();
    }
    
    /**
     * 志愿招募审核列表
     */
    public function recruitlist() {
        $map = array(
            'status' => 0
        );
        $recruitModel = new VolunteerRecruit();
        $list = $recruitModel->where($map)->order('create_time desc')->select();
        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 志愿审核
     */
    public function recruitreview() {
        $id = input('id');
        $map['status'] = input('status');
        $model = VolunteerRecruit::where('id',$id)->update($map);
        $msg = VolunteerRecruit::get($id);
        $userid = $msg['userid'];
        if($model) {
            if($map['status'] == 1) {
                $content = "恭喜您提交的志愿招募【".$msg['title']."】已成功通过审核！";
            }else{
                $content = "很抱歉，您提交的志愿招募【".$msg['title']."】未能通过审核！";
            }
            $Wechat = new TPQYWechat(Config::get('party'));
            $message = array(
                'touser' => $userid,
                "msgtype" => 'text',
                "agentid" => 8,  // 个人中心
                "text" => array(
                    "content" => $content
                ),
                "safe" => "0"
            );

            $suc = $Wechat->sendMessage($message);  //审核不通过推送
            if($suc) {
                $arr = array(
                    'rid' => $id,
                    'userid' => session('userId'),
                );
                VolunteerRecruitReview::create($arr);
                return $this->success("审核完成");
            }else {
                return $this->error("审核失败");
            }
        }else{
            return $this->error("审核失败");
        }
        
    }
}