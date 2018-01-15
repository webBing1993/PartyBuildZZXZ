<?php
/**
 * Created by PhpStorm.
 * User: daire
 * Date: 2018/1/8
 * Time: 13:06
 */

namespace app\home\controller;
use app\home\model\Browse;
use app\home\model\Comment;
use app\home\model\Like;
use app\home\model\Picture;
use app\home\model\WechatDepartmentUser;
use app\home\model\WechatUser;
use app\home\model\WechatDepartment;
use app\home\model\WechatUserTag;
use app\home\model\Meet;

class Service extends Base
{
    /**
     * 党员管理
     */
    public function manage(){
        $this->anonymous();
        //数据列表
        $map = [
            'status' => ['egt',0],
        ];
        $list = Meet::where($map)->limit(10)->order('id desc')->select();
        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 参会情况加载更多
     */
    public function indexmore(){
        $len = input('length');
        $map = [
            'status' => ['egt',0],
        ];
        $list = Meet::where($map)->order('id desc')->limit($len,6)->select();
        foreach($list as $value){
            $img = Picture::get($value['front_cover']);
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
     * 详情页
     */
    public function meetdetail(){
        $this->anonymous();        //判断是否是游客
        $this->jssdk();

        $userId = session('userId');
        $id = input('id');
        $meetModel = new Meet();
        //浏览加一
        $info['views'] = ['exp','`views`+1'];
        $meetModel::where('id',$id)->update($info);
        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = [
                'user_id' => $userId,
                'meet_id' => $id,
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
        }
        $article = $meetModel::get($id);
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
        $like = $likeModel->getLike(7,$id,$userId);
        $article['is_like'] = $like;
        $this->assign('article',$article);
        //获取 评论
        $commentModel = new Comment();
        $comment = $commentModel->getComment(7,$id,$userId);
        $this->assign('comment',$comment);

        return $this->fetch();
    }
}