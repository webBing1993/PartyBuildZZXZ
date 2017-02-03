<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2016/11/2
 * Time: 13:21
 */
namespace app\home\controller;
use app\home\model\AllianceArrange;
use app\home\model\AllianceShow;
use app\home\model\Comment;
use app\home\model\Like;
use think\Log;

class Alliance extends Base{
    /*
     * 地信红盟主页
     */
    public function index(){
        //活动安排
        $arrangeModel = new AllianceArrange();
        $map['status'] = 1;
        $arrange = $arrangeModel->where($map)->order('create_time desc')->limit(2)->select();
        $this->assign('arrange',$arrange);
//
//        //活动展示
//        $showModel = new AllianceShow();
//        $order = array('type desc','create_time desc');
//        $data = $showModel->where($map)->order($order)->select();
//        if (empty($data)) {
//            return $this->error("没有取到数据!");
//        }
//        $list = [];
//        foreach($data as $value)
//        {
//            $year = date('Y',$value['create_time']);    //年
//            $month = date('n',$value['create_time']);   //月
//            $quarter = $month%3 == 0?$month/3:intval($month/3+1);   //季
//            switch ($value['type']) {
//                case "1":
//                    $list[$year][$quarter]['msg'][$month]['data'][] = $value->toArray();
//                    break;
//                case "2":
//                    $list[$year][$quarter]['msg'][$month]['data'][] = $value->toArray();
//                    break;
//                case "3":
//                    $list[$year][$quarter]['data'][] = $value->toArray();
//                    $list[$year][$quarter]['quarter'] = $quarter;
//                    break;
//                default:
//                    Log::error("");
//                    break;
//            }
//        }
//        $this->assign('list',$list);
        return $this->fetch();
    }

    /*
     * 活动安排更多页
     */
    public function lists(){
        $map = array(
            'status' => 1,
        );
        $arrangeModel = new AllianceArrange();
        $list = $arrangeModel->where($map)->order('create_time desc')->limit(7)->select();
        foreach ($list as $value) {
            if($value['time'] < time()) {
                $value['begin'] = 0;    //未开始
            }else{
                $value['begin'] = 1;    //已结束
            }
        }
        $this->assign('list',$list);
        return $this->fetch();
    }

    /*
     * 加载更多
     */
    public function listsmore() {
        $len = input('length');
        $map =  array(
            'status' => 1,
        );
        $arrangeModel = new AllianceArrange();
        $list = $arrangeModel->where($map)->order('create_time desc')->limit($len,7)->select();
        foreach ($list as $value) {
            $value['time'] = date('Y-m-d',$value['time']);
        }
        if($list) {
            return $this->success("加载成功","",$list);
        }else{
            return $this->error("加载失败");
        }
    }

    /*
     * 通知详情页
     */
    public function informdetail(){
        $this->anonymous(); //判断是否是游客

        $uid = session('userId');
        $id = input('id');
        $arrangeModel = new AllianceArrange();

        $arrangeModel::where('id',$id)->setInc('views');     //浏览加一

        $info = $arrangeModel->get($id);     //获取基本数据

        //点赞
        $likeModel = new Like();
        $like = $likeModel->getLike(3,$id,$uid);
        $info['is_like'] = $like;
        $this->assign('info',$info);

        //获取评论
        $commentModel = new Comment();
        $comment = $commentModel->getComment(3,$id,$uid);
        $this->assign('comment',$comment);

        return $this->fetch();
    }

    /*
     * 文章详情页
     */
    public function articledetail(){
        $this->anonymous(); //判断是否是游客

        $uid = session('userId');
        $id = input('id');
        $showModel = new AllianceShow();
        $showModel::where('id',$id)->setInc('views');     //浏览加一

        $info = $showModel->get($id);
        //点赞
        $likeModel = new Like();
        $like = $likeModel->getLike(4,$id,$uid);
        $info['is_like'] = $like;
        $this->assign('info',$info);

        //获取评论
        $commentModel = new Comment();
        $comment = $commentModel->getComment(4,$id,$uid);
        $this->assign('comment',$comment);
        return $this->fetch();
    }
    /*
     * 文章列表页
     */
    public function articlelist(){
        $map = array(
            'type' => input('type'),
            'status' => 1,
        );
        $showModel = new AllianceShow();
        $list = $showModel->where($map)->order('create_time desc')->limit(7)->select();
        $this->assign('list',$list);

        return $this->fetch();
    }

    /*
     * 加载更多
     */
    public function articlemore() {
        $len = input('length');
        $map =  array(
            'type' => input('type'),
            'status' => 1,
        );
        $showModel = new AllianceShow();
        $list = $showModel->where($map)->order('create_time desc')->limit($len,7)->select();
        foreach ($list as $value) {
            $value['time'] = date('Y-m-d',$value['time']);
        }
        if($list) {
            return $this->success("加载成功","",$list);
        }else{
            return $this->error("加载失败");
        }
    }

}
