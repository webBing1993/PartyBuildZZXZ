<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/5/11
 * Time: 14:21
 */

namespace app\home\controller;
use app\home\model\WechatUser;
use think\Controller;
use app\admin\model\Question;
use app\home\model\Answers;
use app\home\model\Browse;
use app\home\model\Comment;
use app\admin\model\Picture;
use app\home\model\Learns as LearnsModel;
use app\home\model\Like;
use think\Request;

class Learns extends Base{
    /**
     * 首页
     */
    public function index(){
        return $this->fetch();
    }
    /**
     * 十九大专题
     */
    public function meeting(){
        $this->anonymous();
        //数据列表
        $map = array(
            'type' => array('in',[1,2]),
            'status' => array('egt',0),
        );
        $list = LearnsModel::where($map)->limit(5)->order('id desc')->select();
        foreach($list as $value){
            if ($value['create_time'] >= strtotime(date('Y-m-d'))) {
                $value['time'] = date("H:i",$value['create_time']);
            }else{
                if ($value['create_time'] >= strtotime(date("Y-m-d",strtotime("-1 day")))) {
                    $value['time'] = date("昨天 H:i",$value['create_time']);
                }else{
                    $value['time'] = date("Y-n-j H:i",$value['create_time']);
                }
            }
        }
        $this->assign('list',$list);
        return $this->fetch();
    }
    /**
     * 主页加载更多
     */
    public function indexmore(){
        $len = input('length');
        $map = array(
            'type' => array('in',[1,2]),
            'status' => array('egt',0),
        );
        $list = LearnsModel::where($map)->order('id desc')->limit($len,3)->select();
        foreach($list as $value){
            $img = Picture::get($value['front_cover']);
            $value['path'] = $img['path'];
            if ($value['create_time'] >= strtotime(date('Y-m-d'))) {
                $value['time'] = date("H:i",$value['create_time']);
            }else{
                if ($value['create_time'] >= strtotime(date("Y-m-d",strtotime("-1 day")))) {
                    $value['time'] = date("昨天 H:i",$value['create_time']);
                }else{
                    $value['time'] = date("Y-n-j H:i",$value['create_time']);
                }
            }
        }
        if($list){
            return $this->success("加载成功",'',$list);
        }else{
            return $this->error("加载失败");
        }
    }
    /**
     * 视频课程
     */
    public function video(){
        $this->anonymous();        //判断是否是游客
        $this->jssdk();

        $userId = session('userId');
        $id = input('id');
        $learnsModel = new LearnsModel();
        //浏览加一
        $info['views'] = array('exp','`views`+1');
        $learnsModel::where('id',$id)->update($info);
        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = array(
                'user_id' => $userId,
                'learns_id' => $id,
            );
            $history = Browse::get($con);
            if(!$history && $id != 0){
                $s['score'] = array('exp','`score`+1');
//                if ($this->score_up()){
                    // 未满 15分
                    Browse::create($con);
                    WechatUser::where('userid',$userId)->update($s);
//                }
            }
        }
        $video = $learnsModel::get($id);
        $video['user'] = session('userId');
        //分享图片及链接及描述
        $image = Picture::where('id',$video['front_cover'])->find();
        $video['share_image'] = "http://".$_SERVER['SERVER_NAME'].$image['path'];
        $video['link'] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'];
        $video['desc'] = str_replace('&nbsp;','',strip_tags($video['content']));
        $video['desc'] = str_replace(" ",'',$video['desc']);
        $video['desc'] = str_replace("\n",'',$video['desc']);

        //获取 文章点赞
        $likeModel = new Like;
        $like = $likeModel->getLike(1,$id,$userId);
        $video['is_like'] = $like;
        $this->assign('video',$video);

        //获取 评论
        $commentModel = new Comment();
        $comment = $commentModel->getComment(1,$id,$userId);
        $this->assign('comment',$comment);
        return $this->fetch();
    }

    /**
     * 图文课程
     */
    public function article(){
        $this->anonymous();        //判断是否是游客
        $this->jssdk();

        $userId = session('userId');
        $id = input('id');
        $learnsModel = new LearnsModel();
        //浏览加一
        $info['views'] = array('exp','`views`+1');
        $learnsModel::where('id',$id)->update($info);
        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = array(
                'user_id' => $userId,
                'learns_id' => $id,
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
        $article = $learnsModel::get($id);
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
        $like = $likeModel->getLike(1,$id,$userId);
        $article['is_like'] = $like;
        $this->assign('article',$article);

        //获取 评论
        $commentModel = new Comment();
        $comment = $commentModel->getComment(1,$id,$userId);
        $this->assign('comment',$comment);

        return $this->fetch();
    }
}