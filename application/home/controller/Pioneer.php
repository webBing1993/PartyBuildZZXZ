<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/10/19
 * Time: 19:09
 */

namespace app\home\controller;
use app\home\model\Browse;
use app\home\model\Comment;
use app\home\model\Like;
use app\home\model\Pioneer as NewsModel;
use app\home\model\Picture;
use app\home\model\WechatUser;

/**
 * Class Trends
 * @package app\home\controller
 * 先锋社区
 */

class Pioneer extends Base{
    /**
     * 主页
     */
    public function index(){
        $Model = new NewsModel();
        $tops = $Model->getThreeTop();   //获取三条轮播记录
        $this->assign('tops',$tops);

        $news = $Model->getNewMessage();  //获取新闻资讯
        $this->assign('news',$news);

        $files = $Model->getFileNote();  //获取新闻通知
        $this->assign('files',$files);

        return $this->fetch();
    }

    /**
     * 详情页
     */
    public function detail(){
        $this->anonymous();
        $this->jssdk();

        $id = input('id');
        $userId = session('userId');
        //浏览加一
        $info['views'] = array('exp','`views`+1');
        NewsModel::where('id',$id)->update($info);

        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = array(
                'user_id' => $userId,
                'pioneer_id' => $id,
            );
            $history = Browse::get($con);
            if(!$history && $id != 0){
                Browse::create($con);
                $s['score'] = array('exp','`score`+1');
                WechatUser::where('userid',$userId)->update($s);
            }
        }

        //详细信息
        $info = NewsModel::get($id);
        //分享图片及链接及描述
        $image = Picture::where('id',$info['front_cover'])->find();
        $info['share_image'] = "http://".$_SERVER['SERVER_NAME'].$image['path'];
        $info['link'] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'];
        $info['desc'] = str_replace('&nbsp;','',strip_tags($info['content']));

        //获取 文章点赞
        $likeModel = new Like();
        $like = $likeModel->getLike(17,$id,$userId);
        $info['is_like'] = $like;
        $this->assign('detail',$info);
        //获取 评论
        $commentModel = new Comment();
        $comment = $commentModel->getComment(17,$id,$userId);
        $this->assign('comment',$comment);

        return $this->fetch();
    }

    /**
     * 获取更多数据
     */
    public function listMore(){
        $data = input('post.');
        $Model = new NewsModel();
        $list = $Model->getMoreList($data);
        if($list){
            return $this->success("加载成功",'',$list);
        }else{
            return $this->error("加载失败");
        }
    }
}