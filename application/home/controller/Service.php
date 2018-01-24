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
use think\Db;

class Service extends Base
{
    static $MEMBER_TAG = 1; // 党员标签ID
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

        // 党员情况

        $dLists = WechatDepartment::where(['parentid'=>1])->select(); // 部门列表
        $this->assign('dList',$dLists);

        return $this->fetch();
    }

    /**
     *  获取部门党员列表
     */
    public function getDepartmentMember($did)
    {
        if (empty($did)) {

            $this->error('部门参数错输！');
        } else {
            $list = Db::table('pb_wechat_user')
                ->alias('a')
                ->join('pb_wechat_department_user b','a.userid = b.userid')
                ->join('pb_wechat_user_tag c','a.userid = c.userid')
                ->join('pb_wechat_department d','b.departmentid = d.id')
                ->field('d.name as dname,a.userid,a.name,a.mobile,a.avatar,a.position')
                ->where(['departmentid'=>$did,'tagid'=>$this::$MEMBER_TAG])
                ->select();

            return $this->success('获取成功！',null,json_encode($list));
        }
    }

    /**
     * 党员单人搜索
     */
    public function getMember($search)
    {
        if (empty($search)) {

            $this->error('参数错输！');
        } else {
            $list = Db::table('pb_wechat_user')
                ->alias('a')
                ->join('pb_wechat_department_user b','a.userid = b.userid','left')
                ->join('pb_wechat_user_tag c','a.userid = c.userid','left')
                ->join('pb_wechat_department d','b.departmentid = d.id','left')
                ->field('d.name as dname,a.userid,a.name,a.mobile,a.avatar,a.position')
                ->where(['a.name'=>['like',"%$search%"],'tagid'=>$this::$MEMBER_TAG])
                ->select();

            return $this->success('搜索获取成功！',null,json_encode($list));
        }

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