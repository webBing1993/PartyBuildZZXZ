<?php
/**
 * Created by PhpStorm.
 * User: daire
 * Date: 2017/12/29
 * Time: 15:59
 */

namespace app\home\controller;
use app\home\model\Browse;
use app\home\model\Collect;
use app\home\model\Comment;
use app\home\model\Like;
use app\home\model\Picture;
use app\home\model\WechatUser;
use app\home\model\WechatUserTag;
use com\wechat\TPQYWechat;
use think\Config;
use app\home\model\Study as StudyModel;

/**
 * Class Study
 * @package 基本书目
 */
class Study extends Base
{
    /**
     * 主页
     */
    public function index(){
        $this->anonymous(); //判断是否是游客
//        $this->default_pic();

        $userId = session('userId');
        //党的历史 type = 1
        $map1 = array(
            'type' => 1,
            'status' => array('egt',0)
        );
        $list1 = StudyModel::where($map1)->order('id desc')->limit(6)->select();
        foreach ($list1 as $v) {
            $v['desc'] = str_replace('&nbsp;','',strip_tags($v['content']));
            $v['desc'] = str_replace(" ",'',$v['desc']);
            $v['desc'] = str_replace("\n",'',$v['desc']);
        }
        $this->assign('list1',$list1);

        //党的理论 type = 2
        $map2 = array(
            'type' => 2,
            'status' => array('egt',0)
        );
        $list2 = StudyModel::where($map2)->order('id desc')->limit(6)->select();
        foreach ($list2 as $v) {
            $v['desc'] = str_replace('&nbsp;','',strip_tags($v['content']));
            $v['desc'] = str_replace(" ",'',$v['desc']);
            $v['desc'] = str_replace("\n",'',$v['desc']);
        }
        $this->assign('list2',$list2);

        //党的修养 type = 3
        $map3 = array(
            'type' => 3,
            'status' => array('egt',0)
        );
        $list3 = StudyModel::where($map3)->order('id desc')->limit(6)->select();
        foreach ($list3 as $v) {
            $v['desc'] = str_replace('&nbsp;','',strip_tags($v['content']));
            $v['desc'] = str_replace(" ",'',$v['desc']);
            $v['desc'] = str_replace("\n",'',$v['desc']);
        }
        $this->assign('list3',$list3);

        //党务知识 type = 4
        $map4 = array(
            'type' => 4,
            'status' => array('egt',0)
        );
        $list4 = StudyModel::where($map4)->order('id desc')->limit(6)->select();
        foreach ($list4 as $v) {
            $v['desc'] = str_replace('&nbsp;','',strip_tags($v['content']));
            $v['desc'] = str_replace(" ",'',$v['desc']);
            $v['desc'] = str_replace("\n",'',$v['desc']);
        }
        $this->assign('list4',$list4);

        //党规党章 type = 5
        $map5 = array(
            'type' => 5,
            'status' => array('egt',0)
        );
        $list5 = StudyModel::where($map5)->order('id desc')->limit(6)->select();
        foreach ($list5 as $v) {
            $v['desc'] = str_replace('&nbsp;','',strip_tags($v['content']));
            $v['desc'] = str_replace(" ",'',$v['desc']);
            $v['desc'] = str_replace("\n",'',$v['desc']);
        }
        $this->assign('list5',$list5);

        //系列讲话 type = 6
        $map6 = array(
            'type' => 6,
            'status' => array('egt',0)
        );
        $list6 = StudyModel::where($map6)->order('id desc')->limit(6)->select();
        foreach ($list6 as $v) {
            $v['desc'] = str_replace('&nbsp;','',strip_tags($v['content']));
            $v['desc'] = str_replace(" ",'',$v['desc']);
            $v['desc'] = str_replace("\n",'',$v['desc']);
        }
        $this->assign('list6',$list6);

        return $this->fetch();
    }
    /**
     * 主页加载更多
     */
    public function indexmore(){
        $len = input('length');
        $map = array(
            'type' => input('type'),
            'status' => array('egt',0),
        );
        $list = StudyModel::where($map)->order('id desc')->limit($len,6)->select();
        foreach ($list as $v) {
            $v['desc'] = str_replace('&nbsp;','',strip_tags($v['content']));
            $v['desc'] = str_replace(" ",'',$v['desc']);
            $v['desc'] = str_replace("\n",'',$v['desc']);
        }
        if($list){
            return $this->success("加载成功",'',$list);
        }else{
            return $this->error("加载失败");
        }
    }
    /**
     * 详情页
     */
    public function detail(){
        $this->anonymous();        //判断是否是游客
        $this->jssdk();

        $userId = session('userId');
        $id = input('id');
        $studyModel = new StudyModel();
        //浏览加一
        $info['views'] = array('exp','`views`+1');
        $studyModel::where('id',$id)->update($info);
        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = array(
                'user_id' => $userId,
                'study_id' => $id,
            );
            $history = Browse::get($con);
            if(!$history && $id != 0){
                $s['score'] = array('exp','`score`+1');
//                if ($this->score_up()){
                // 未满 15 分
                Browse::create($con);
                WechatUser::where('userid',$userId)->update($s);
//                }
            }
        }
        $article = $studyModel::get($id);
        $article['user'] = session('userId');
        //分享图片及链接及描述
        $image = Picture::where('id',$article['front_cover'])->find();
        $article['share_image'] = "http://".$_SERVER['SERVER_NAME'].$image['path'];
        $article['link'] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'];
        $article['desc'] = str_replace('&nbsp;','',strip_tags($article['content']));
        $article['desc'] = str_replace(" ",'',$article['desc']);
        $article['desc'] = str_replace("\n",'',$article['desc']);

        //获取 文章点赞
        $likeModel = new Like;
        $like = $likeModel->getLike(5,$id,$userId);
        $article['is_like'] = $like;
        //获取 收藏
        $collectModel = new Collect();
        $collect = $collectModel->getCollect(2,$id,$userId);
        $article['is_collect'] = $collect;
        $this->assign('article',$article);
        //获取 评论
        $commentModel = new Comment();
        $comment = $commentModel->getComment(5,$id,$userId);
        $this->assign('comment',$comment);

        return $this->fetch();
    }
}