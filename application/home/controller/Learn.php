<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/9/12
 * Time: 16:34
 */

namespace app\home\controller;
use app\admin\model\Picture;
use app\home\model\Browse;
use app\home\model\Collect;
use app\home\model\Comment;
use app\home\model\Like;
use app\home\model\WechatUser;
use think\Controller;

use app\home\model\Learn as LearnModel;
/**
 *
 * @package 两学一做
 */
class Learn extends Base {
    /**
     * 主页
     */
    public function index(){
        //推荐
        $map1 = array(
            'status' => array('egt',0),
            'recommend' => 1
        );
        $list1 = LearnModel::where($map1)->limit(3)->order('id desc')->select();
        $this->assign('list1',$list1);

        //数据列表
        $map2 = array(
            'status' => array('egt',0),
        );
        $list2 = LearnModel::where($map2)->limit(5)->order('id desc')->select();
        $this->assign('list2',$list2);

        return $this->fetch();
    }

    /**
     * 主页加载更多
     */
    public function indexmore(){
        $len = input('length');
        $map = array(
            'status' => array('eq',0),
        );
        $list = LearnModel::where($map)->order('id desc')->limit($len,5)->select();
        foreach($list as $value){
            $img = Picture::get($value['list_image']);
            $value['path'] = $img['path'];
            $value['time'] = date("Y-m-d",$value['create_time']);
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
        $learnModel = new LearnModel();
        //浏览加一
        $info['views'] = array('exp','`views`+1');
        $learnModel::where('id',$id)->update($info);
        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = array(
                'user_id' => $userId,
                'learn_id' => $id,
            );
            $history = Browse::get($con);
            if(!$history && $id != 0){
                Browse::create($con);
                $s['score'] = array('exp','`score`+1');
                WechatUser::where('userid',$userId)->update($s);
            }
        }

        $video = $learnModel::get($id);
        $video['user'] = session('userId');
        //分享图片及链接及描述
        $image = Picture::where('id',$video['front_cover'])->find();
        $video['share_image'] = "http://".$_SERVER['SERVER_NAME'].$image['path'];
        $video['link'] = $_SERVER['REDIRECT_URL'];
        $video['desc'] = str_replace('&nbsp;','',strip_tags($video['content']));

        //文章点赞是否存在
        $map2 = array(
            'learn_id' => $id,
            'create_user' => $userId,
            'status' => 0,
            'type' => 1,
        );
        $msg = Like::where($map2)->find();
        if($msg) {
            $video['is_like'] = 1;
        }else{
            $video['is_like'] = 0;
        }

        //收藏是否存在
        unset($map2['type']);
        $col = Collect::where($map2)->find();
        if($col) {
            $video['is_collect'] = 1;
        }else {
            $video['is_collect'] = 0;
        }
        $this->assign('video',$video);

        //评论
        $map = array(
            'learn_id' => $id,
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
     * 图文课程
     */
    public function article(){
        $this->anonymous();        //判断是否是游客
        $this->jssdk();

        $userId = session('userId');
        $id = input('id');
        $learnModel = new LearnModel();
        //浏览加一
        $info['views'] = array('exp','`views`+1');
        $learnModel::where('id',$id)->update($info);
        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = array(
                'user_id' => $userId,
                'learn_id' => $id,
            );
            $history = Browse::get($con);
            if(!$history && $id != 0){
                Browse::create($con);
                $s['score'] = array('exp','`score`+1');
                WechatUser::where('userid',$userId)->update($s);
            }
        }

        $article = $learnModel::get($id);
        $article['user'] = session('userId');
        //分享图片及链接及描述
        $image = Picture::where('id',$article['front_cover'])->find();
        $article['share_image'] = "http://".$_SERVER['SERVER_NAME'].$image['path'];
        $article['link'] = $_SERVER['REDIRECT_URL'];
        $article['desc'] = str_replace('&nbsp;','',strip_tags($article['content']));

        //文章点赞是否存在
        $map2 = array(
            'learn_id' => $id,
            'create_user' => $userId,
            'status' => 0,
            'type' => 1,
        );
        $msg = Like::where($map2)->find();
        if($msg) {
            $article['is_like'] = 1;
        }else{
            $article['is_like'] = 0;
        }

        //收藏是否存在
        unset($map2['type']);
        $col = Collect::where($map2)->find();
        if($col) {
            $article['is_collect'] = 1;
        }else {
            $article['is_collect'] = 0;
        }
        $this->assign('article',$article);

        //评论
        $map = array(
            'learn_id' => $id,
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
    
    
}