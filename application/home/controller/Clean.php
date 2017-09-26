<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/5/11
 * Time: 14:21
 */

namespace app\home\controller;

use think\Controller;
use app\home\model\Comment;
use app\admin\model\Picture;
use app\home\model\Clean as CleanModel;
use app\home\model\Like;
use app\home\model\Browse;
use app\home\model\WechatUser;

class Clean extends Base{
    /**
     * 廉政文化首页
     */
    public function index(){
        $Model = new CleanModel();
        if(IS_POST) {
            $data = input('post.');
            $map = array(
                'type' => $data['type'],
                'status' => 1,
            );
            $list = $Model->where($map)->order('create_time desc')->limit($data['length'],5)->select();
            foreach ($list as $value) {
                $path = Picture::get($value['front_cover']);
                $value['path'] = $path['path'];
                $value['time'] = time_format($value['create_time'],"Y-m-d");
            }
            if($list) {
                return $this->success("加载成功","",$list);
            }else {
                return $this->error("加载失败");
            }
        }else {
            $map1 = array(
                'type' => 1,
                'status' => 1
            );
            $list1 = $Model->where($map1)->order('create_time desc')->limit(5)->select();
            $this->assign('list1',$list1);

            $map2 = array(
                'type' => 2,
                'status' => 1
            );
            $list2 = $Model->where($map2)->order('create_time desc')->limit(5)->select();
            $this->assign('list2',$list2);

            $map3 = array(
                'type' => 3,
                'status' => 1
            );
            $list3 = $Model->where($map3)->order('create_time desc')->limit(5)->select();
            $this->assign('list3',$list3);

            return $this ->fetch();
        }
    }
    /**
     * 在线视频详情
     */
    public function video(){
        $this->anonymous();        //判断是否是游客
        $this->jssdk();

        $uid = session('userId');
        $id = input('id');
        $learnModel = new CleanModel();
        //浏览加一
        $info['views'] = array('exp','`views`+1');
        $learnModel::where('id',$id)->update($info);
        if($uid != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = array(
                'user_id' => $uid,
                'clean_id' => $id,
            );
            $history = Browse::get($con);
            if(!$history && $id != 0){
                Browse::create($con);
                $s['score'] = array('exp','`score`+1');
                WechatUser::where('userid',$uid)->update($s);
            }
        }

        $video = $learnModel::get($id);
        $video['user'] = session('userId');
        //分享图片及链接及描述
//        $image = Picture::where('id',$video['front_cover'])->find();
//        $video['share_image'] = "http://".$_SERVER['SERVER_NAME'].$image['path'];
//        $video['link'] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'];
//        $video['desc'] = str_replace('&nbsp;','',strip_tags($video['content']));

        //获取 文章点赞
        $likeModel = new Like;
        $like = $likeModel->getLike(16,$id,$uid);
        $video['is_like'] = $like;
        $this->assign('video',$video);

        //获取 评论
        $commentModel = new Comment();
        $comment = $commentModel->getComment(16,$id,$uid);
        $this->assign('comment',$comment);
        return $this->fetch();
    }
	/**
	 * 家规家风详情
	 */
	public function article(){
		$this->anonymous();        //判断是否是游客
        $this->jssdk();

        $uid = session('userId');
        $id = input('id');
        $learnModel = new CleanModel();
        //浏览加一
        $info['views'] = array('exp','`views`+1');
        $learnModel::where('id',$id)->update($info);

        if($uid != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = array(
                'user_id' => $uid,
                'clean_id' => $id,
            );
            $history = Browse::get($con);
            if(!$history && $id != 0){
                Browse::create($con);
                $s['score'] = array('exp','`score`+1');
                WechatUser::where('userid',$uid)->update($s);
            }
        }

        $article = $learnModel::get($id);
        $article['user'] = session('userId');
        //分享图片及链接及描述
//        $image = Picture::where('id',$article['front_cover'])->find();
//        $article['share_image'] = "http://".$_SERVER['SERVER_NAME'].$image['path'];
//        $article['link'] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'];
//        $article['desc'] = str_replace('&nbsp;','',strip_tags($article['content']));

        //获取 文章点赞
        $likeModel = new Like;
        $like = $likeModel->getLike(16,$id,$uid);
        $article['is_like'] = $like;
        $this->assign('detail',$article);

        //获取 评论
        $commentModel = new Comment();
        $comment = $commentModel->getComment(16,$id,$uid);
        $this->assign('comment',$comment);

		return $this->fetch();
	}

}