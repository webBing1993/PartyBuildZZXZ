<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/9/12
 * Time: 16:12
 */

namespace app\home\controller;
use app\admin\model\NoticeSend;
use app\home\model\Browse;
use app\home\model\Collect;
use app\home\model\Comment;
use app\home\model\Like;
use app\home\model\Picture;
use app\home\model\WechatDepartmentUser;
use app\home\model\WechatUser;
use app\home\model\WechatUserTag;
use com\wechat\TPQYWechat;
use think\Config;
use think\Controller;

use app\home\model\Notice as NoticeModel;
use think\Db;

/**
 * Class Notice
 * @package 通知公告/支部活动
 */
class Notice extends Base {
    /**
     * 主页
     */
    public function index(){
        $this->anonymous(); //判断是否是游客
        $this->default_pic();
        
        $userId = session('userId');
        //相关通知 type = 1
        $map1 = array(
            'type' => 1,
            'status' => array('eq',1)
        );
        $list1 = NoticeModel::where($map1)->order('id desc')->limit(2)->select();
        $this->assign('relevant',$list1);

        //会议情况 type = 2
        $map2 = array(
            'type' => 2,
            'status' => array('eq',1)
        );
        $list2 = NoticeModel::where($map2)->order('id desc')->limit(2)->select();
        $this->assign('meet',$list2);

        //党课情况 type = 3
        $map3 = array(
            'type' => 3,
            'status' => array('eq',1)
        );
        $list3 = NoticeModel::where($map3)->order('id desc')->limit(5)->select();
        $this->assign('party',$list3);

        //活动招募 type = 4
        $map4 = array(
            'type' => 4,
            'status' => array('eq',1)
        );
        $list4 = NoticeModel::where($map4)->order('id desc')->limit(2)->select();
        $this->assign('recruit',$list4);

        //活动情况 type = 5
        $map5 = array(
            'type' => 5,
            'status' => array('eq',1)
        );
        $list5 = NoticeModel::where($map5)->order('id desc')->limit(5)->select();
        $this->assign('activity',$list5);

        //创意组织生活 type = 6
        $map6 = array(
            'type' => 6,
            'status' => array('eq',1)
        );
        $list6 = NoticeModel::where($map6)->order('id desc')->limit(2)->select();
        $this->assign('regular',$list6);

        //是否具备我的发布权限,具备为1，无则为0
        $map = array(
            'userid' => $userId,
            'tagid' => 0, //权限标签id
        );
        $info = WechatUserTag::where($map)->find();
        if($info) {
            $this->assign('is',1);
        }else{
            $this->assign('is',0);
        }
        return $this->fetch();
    }

    /**
     * 相关通知
     */
    public function relevant(){
        //判断是否是游客
        $this->anonymous();

        $this->jssdk();

        $userId = session('userId');
        $id = input('id');
        $noticeModel = new NoticeModel();

        //浏览加一
        $info['views'] = array('exp','`views`+1');
        $noticeModel::where('id',$id)->update($info);
        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = array(
                'user_id' => $userId,
                'notice_id' => $id,
            );
            $history = Browse::get($con);
            if(!$history && $id != 0){
                Browse::create($con);
                $s['score'] = array('exp','`score`+1');
                WechatUser::where('userid',$userId)->update($s);
            }
        }

        //活动基本信息
        $list = $noticeModel::get($id);
        //重组轮播图片
        $list['carousel'] = json_decode($list['carousel_images']);
        $list['user'] = session('userId');
        //分享图片及链接及描述
        $image = Picture::where('id',$list['front_cover'])->find();
        $list['share_image'] = "http://".$_SERVER['SERVER_NAME'].$image['path'];
        $list['link'] = $_SERVER['REDIRECT_URL'];
        $list['desc'] = str_replace('&nbsp;','',strip_tags($list['content']));

        //文章点赞是否存在
        $map2 = array(
            'notice_id' => $id,
            'create_user' => $userId,
            'status' => 0,
            'type' => 1,
        );
        $msg = Like::where($map2)->find();
        if($msg) {
            $list['is_like'] = 1;
        }else{
            $list['is_like'] = 0;
        }

        //收藏是否存在
        unset($map2['type']);
        $col = Collect::where($map2)->find();
        if($col) {
            $list['is_collect'] = 1;
        }else {
            $list['is_collect'] = 0;
        }
        $this->assign('list',$list);

