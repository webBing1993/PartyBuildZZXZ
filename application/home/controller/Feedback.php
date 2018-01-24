<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/11/21
 * Time: 10:27
 */

namespace app\home\controller;
use app\home\model\Comment;
use app\home\model\Like;
use app\home\model\Opinion;
use app\home\model\Pioneer;
use app\home\model\Picture;
use app\home\model\WechatUser;
use com\wechat\TPQYWechat;
use think\Config;
use think\Db;

/**
 * Class Feedback
 * @package app\home\controller
 * 示范评比
 */
class Feedback extends Base {
    /**
     * 主页
     */
    public function index() {
        $this->anonymous();
        $userId = session('userId');
        $map = [
            'status' => array('eq',0),
        ];
        $optionModel = new Opinion();
        $list = $optionModel->where($map)->order('id desc')->limit(6)->select();
        foreach ($list as $value) {
            //获取用户信息
            $value['images'] = json_decode($value['images']);
            $value['username'] = $value->user->name;
            ($value->user->header) ? $value['header'] = $value->user->header : $value['header'] = $value->user->avatar;

            //是否点赞
            $likeModel = new Like;
            $like = $likeModel->getLike(8,$value['id'],$userId);
            $value['is_like'] = $like;

            //获取相关意见反馈评论
            /*$map1 = [
                'aid' => $value['id'],
                'type' => 13,
                'status' => array('egt',0)
            ];
            $comment = Comment::where($map1)->select();
            foreach ($comment as $k => $val) {

                $val['username'] = get_name($val['uid']);
            }
            $value['comment'] = $comment;*/
        }
        $this->assign('list',$list);

        $politics_status = WechatUser::where(['userid' => $userId])->value('politics_status');
        if($politics_status == 3) {//党员才能推荐
            $publish = 1;
        }else{
            $publish = 0;
        }
        $this->assign('publish',$publish);

        $map2 = [
            'status' => ['egt',0],
        ];
        $list2 = Pioneer::where($map2)->limit(6)->order('id desc')->select();
        $this->assign('list2',$list2);

        return $this->fetch();
    }
    /**
     * 主页加载更多
     */
    public function pioneermore(){
        $len = input('length');
        $map = [
            'status' => ['egt',0],
        ];
        $list = Pioneer::where($map)->order('id desc')->limit($len,6)->select();
        foreach($list as $value){
            $img = Picture::get($value['front_cover']);
            $value['path'] = $img['path'];
        }
        if($list){
            return $this->success("加载成功",'',$list);
        }else{
            return $this->error("加载失败");
        }
    }

    /**
     * 反馈提交页
     */
    public function publish() {
        $this->checkAnonymous();
        if(IS_POST) {
            $userId = session('userId');
            $data = input('post.');
            $department = Db::table('pb_wechat_department_user')
                ->alias('a')
                ->join('pb_wechat_department b','a.departmentid = b.id','LEFT')
                ->where('a.userid',$userId)
                ->find();
            $data['department_name'] = $department['name'];
            $data['images'] = json_encode($data['images']);
            $data['create_user'] = $userId;
            $opinionModel = new Opinion();
            $model = $opinionModel->create($data);
            if ($model) {

                return $this->success("提交成功");
            } else {

                return $this->error("提交失败");
            }
        }else{
            return $this->fetch();
        }
    }

    /**
     * 加载更多
     */
    public function indexmore(){
        $len = input('length');
        $userId = session('userId');
        $map = [
            'status' => array('eq',0)
        ];
        $list = Opinion::where($map)->order('id desc')->limit($len,6)->select();
        foreach ($list as $value) {
            //获取用户信息
            $value['images'] = json_decode($value['images']);
            $image =array();
            if (!empty($value['images'])) {
                foreach ($value['images'] as $k=>$val){
                    $img = Picture::get($val);
                    $image[$k] = $img['path'];
                }
            }
            $value['images'] = $image;
            $value['username'] = $value->user->name;
            ($value->user->header) ? $value['header'] = $value->user->header : $value['header'] = $value->user->avatar;

            //是否点赞
            $likeModel = new Like;
            $like = $likeModel->getLike(8,$value['id'],$userId);
            $value['is_like'] = $like;
            $value['time'] = date("Y-m-d",$value['create_time']);

            //获取相关意见反馈评论
            /*$map1 = [
                'aid' => $value['id'],
                'type' => 13,
                'status' => array('egt',0)
            ];
            $comment = Comment::where($map1)->select();
            foreach ($comment as $k => $val) {
                $val['username'] = get_name($val['uid']);
            }
            $value['comment'] = $comment;*/
        }
        if ($list) {

            return $this->success("加载成功","",$list);
        } else {

            return $this->error("加载失败");
        }
    }
    /*
     * 典型引路
     */
    public function pioneer(){
        $this->anonymous();        //判断是否是游客
        $this->jssdk();

        $userId = session('userId');
        $id = input('id');
        /*$learnsModel = new Pioneer();
        //浏览加一
        $info['views'] = ['exp','`views`+1'];
        $learnsModel::where('id',$id)->update($info);
        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = [
                'user_id' => $userId,
                'learns_id' => $id,
            ];
            $history = Browse::get($con);
            if(!$history && $id != 0){
                $s['score'] = ['exp','`score`+1'];
//                if ($this->score_up()){
                // 未满 15 分
                Browse::create($con);
                WechatUser::where('userid',$userId)->update($s);
//                }
            }
        }*/
        $article = Pioneer::get($id);
        $article['user'] = session('userId');
        //分享图片及链接及描述
        $image = Picture::where('id',$article['front_cover'])->find();
        $article['share_image'] = "http://".$_SERVER['SERVER_NAME'].$image['path'];
        $article['link'] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'];
        $article['desc'] = str_replace('&nbsp;','',strip_tags($article['content']));
        $article['desc'] = str_replace(" ",'',$article['desc']);
        $article['desc'] = str_replace("\n",'',$article['desc']);

        $this->assign('article',$article);

        return $this->fetch();
    }

//    新建推荐
    public function detail(){
        return $this->fetch();
    }

}