<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2016/11/2
 * Time: 13:21
 */
namespace app\home\controller;
use app\home\model\Learn;
use app\home\model\Browse;
use app\home\model\Picture;
use app\home\model\Like;
use app\home\model\Comment;
use app\home\model\WechatUser;

class Topic extends Base{
    /*
     * 专题讨论主页
     */
    public function index(){
        $map1 = array(
            'type' => 4,
            'status' => 0
        );
        $learn = Learn::where($map1)->order('id desc')->limit(7)->select();
        $this->assign('list',$learn);
        return $this->fetch();
    }
    /*
     * 专题讨论   加载更多
     */
    public function more(){
        $len = input('length');
        $map = array(
            'type' => 4,
            'status' => array('eq',0),
        );
        $list = Learn::where($map)->order('id desc')->limit($len,5)->select();
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
    /*
     * 党性体检 主页
     */
    public function experience(){
        $map2 = array(
            'type' => 5,
            'status' => 0
        );
        $learn = Learn::where($map2)->order('id desc')->limit(7)->select();
        $this->assign('list',$learn);
        return $this->fetch();
    }
    /*
     * 党性体检  加载更多
     */
    public function experiencemore(){
        $len = input('length');
        $map = array(
            'type' => 5,
            'status' => array('eq',0),
        );
        $list = Learn::where($map)->order('id desc')->limit($len,5)->select();
        foreach($list as $value){
            $value['time'] = date("Y-m-d",$value['create_time']);
        }
        if($list){
            return $this->success("加载成功",'',$list);
        }else{
            return $this->error("加载失败");
        }
    }
    /*
     * 详情页
     */
    public function detail(){
        $this->anonymous();        //判断是否是游客
        $this->jssdk();
        $id = input('id');
        $userId = session('userId');
        $learnModel = new Learn();
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
                $s['score'] = array('exp','`score`+1');
                if ($this->score_up()){
                    // 未满 15 分
                    Browse::create($con);
                    WechatUser::where('userid',$userId)->update($s);
                }
            }
        }

        $article = $learnModel::get($id);
        $article['user'] = session('userId');
        //分享图片及链接及描述
        $image = Picture::where('id',$article['front_cover'])->find();
        $article['share_image'] = "http://".$_SERVER['SERVER_NAME'].$image['path'];
        $article['link'] = $_SERVER['REDIRECT_URL'];
        $article['desc'] = str_replace('&nbsp;','',strip_tags($article['content']));

        //获取 文章点赞
        $likeModel = new Like;
        $like = $likeModel->getLike(1,$id,$userId);
        $article['is_like'] = $like;
        if($article['type'] == 4){
            $this->assign('c',1);
        }else{
            $this->assign('c',0);
        }
        $this->assign('detail',$article);

        //获取 评论
        $commentModel = new Comment();
        $comment = $commentModel->getComment(1,$id,$userId);
        $this->assign('comment',$comment);

        return $this->fetch();
    }
}
