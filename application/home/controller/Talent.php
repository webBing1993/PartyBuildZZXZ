<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2016/11/2
 * Time: 13:21
 */
namespace app\home\controller;

use app\home\model\Like;
use app\home\model\Picture;
use think\Controller;
use app\home\model\Talent as TalentModel;

class Talent extends Base{
    /*
     * 人才创业
     */
    public function index(){
        $talentModel = new TalentModel();
        //政策解读
        $map1 = array(
            'type' => 1,
            'status' => 1,
        );
        $list1 = $talentModel->where($map1)->order('create_time desc')->limit(2)->select();
        $this->assign('list1',$list1);

        //申请流程
        $map2 = array(
            'type' => 2,
            'status' => 1,
        );
        $list2 = $talentModel->where($map2)->order('create_time desc')->limit(2)->select();
        $this->assign('list2',$list2);

        //创业支持
        $map3 = array(
            'type' => 3,
            'status' => 1,
        );
        $list3 = $talentModel->where($map3)->order('create_time desc')->limit(2)->select();
        $this->assign('list3',$list3);
        return $this->fetch();
    }
    /*
     *人才创业列表
     */
    public function lists(){
        $talentModel = new TalentModel();
        $type = input('type');
        $map = array(
            'type' => $type,
            'status' => 1
        );
        $list = $talentModel->where($map)->order('create_time desc')->limit(10)->select();
        $this->assign('list',$list);
        return $this->fetch();
    }
    /*
     *人才创业详情页
     */
    public function detail(){
        $this->anonymous(); //判断是否是游客
        $this->jssdk();
        $uid = session('userId');
        $id = input('id');
        $talentModel = new TalentModel();
        $talentModel::where('id',$id)->setInc('views');     //浏览加一
        $talent = $talentModel->get($id);
        //分享图片及链接及描述
        $info['share_image'] = "http://".$_SERVER['SERVER_NAME']."/home/images/talent.png";
        $info['link'] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'];
        $info['desc'] = str_replace('&nbsp;','',strip_tags($info['content']));
        //点赞
        $likeModel = new Like();
        $like = $likeModel->getLike(5,$id,$uid);
        $talent['is_like'] = $like;
        $this->assign('talent',$talent);
        return $this->fetch();
    }
    
    /**
     * 加载更多
     */
    public function listmore() {
        $len = input('length');
        $type = input('type');
        $map = array(
            'type' => $type,
            'status' => array('eq',1),
        );
        $list = TalentModel::where($map)->order('create_time desc')->limit($len,10)->select();
        foreach ($list as $value) {
            $value['time'] = date("Y-m-d",$value['create_time']);
        }
        if($list){
            return $this->success("加载成功",'',$list);
        }else{
            return $this->error("加载失败");
        }
    }
    
}