        //评论
        $map = array(
            'notice_id' => $id,
            'status' => 0
        );
        //敏感词屏蔽
        $badword = array(
            '法轮功','法轮','FLG','六四','6.4','flg'
        );
        $badword1 = array_combine($badword,array_fill(0,count($badword),'***'));
        $comment = Comment::where($map)->limit(0,5)->order('likes desc,id desc')->select();
        foreach($comment as $value){
            $content = $value['content'];
            $str = strtr($content, $badword1);
            $value['content'] = $str;
            if($value['create_user'] == $userId){
                $value['self'] = 1;
            }else{
                $value['self'] = 0;
            }
            $map1 = array(
                'create_user' => $userId,
                'comment_id' => $value['id'],
                'status' => array('egt',0),
                'type' => 2, //评论点赞
            );

            $like = Like::where($map1)->find();
            ($like)?$value['is_like'] = 1:$value['is_like'] = 0;
            $users = WechatUser::where('userid',$value['create_user'])->find();
            $value['header'] = ($users['header']) ? $users['header'] : $users['avatar'];
            $value['nickname'] = ($users['nickname']) ? $users['nickname'] : $users['name'];
        }
        $this->assign('comment',$comment);

        return $this->fetch();
    }

    /**
     * 相关通知列表
     */
    public function relevantlist(){

        $map = array(
            'status' => array('eq',1),
            'type' => 1,
        );
        $noticeModel = new NoticeModel();
        $list = $noticeModel::where($map)->order('id desc')->limit(7)->select();
        //判断是否为空
        if (empty($list)){
            $this->assign('show',0);
        }else{
            $this->assign('show',1);
        }
        foreach ($list as $value) {
            if($value['end_time'] < time()) {
                $value['is'] = 1;
            }else{
                $value['is'] = 0;
            }
        }
        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 更多通知
     */
    public function relevantmore(){
        $len = input('length');
        $map = array(
            'type' => 1,
            'status' => array('eq',1),
        );
        $list = NoticeModel::where($map)->order('id desc')->limit($len,7)->select();
        foreach($list as $value){
            $value['time'] = date("Y-m-d",$value['create_time']);
            if($value['end_time'] < time()) {
                $value['state'] = 1;
            }else{
                $value['state'] = 0;
            }
        }
        if($list){
            return $this->success("加载成功",'',$list);
        }else{
            return $this->error("加载失败");
        }

    }

    /**
     * 会议情况
     */
    public function meet(){
        //判断是否是游客
        $this->anonymous();

        $this->jssdk();

        $userId = session('userId');
        $id = input('id');
        $noticeModel = new NoticeModel();
        //浏览加一
        $info['views'] = array('exp','`views`+1');
        $noticeModel::where('id',$id)->update($info);
        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = array(
                'user_id' => $userId,
                'notice_id' => $id,
            );
            $history = Browse::get($con);
            if(!$history && $id != 0){
                Browse::create($con);
                $s['score'] = array('exp','`score`+1');
                WechatUser::where('userid',$userId)->update($s);
            }
        }

        $meet = $noticeModel->get($id);
        $meet['user'] = session('userId');
        //分享图片及链接及描述
        $image = Picture::where('id',$meet['front_cover'])->find();
        $meet['share_image'] = "http://".$_SERVER['SERVER_NAME'].$image['path'];
        $meet['link'] = $_SERVER['REDIRECT_URL'];
        $meet['desc'] = str_replace('&nbsp;','',strip_tags($meet['content']));

        //文章点赞是否存在
        $map2 = array(
            'notice_id' => $id,
            'create_user' => $userId,
            'status' => 0,
            'type' => 1,
        );
        $msg = Like::where($map2)->find();
        if($msg) {
            $meet['is_like'] = 1;
        }else{
            $meet['is_like'] = 0;
        }

        //收藏是否存在
        unset($map2['type']);
        $col = Collect::where($map2)->find();
        if($col) {
            $meet['is_collect'] = 1;
        }else {
            $meet['is_collect'] = 0;
        }
        $meet['images'] = json_decode($meet['images']);
        $this->assign('meet',$meet);

        //评论
        $map = array(
            'notice_id' => $id,
            'status' => 0
        );
        //敏感词屏蔽
        $badword = array(
            '法轮功','法轮','FLG','六四','6.4','flg'
        );
        $badword1 = array_combine($badword,array_fill(0,count($badword),'***'));
        $comment = Comment::where($map)->limit(0,5)->order('likes desc,id desc')->select();
        foreach($comment as $value){
            $content = $value['content'];
            $str = strtr($content, $badword1);
            $value['content'] = $str;
            if($value['create_user'] == $userId){
                $value['self'] = 1;
            }else{
                $value['self'] = 0;
            }
            $map1 = array(
                'create_user' => $userId,
                'comment_id' => $value['id'],
                'status' => array('egt',0),
                'type' => 2, //评论点赞
            );

            $like = Like::where($map1)->find();
            ($like)?$value['is_like'] = 1:$value['is_like'] = 0;
            $users = WechatUser::where('userid',$value['create_user'])->find();
            $value['header'] = ($users['header']) ? $users['header'] : $users['avatar'];
            $value['nickname'] = ($users['nickname']) ? $users['nickname'] : $users['name'];
        }
        $this->assign('comment',$comment);

        return $this->fetch();
    }

    /**
     * 会议情况列表页面
     */
    public function meetlist(){
        $userId = session('userId');
        //会议情况 type = 2
        $dept = Db::table('pb_notice')
            ->alias('a')
            ->join('pb_notice_send b','a.id = b.notice_id','LEFT')
            ->join('pb_wechat_department_user c','b.departmentid = c.departmentid','LEFT')
            ->field('a.id,a.title,a.front_cover,a.create_time')
            ->where('c.userid',$userId)
            ->where('a.status','eq',1)
            ->where('a.type','eq',2)
            ->order('a.id desc')
            ->limit(7)
            ->select();
        //获取标签关联活动
        $tag = Db::table('pb_notice')
            ->alias('a')
            ->join('pb_notice_send b','a.id = b.notice_id','LEFT')
            ->join('pb_wechat_user_tag c','b.tagid = c.tagid','LEFT')
            ->field('a.id,a.title,a.front_cover,a.create_time')
            ->where('c.userid',$userId)
            ->where('a.status','eq',1)
            ->where('a.type','eq',2)
            ->order('a.id desc')
            ->limit(7)
            ->select();
        $file = array_merge($dept,$tag); //合并需要合并的俩个数组
        if(!empty($file)){
            $key = 'id';//去重条件
            $tmp_arr = array();//声明数组
            foreach($file as $k => $v) {
                if(in_array($v[$key], $tmp_arr)) {
                    //搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true
                    unset($file[$k]); //删除掉数组（$arr）里相同ID的数组
                }else {
                    $tmp_arr[] = $v[$key]; //记录已有的id
                }
            }

            $list = array();
            foreach ($file as $key => $value){
                if($key < 7){
                    $list[] = $value;
                }
            }
            rsort($list); // 排序
            $this->assign('show',1);
        }else{
            $list = "";
            $this->assign('show',0);
        }
        $this->assign('meet',$list);
        return $this->fetch();
    }

    /**
     * 会议更多
     */
    public function meetmore(){
        $len = input('length');
        $userId = session('userId');
        //会议情况 type = 2
        $dept = Db::table('pb_notice')
            ->alias('a')
            ->join('pb_notice_send b','a.id = b.notice_id','LEFT')
            ->join('pb_wechat_department_user c','b.departmentid = c.departmentid','LEFT')
            ->field('a.id,a.title,a.front_cover,a.create_time')
            ->where('c.userid',$userId)
            ->where('a.status','eq',1)
            ->where('a.type','eq',2)
            ->order('a.id desc')
            ->select();
        //获取标签关联活动
        $tag = Db::table('pb_notice')
            ->alias('a')
            ->join('pb_notice_send b','a.id = b.notice_id','LEFT')
            ->join('pb_wechat_user_tag c','b.tagid = c.tagid','LEFT')
            ->field('a.id,a.title,a.front_cover,a.create_time')
            ->where('c.userid',$userId)
            ->where('a.status','eq',1)
            ->where('a.type','eq',2)
            ->order('a.id desc')
            ->select();
        $file = array_merge($dept,$tag); //合并需要合并的俩个数组
        $key = 'id';//去重条件
        $tmp_arr = array();//声明数组
        foreach($file as $k => $v) {
            if(in_array($v[$key], $tmp_arr)) {
                //搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true
                unset($file[$k]); //删除掉数组（$arr）里相同ID的数组
            }else {
                $tmp_arr[] = $v[$key]; //记录已有的id
            }
        }

        $list = array();
        foreach ($file as $key => $value){
            $value['time'] = date("Y-m-d",$value['create_time']);
            $img = Picture::get($value['front_cover']);
            $value['path'] = $img['path'];
            if($key >= $len && $key < $len+7){
                $list[] = $value;
            }
        }
        rsort($list); // 排序
        if($list){
            return $this->success("加载成功","",$list);
        }else{
            return $this->error("加载失败");
        }
    }

    /**
     * 党课情况
     */
    public function party(){

        //判断是否是游客
        $this->anonymous();

        $this->jssdk();

        $userId = session('userId');
        $id = input('id');
        $noticeModel = new NoticeModel();
        //浏览加一
        $info['views'] = array('exp','`views`+1');
        $noticeModel::where('id',$id)->update($info);
        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = array(
                'user_id' => $userId,
                'notice_id' => $id,
            );
            $history = Browse::get($con);
            if(!$history && $id != 0){
                Browse::create($con);
                $s['score'] = array('exp','`score`+1');
                WechatUser::where('userid',$userId)->update($s);
            }
        }

        $party = $noticeModel->get($id);
        $party['user'] = session('userId');
        //分享图片及链接及描述
        $image = Picture::where('id',$party['front_cover'])->find();
        $party['share_image'] = "http://".$_SERVER['SERVER_NAME'].$image['path'];
        $party['link'] = $_SERVER['REDIRECT_URL'];
        $party['desc'] = str_replace('&nbsp;','',strip_tags($party['content']));

        //文章点赞是否存在
        $map2 = array(
            'notice_id' => $id,
            'create_user' => $userId,
            'status' => 0,
            'type' => 1,
        );
        $msg = Like::where($map2)->find();
        if($msg) {
            $party['is_like'] = 1;
        }else{
            $party['is_like'] = 0;
        }

        //收藏是否存在
        unset($map2['type']);
        $col = Collect::where($map2)->find();
        if($col) {
            $party['is_collect'] = 1;
        }else {
            $party['is_collect'] = 0;
        }
        $party['images'] = json_decode($party['images']);
        $this->assign('party',$party);

        //评论
        $map = array(
            'notice_id' => $id,
            'status' => 0
        );
        //敏感词屏蔽
        $badword = array(
            '法轮功','法轮','FLG','六四','6.4','flg'
        );
        $badword1 = array_combine($badword,array_fill(0,count($badword),'***'));
        $comment = Comment::where($map)->limit(0,5)->order('likes desc,id desc')->select();
        foreach($comment as $value){
            $content = $value['content'];
            $str = strtr($content, $badword1);
            $value['content'] = $str;
            if($value['create_user'] == $userId){
                $value['self'] = 1;
            }else{
                $value['self'] = 0;
            }
            $map1 = array(
                'create_user' => $userId,
                'comment_id' => $value['id'],
                'status' => array('egt',0),
                'type' => 2, //评论点赞
            );

            $like = Like::where($map1)->find();
            ($like)?$value['is_like'] = 1:$value['is_like'] = 0;
            $users = WechatUser::where('userid',$value['create_user'])->find();
            $value['header'] = ($users['header']) ? $users['header'] : $users['avatar'];
            $value['nickname'] = ($users['nickname']) ? $users['nickname'] : $users['name'];
        }
        $this->assign('comment',$comment);

        return $this->fetch();
    }

    /**
     * 党课情况列表页面
     */
    public function partylist(){

        $map = array(
            'status' => array('eq',1),
            'type' => 3,
        );
        $list = NoticeModel::where($map)->order('id desc')->limit(7)->select();
        //判断是否为空
        if (empty($list)){
            $this->assign('show',0);
        }else{
            $this->assign('show',1);
        }
        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 党课加载更多
     */
    public function partymore(){
        $len = input("length");
        $map = array(
            'type' => 3,
            'status' => array('eq',1)
        );
        $list = NoticeModel::where($map)->order('id desc')->limit($len,7)->select();
        foreach($list as $value){
            $img = Picture::get($value['front_cover']);
            $value['path'] = $img['path'];
            $value['time'] = date("Y-m-d",$value['create_time']);
        }
        if($list){
            return $this->success("加载成功","",$list);
        }else{
            return $this->error("加载失败");
        }

    }

    /**
     * 活动通知
     */
    public function recruit(){

        //判断是否是游客
        $this->anonymous();

        $this->jssdk();

        $userId = session('userId');
        $id = input('id');
        $noticeModel = new NoticeModel();
        //浏览加一
        $info['views'] = array('exp','`views`+1');
        $noticeModel::where('id',$id)->update($info);
        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = array(
                'user_id' => $userId,
                'notice_id' => $id,
            );
            $history = Browse::get($con);
            if(!$history && $id != 0){
                Browse::create($con);
                $s['score'] = array('exp','`score`+1');
                WechatUser::where('userid',$userId)->update($s);
            }
        }

        //活动基本信息
        $list = $noticeModel::get($id);
        //重组轮播图片
        $list['carousel'] = json_decode($list['carousel_images']);
        $list['user'] = session('userId');
        //分享图片及链接及描述
        $image = Picture::where('id',$list['front_cover'])->find();
        $list['share_image'] = "http://".$_SERVER['SERVER_NAME'].$image['path'];
        $list['link'] = $_SERVER['REDIRECT_URL'];
        $list['desc'] = str_replace('&nbsp;','',strip_tags($list['content']));

        //文章点赞是否存在
        $map2 = array(
            'notice_id' => $id,
            'create_user' => $userId,
            'status' => 0,
            'type' => 1,
        );
        $msg = Like::where($map2)->find();
        if($msg) {
            $list['is_like'] = 1;
        }else{
            $list['is_like'] = 0;
        }

        //收藏是否存在
        unset($map2['type']);
        $col = Collect::where($map2)->find();
        if($col) {
            $list['is_collect'] = 1;
        }else {
            $list['is_collect'] = 0;
        }
        $this->assign('list',$list);

        //评论
        $map = array(
            'notice_id' => $id,
            'status' => 0
        );
        //敏感词屏蔽
        $badword = array(
            '法轮功','法轮','FLG','六四','6.4','flg'
        );
        $badword1 = array_combine($badword,array_fill(0,count($badword),'***'));
        $comment = Comment::where($map)->limit(0,5)->order('likes desc,id desc')->select();
        foreach($comment as $value){
            $content = $value['content'];
            $str = strtr($content, $badword1);
            $value['content'] = $str;
            if($value['create_user'] == $userId){
                $value['self'] = 1;
            }else{
                $value['self'] = 0;
            }
            $map1 = array(
                'create_user' => $userId,
                'comment_id' => $value['id'],
                'status' => array('egt',0),
                'type' => 2, //评论点赞
            );

            $like = Like::where($map1)->find();
            ($like)?$value['is_like'] = 1:$value['is_like'] = 0;
            $users = WechatUser::where('userid',$value['create_user'])->find();
            $value['header'] = ($users['header']) ? $users['header'] : $users['avatar'];
            $value['nickname'] = ($users['nickname']) ? $users['nickname'] : $users['name'];
        }
        $this->assign('comment',$comment);
        
        return $this->fetch();
    }

    /**
     * 活动通知列表
     */
    public function recruitlist(){
        $map = array(
            'status' => array('eq',1),
            'type' => 4,
        );
        $noticeModel = new NoticeModel();
        $list = $noticeModel::where($map)->order('id desc')->limit(7)->select();
        //判断是否为空
        if (empty($list)){
            $this->assign('show',0);
        }else{
            $this->assign('show',1);
        }
        foreach ($list as $value) {
            if($value['end_time'] < time()) {
                $value['is'] = 1;
            }else{
                $value['is'] = 0;
            }
        }
        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 活动通知  更多
     */
    public function recruitmore(){
        $len = input('length');
        $map = array(
            'type' => 4,
            'status' => array('eq',1),
        );
        $list = NoticeModel::where($map)->order('id desc')->limit($len,7)->select();
        foreach($list as $value){
            $value['time'] = date("Y-m-d",$value['create_time']);
            if($value['end_time'] < time()) {
                $value['state'] = 1; //结束
            }else{
                $value['state'] = 0; //进行
            }
        }
        if($list){
            return $this->success("加载成功",'',$list);
        }else{
            return $this->error("加载失败");
        }
    }

    /**
     * 活动情况
     */
    public function activity(){
        //判断是否是游客
        $this->anonymous();

        $this->jssdk();

        $userId = session('userId');
        $id = input('id');
        $noticeModel = new NoticeModel();
        //浏览加一
        $info['views'] = array('exp','`views`+1');
        $noticeModel::where('id',$id)->update($info);
        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = array(
                'user_id' => $userId,
                'notice_id' => $id,
            );
            $history = Browse::get($con);
            if(!$history && $id != 0){
                Browse::create($con);
                $s['score'] = array('exp','`score`+1');
                WechatUser::where('userid',$userId)->update($s);
            }
        }

        $activity = $noticeModel->get($id);
        //重组轮播图片
        $activity['user'] = session('userId');
        //分享图片及链接及描述
        $image = Picture::where('id',$activity['front_cover'])->find();
        $activity['share_image'] = "http://".$_SERVER['SERVER_NAME'].$image['path'];
        $activity['link'] = $_SERVER['REDIRECT_URL'];
        $activity['desc'] = str_replace('&nbsp;','',strip_tags($activity['content']));

        //文章点赞是否存在
        $map2 = array(
            'notice_id' => $id,
            'create_user' => $userId,
            'status' => 0,
            'type' => 1,
        );
        $msg = Like::where($map2)->find();
        if($msg) {
            $activity['is_like'] = 1;
        }else{
            $activity['is_like'] = 0;
        }
        
        //收藏是否存在
        unset($map2['type']);
        $col = Collect::where($map2)->find();
        if($col) {
            $activity['is_collect'] = 1;
        }else {
            $activity['is_collect'] = 0;
        }
        $activity['images'] = json_decode($activity['images']);
        $this->assign('activity',$activity);

        //评论
        $map = array(
            'notice_id' => $id,
            'status' => 0
        );
        //敏感词屏蔽
        $badword = array(
            '法轮功','法轮','FLG','六四','6.4','flg'
        );
        $badword1 = array_combine($badword,array_fill(0,count($badword),'***'));
        $comment = Comment::where($map)->limit(0,5)->order('likes desc,id desc')->select();
        foreach($comment as $value){
            $content = $value['content'];
            $str = strtr($content, $badword1);
            $value['content'] = $str;
            if($value['create_user'] == $userId){
                $value['self'] = 1;
            }else{
                $value['self'] = 0;
            }
            $map1 = array(
                'create_user' => $userId,
                'comment_id' => $value['id'],
                'status' => array('egt',0),
                'type' => 2, //评论点赞
            );

            $like = Like::where($map1)->find();
            ($like)?$value['is_like'] = 1:$value['is_like'] = 0;
            $users = WechatUser::where('userid',$value['create_user'])->find();
            $value['header'] = ($users['header']) ? $users['header'] : $users['avatar'];
            $value['nickname'] = ($users['nickname']) ? $users['nickname'] : $users['name'];
        }
        $this->assign('comment',$comment);

        return $this->fetch();
    }
    
    /**
     * 活动情况列表
     */
    public function activitylist(){

        $map = array(
            'status' => array('eq',1),
            'type' => 5,
        );
        $noticeModel = new NoticeModel();
        $list = $noticeModel::where($map)->order('id desc')->limit(7)->select();
        //判断是否为空
        if (empty($list)){
            $this->assign('show',0);
        }else{
            $this->assign('show',1);
        }
        $this->assign('list',$list);
        
        return $this->fetch();
    }

    /**
     * 更多活动详情
     */
    public function activitymore(){
        $len = input("length");
        $map = array(
            'type' => 5,
            'status' => array('eq',1)
        );
        $list = NoticeModel::where($map)->order('id desc')->limit($len,7)->select();
        foreach($list as $value){
            $img = Picture::get($value['front_cover']);
            $value['path'] = $img['path'];
            $value['time'] = date("Y-m-d",$value['create_time']);
        }
        if($list){
            return $this->success("加载成功","",$list);
        }else{
            return $this->error("加载失败");
        }
    }

    /**
     * 创意组织生活
     */
    public function regular() {

        //判断是否是游客
        $this->anonymous();

        $this->jssdk();

        $userId = session('userId');
        $id = input('id');
        $noticeModel = new NoticeModel();
        //浏览加一
        $info['views'] = array('exp','`views`+1');
        $noticeModel::where('id',$id)->update($info);
        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = array(
                'user_id' => $userId,
                'notice_id' => $id,
            );
            $history = Browse::get($con);
            if(!$history && $id != 0){
                Browse::create($con);
                $s['score'] = array('exp','`score`+1');
                WechatUser::where('userid',$userId)->update($s);
            }
        }

        //活动基本信息
        $list = $noticeModel::get($id);
        //重组轮播图片
        $list['carousel'] = json_decode($list['carousel_images']);
        $list['user'] = session('userId');
        //分享图片及链接及描述
        $image = Picture::where('id',$list['front_cover'])->find();
        $list['share_image'] = "http://".$_SERVER['SERVER_NAME'].$image['path'];
        $list['link'] = $_SERVER['REDIRECT_URL'];
        $list['desc'] = str_replace('&nbsp;','',strip_tags($list['content']));

        //文章点赞是否存在
        $map2 = array(
            'notice_id' => $id,
            'create_user' => $userId,
            'status' => 0,
            'type' => 1,
        );
        $msg = Like::where($map2)->find();
        if($msg) {
            $list['is_like'] = 1;
        }else{
            $list['is_like'] = 0;
        }

        //收藏是否存在
        unset($map2['type']);
        $col = Collect::where($map2)->find();
        if($col) {
            $list['is_collect'] = 1;
        }else {
            $list['is_collect'] = 0;
        }
        $this->assign('list',$list);

        //评论
        $map = array(
            'notice_id' => $id,
            'status' => 0
        );
        //敏感词屏蔽
        $badword = array(
            '法轮功','法轮','FLG','六四','6.4','flg'
        );
        $badword1 = array_combine($badword,array_fill(0,count($badword),'***'));
        $comment = Comment::where($map)->limit(0,5)->order('likes desc,id desc')->select();
        foreach($comment as $value){
            $content = $value['content'];
            $str = strtr($content, $badword1);
            $value['content'] = $str;
            if($value['create_user'] == $userId){
                $value['self'] = 1;
            }else{
                $value['self'] = 0;
            }
            $map1 = array(
                'create_user' => $userId,
                'comment_id' => $value['id'],
                'status' => array('egt',0),
                'type' => 2, //评论点赞
            );

            $like = Like::where($map1)->find();
            ($like)?$value['is_like'] = 1:$value['is_like'] = 0;
            $users = WechatUser::where('userid',$value['create_user'])->find();
            $value['header'] = ($users['header']) ? $users['header'] : $users['avatar'];
            $value['nickname'] = ($users['nickname']) ? $users['nickname'] : $users['name'];
        }
        $this->assign('comment',$comment);


        return $this->fetch();
    }

    /**
     * 创意组织生活  发布列表
     */
    public function regularlist() {

        $map = array(
            'status' => array('eq',1),
            'type' => 6,
        );
        $noticeModel = new NoticeModel();
        $list = $noticeModel::where($map)->order('id desc')->limit(7)->select();
        //判断是否为空
        if (empty($list)){
            $this->assign('show',0);
        }else{
            $this->assign('show',1);
        }
        foreach ($list as $value) {
            if($value['end_time'] < time()) {
                $value['is'] = 1;
            }else{
                $value['is'] = 0;
            }
        }
        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 创意组织生活  加载更多
     */
    public function regularmore() {
        $len = input("length");
        $map = array(
            'type' => 6,
            'status' => array('eq',1)
        );
        $list = NoticeModel::where($map)->order('id desc')->limit($len,7)->select();
        foreach($list as $value){
            $img = Picture::get($value['front_cover']);
            $value['path'] = $img['path'];
            $value['time'] = date("Y-m-d",$value['create_time']);
        }
        if($list){
            return $this->success("加载成功","",$list);
        }else{
            return $this->error("加载失败");
        }
    }


    /**
     * 通知发布
     */
    public function publish(){
        $noticeModel = new NoticeModel();
        if(IS_POST) {
            $data = input('post.');
            $data['start_time'] = strtotime($data['start_time']);
            $data['end_time'] = strtotime($data['end_time']);
            $data['userid'] = session('userId');
            if($data['id']) {
                $model = $noticeModel->save($data,['id' => $data['id']]);
            }else {
                unset($data['id']);
                //随机轮播图
                $a = array('1'=>'a','2'=>'b','3'=>'c','4'=>'d','5'=>'e','6'=>'f','7'=>'g','8'=>'h','9'=>'i','10'=>'j','11'=>'k','12'=>'l','13'=>'m','14'=>'n','15'=>'o',
                    '16'=>'p','17'=>'q','18'=>'r','19'=>'s','20'=>'t','21'=>'u','22'=>'v','23'=>'w','24'=>'x','25'=>'y','26'=>'z');
                $data['carousel_images'] = '["'.array_rand($a,1).'",'.'"'.array_rand($a,1).'",'.'"'.array_rand($a,1).'"]';
                $data['front_cover'] = array_rand($a,1);
                $data['create_time'] = time();
                $data['create_user'] = session('userId');
                $model = $noticeModel->create($data);
            }
            if($model) {
                $map['status'] = 0;
                $count = $noticeModel->where($map)->count();
                $content = "您有".$count."条[支部活动]审核消息，请点击【文章审核】及时查看。";
                $Wechat = new TPQYWechat(Config::get('party'));
                $message = array(
                    "totag" => 3,
                    "msgtype" => 'text',
                    "agentid" => 13,
                    "text" => array(
                        "content" => $content
                    ),
                    "safe" => "0"
                );
                $Wechat->sendMessage($message);  //审核通过，向用户推送提示
                return $this->success("编辑成功");
            }else{
                return $this->error("编辑失败");
            }
        }else{

            $id = input('id') ? input('id') : "";
            $notice = $noticeModel->where('id',$id)->find();
            $this->assign('pub',$notice);
            
            return $this->fetch();
        }
    }
    
    /**
     * 上传笔记
     */
    public function notes(){
        $noticeModel = new NoticeModel();
        $userId = session('userId');
        if(IS_POST) {
            $data = input('post.');
            $data['meet_time'] = strtotime($data['meet_time']);
            if($data['type'] == 2){
                //获取发布用的所在部门
                $departmentid = WechatDepartmentUser::where('userid',$userId)->select();
                $depart = array();
                foreach ($departmentid as $key => $value) {
                    $depart[] = (string)$value['departmentid'];
                }
                $data['departmentid'] = json_encode($depart);
                $data['tag'] = 0;
            }
            $data['userid'] = $userId;
            if(isset($data['images'])) {
                $data['images'] = json_encode($data['images']);
            }
            if($data['id']) {
                $model = $noticeModel->save($data,['id' => $data['id']]);
            }else {
                unset($data['id']);
                $a = array('1'=>'a','2'=>'b','3'=>'c','4'=>'d','5'=>'e','6'=>'f','7'=>'g','8'=>'h','9'=>'i','10'=>'j','11'=>'k','12'=>'l','13'=>'m','14'=>'n','15'=>'o',
                    '16'=>'p','17'=>'q','18'=>'r','19'=>'s','20'=>'t','21'=>'u','22'=>'v','23'=>'w','24'=>'x','25'=>'y','26'=>'z');
                $data['front_cover'] = array_rand($a,1);
                $data['create_time'] = time();
                $data['create_user'] = session('userId');
                $model = $noticeModel->create($data);
            }
            if($model) {
                $map['status'] = 0;
                $count = $noticeModel->where($map)->count();
                $content = "您有".$count."条[支部活动]审核消息，请点击【文章审核】进行查看。";
                $Wechat = new TPQYWechat(Config::get('party'));
                $message = array(
                    "totag" => 3,
                    "msgtype" => 'text',
                    "agentid" => 13,
                    "text" => array(
                        "content" => $content
                    ),
                    "safe" => "0"
                );
                $Wechat->sendMessage($message);  //审核通过，向用户推送提示
                return $this->success("编辑成功");
            }else{
                return $this->error("编辑失败");
            }
        }else{
            $id = input('id') ? input('id') : "";
            if($id != null){
                $this->assign('class',1); // 修改
                $notice = $noticeModel->where('id',$id)->find();
                $notice['images'] = json_decode($notice['images']);
                $this->assign('note',$notice);
            }else{
                $this->assign('class',0); // 添加
            }
            return $this->fetch();
        }
    }
}