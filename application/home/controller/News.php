<?php
/**
 * Created by PhpStorm.
 * User: daire
 * Date: 2017/12/11
 * Time: 13:20
 */

namespace app\home\controller;
use app\home\model\Browse;
use app\home\model\Comment;
use app\home\model\Like;
use app\home\model\Picture;
use app\home\model\WechatUser;
use app\home\model\WechatUserTag;
use com\wechat\TPQYWechat;
use think\Config;
use app\home\model\News as NewsModel;

/**
 * Class News
 * @package 第一聚焦
 */
class News extends Base
{
    /**
     * 主页
     */
    public function index(){
        $this->anonymous(); //判断是否是游客
//        $this->default_pic();
        //通知轮播
        $top = NewsModel::getTop();
        $this->assign('top',$top);
        $userId = session('userId');
        $map = [
            'status' => ['gt',0],
        ];
        $list = NewsModel::where($map)->order('id desc')->limit(10)->select();
        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 加载更多
     */
    public function indexmore(){
        $len = input('length');
        $map = [
            'status' => ['gt',0],
        ];
        $list = NewsModel::where($map)->order('id desc')->limit($len,6)->select();
        foreach($list as $value){
            $value['time'] = date("Y-m-d",$value['create_time']);
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
     * 详情页
     */
    public function detail(){
        //判断是否是游客
        $this->anonymous();

        $this->jssdk();

        $userId = session('userId');
        $id = input('id');
        $newsModel = new NewsModel();
        //浏览加一
        $info['views'] = ['exp','`views`+1'];
        $newsModel::where('id',$id)->update($info);

        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = [
                'user_id' => $userId,
                'news_id' => $id,
            ];
            $history = Browse::get($con);
            if(!$history && $id != 0){
                Browse::create($con);
                $s['score'] = ['exp','`score`+1'];
                WechatUser::where('userid',$userId)->update($s);
            }
        }

        $model = $newsModel->get($id);
        $model['user'] = session('userId');
        //分享图片及链接及描述
        $image = Picture::where('id',$model['front_cover'])->find();
        $model['share_image'] = "http://".$_SERVER['SERVER_NAME'].$image['path'];
        $model['link'] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'];
        $model['desc'] = str_replace('&nbsp;','',strip_tags($model['content']));
        $model['desc'] = str_replace(" ",'',$model['desc']);
        $model['desc'] = str_replace("\n",'',$model['desc']);

        //获取 文章点赞
        $likeModel = new Like;
        $like = $likeModel->getLike(6,$id,$userId);
        $model['is_like'] = $like;
        $model['images'] = json_decode($model['images']);
        $this->assign('article',$model);

        //获取 评论
        $commentModel = new Comment();
        $comment = $commentModel->getComment(6,$id,$userId);
        $this->assign('comment',$comment);
        return $this->fetch();
    }
}