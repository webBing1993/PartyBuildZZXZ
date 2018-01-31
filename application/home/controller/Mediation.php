<?php
/**
 * Created by PhpStorm.
 * User: daire
 * Date: 2017/12/25
 * Time: 14:51
 */

namespace app\home\controller;
use app\home\model\Like;
use app\home\model\WechatUserTag;
use com\wechat\TPQYWechat;
use think\Config;
use think\Db;
use think\Request;
use think\Controller;
use app\home\model\Browse;
use app\home\model\Comment;
use app\admin\model\Picture;
use app\home\model\WechatUser;
use app\home\model\Mediation as MediationModel;
use app\home\model\MediationCase;
use app\home\model\MediationUser;

class Mediation extends Base
{
    public function index(){
        $this->anonymous();
        //数据列表
        $map = [
            'status' => ['egt',0],
        ];
        $list1 = MediationCase::where($map)->limit(5)->order('id desc')->select();
        $this->assign('list1',$list1);

        $list2 = MediationUser::where($map)->order('id desc')->select();
        $this->assign('list2',$list2);


        $userId = session('userId');
        $tag = WechatUserTag::where(['userid' => $userId, 'tagid' => 1])->find();
        if($tag){
            $user_tag = 1;//管理员
        }else{
            $tag = WechatUserTag::where(['userid' => $userId, 'tagid' => 2])->find();
            if($tag){
                $user_tag = 2;//调解员
            }else{
                $user_tag = 3;//申请人
            }
        }
        if($user_tag == 3){//申请人
            $map_to = [
                'userid' => $userId,
            ];
            $list3 = MediationModel::where($map_to)->limit(10)->order('id desc')->select();
            foreach ($list3 as $value) {
                $value['status_text'] = MediationModel::TOTAL_STATU_ARRAY[$value['status']];
                $value['status_color'] = MediationModel::STATU_COLOR_ARRAY[$value['status']];
                if ($value['images'] && $value['images'] != '""') {
                    $value['path'] = get_cover(json_decode($value['images'], true)[0], 'path');
                } else {
                    $value['path'] = '/home/images/lxyz/icon/default.png';
                }
            }

            $this->assign('list3',$list3);
        }elseif($user_tag == 2){//调解员
            $count_check = MediationModel::where(['mediatorid' => $userId, 'status' => MediationModel::STATUS_CHECK])->count();
            $count_confirm = MediationModel::where(['mediatorid' => $userId, 'status' => MediationModel::STATUS_CONFIRM])->count();
            $count_done = MediationModel::where(['mediatorid' => $userId, 'status' => ['egt',MediationModel::STATUS_FEEDBACK]])->count();

            $this->assign('count_check',$count_check);
            $this->assign('count_confirm',$count_confirm);
            $this->assign('count_done',$count_done);
        }else{//管理员
            $count_noapprove = MediationModel::where(['status' => MediationModel::STATUS_NOAPPROVE])->count();
            $count_check = MediationModel::where(['status' => MediationModel::STATUS_CHECK])->count();
            $count_confirm = MediationModel::where(['status' => MediationModel::STATUS_CONFIRM])->count();
            $count_done = MediationModel::where(['status' => ['egt',MediationModel::STATUS_FEEDBACK]])->count();

            $this->assign('count_noapprove',$count_noapprove);
            $this->assign('count_check',$count_check);
            $this->assign('count_confirm',$count_confirm);
            $this->assign('count_done',$count_done);
        }
        $this->assign('user_tag',$user_tag);
        return $this->fetch();
    }
    /**
     * 网上申请子页面列表
     */
    public function listdetails(){
        $userId = session('userId');
        $type = input('type');
        $tag = WechatUserTag::where(['userid' => $userId, 'tagid' => 1])->find();
        $map = [];
        if ($type == 1) {
            $map = [
                'status' => MediationModel::STATUS_NOAPPROVE,
            ];
        } elseif($type == 2) {
            $map = [
                'status' => MediationModel::STATUS_CHECK,
            ];
        } elseif($type == 3) {
            $map = [
                'status' => MediationModel::STATUS_CONFIRM,
            ];
        } elseif($type == 4) {
            $map = [
                'status' => ['egt',MediationModel::STATUS_FEEDBACK],
            ];
        }
        if (!$tag) {//调解员
            $map['mediatorid'] = $userId;
        }
        $list = MediationModel::where($map)->limit(10)->order('id desc')->select();
        foreach ($list as $value) {
            $value['status_text'] = MediationModel::TOTAL_STATU_ARRAY[$value['status']];
            $value['status_color'] = MediationModel::STATU_COLOR_ARRAY[$value['status']];
            if ($value['images'] && $value['images'] != '""') {
                $value['path'] = get_cover(json_decode($value['images'], true)[0], 'path');
            } else {
                $value['path'] = '/home/images/lxyz/icon/default.png';
            }
        }
        $this->assign('type',$type);
        $this->assign('list',$list);
        return $this->fetch();
    }
    /**
     * 案例加载更多
     */
    public function indexmore(){
        $len = input('length');
        $uid = input('uid');
        $map = array(
            'status' => ['egt',0],
        );
        if($uid){
            $map['uid'] = $uid;
        }
        $list = MediationCase::where($map)->order('id desc')->limit($len,6)->select();
        foreach($list as $value){
            $value['time'] = date("Y-m-d",$value['create_time']);
        }
        if($list){
            return $this->success("加载成功",'',$list);
        }else{
            return $this->error("加载失败");
        }
    }

    /**
     * 案例详情页
     */
    public function newdetail(){
        $this->anonymous();        //判断是否是游客
        $this->jssdk();

        $userId = session('userId');
        $id = input('id');
        $caseModel = new MediationCase();
        //浏览加一
        $info['views'] = ['exp','`views`+1'];
        $caseModel::where('id',$id)->update($info);
        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = [
                'user_id' => $userId,
                'mediation_case_id' => $id,
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
        $article = $caseModel::get($id);
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
        $like = $likeModel->getLike(9,$id,$userId);
        $article['is_like'] = $like;
        $this->assign('article',$article);
        //获取 评论
        $commentModel = new Comment();
        $comment = $commentModel->getComment(9,$id,$userId);
        $this->assign('comment',$comment);

        return $this->fetch();
    }
    /**
     * 网上申请加载更多
     */
    public function mediationmore(){
        $userId = session('userId');
        $len = input('length');
        $type = input('type');
        $tag = WechatUserTag::where(['userid' => $userId, 'tagid' => 1])->find();
        if($tag){
            $user_tag = 1;//管理员
        }else{
            $tag = WechatUserTag::where(['userid' => $userId, 'tagid' => 2])->find();
            if($tag){
                $user_tag = 2;//调解员
            }else{
                $user_tag = 3;//申请人
            }
        }
        if ($user_tag == 3) {
            $map = array(
                'userid' => $userId,
            );
        }else{
            if ($type == 1) {
                $map = [
                    'status' => MediationModel::STATUS_NOAPPROVE,
                ];
            } elseif($type == 2) {
                $map = [
                    'status' => MediationModel::STATUS_CHECK,
                ];
            } elseif($type == 3) {
                $map = [
                    'status' => MediationModel::STATUS_CONFIRM,
                ];
            } elseif($type == 4) {
                $map = [
                    'status' => ['egt',MediationModel::STATUS_FEEDBACK],
                ];
            }
            if ($user_tag == 2) {//调解员
                $map['mediatorid'] = $userId;
            }
        }
        $list = MediationModel::where($map)->limit($len,6)->order('id desc')->select();
        foreach ($list as $value) {
            $value['time'] = date("Y-m-d",$value['create_time']);
            $value['status_text'] = MediationModel::TOTAL_STATU_ARRAY[$value['status']];
            $value['status_color'] = MediationModel::STATU_COLOR_ARRAY[$value['status']];
            if ($value['images'] && $value['images'] != '""') {
                $value['path'] = get_cover(json_decode($value['images'], true)[0], 'path');
            } else {
                $value['path'] = '/home/images/lxyz/icon/default.png';
            }
        }
        if($list){
            return $this->success("加载成功",'',$list);
        }else{
            return $this->error("加载失败");
        }
    }

    /**
     * 调解员介绍页
     */
    public function medcase(){
        $map = [
            'userid' => input('id'),
        ];
        $model = MediationUser::where($map)->find();
        $model['politics'] = MediationUser::STATU_ARRAY[$model['politics_status']];
        $this->assign('model',$model);

        $map = [
            'uid' => input('id'),
            'status' => ['egt',0],
        ];
        $list = MediationCase::where($map)->limit(5)->order('id desc')->select();
        $this->assign('uid',input('id'));
        $this->assign('list',$list);
        return $this->fetch();
    }
    /**
     * 调解详情页
     */
    public function yhdetails(){
        $userId = session('userId');
        $id = input('id');
        $tag = WechatUserTag::where(['userid' => $userId, 'tagid' => 1])->find();
        if($tag){
            $user_tag = 1;//管理员
        }else{
            $tag = WechatUserTag::where(['userid' => $userId, 'tagid' => 2])->find();
            if($tag){
                $user_tag = 2;//调解员
            }else{
                $user_tag = 3;//申请人
            }
        }
        $model = MediationModel::get($id);
//        var_dump($model);die;
        $response = [];

        if($model['status'] == MediationModel::STATUS_NOAPPROVE){
            $response[1] = [
                'd_time' => date('m-d', $model['create_time']),
                's_time' => date('H:i', $model['create_time']),
                'num' => 1,
                'check' => 1,
                'status_text' => MediationModel::STATU_ARRAY[MediationModel::STATUS_COMMIT],
            ];
            $response[2] = [
                'd_time' => date('m-d', $model['check_time']),
                's_time' => date('H:i', $model['check_time']),
                'num' => 2,
                'check' => 1,
                'status_text' => MediationModel::STATU_ARRAY[MediationModel::STATUS_NOAPPROVE],
            ];

        }else{
            for($i=1; $i<=5; $i++){
                $response[$i]['d_time'] = null;
                $response[$i]['s_time'] = null;
                $time_field = MediationModel::STATU_TOTIME_ARRAY[$i];
                if (!empty($model[$time_field])) {
                    $response[$i]['d_time'] = date('m-d', $model[$time_field]);
                    $response[$i]['s_time'] = date('H:i', $model[$time_field]);
                }
                $response[$i]['num'] = $i;
                if ($model['status'] >= $i) {
                    $response[$i]['check'] = 1;
                    $response[$i]['status_text'] = MediationModel::STATU_ARRAY[$i];
                }else{
                    $response[$i]['check'] = 0;
                    $response[$i]['status_text'] = MediationModel::NEXT_STATU_ARRAY[$i-1];
                }

            }
        }
        $title = MediationModel::TOTAL_STATU_ARRAY[$model['status']];
        $images = json_decode($model['images']);
        if($images){
            foreach ($images as $key => $value){
                $images[$key] = get_cover($value, 'path');
            }
        }
        $front_cover = MediationUser::where(['userid' => $model['mediatorid']])->value('front_cover');
        $front_cover = get_cover($front_cover, 'path');

        $map1 = [
            'status' => ['egt',0],
        ];
        $list = MediationUser::where($map1)->order('id desc')->select();

        $this->assign('list',$list);
        $this->assign('response',$response);
        $this->assign('images',$images);
        $this->assign('model',$model);
        $this->assign('mediator_path',$front_cover);
        $this->assign('title',$title);
        $this->assign('id',$id);
        $this->assign('user_tag',$user_tag);

        return $this->fetch();
    }

    /**
     * 调解申请页面
     */
    public function application(){
        $this->checkAnonymous();
        $userId = session('userId');
        if(IS_POST) {
            $data = input('post.');
            $data['userid'] = $userId;
            $data['mediator'] = MediationUser::getMediator($data['mediatorid']);
            $data['images'] = json_encode($data['images']);
            $opinionModel = new MediationModel();
            $model = $opinionModel->create($data);
            if ($model) {
//                 $this->push_review("审核通知", "【调解申请】未审核", "请尽快审核", 1);
                $auditmodel = new \app\admin\model\Audit();
                $audit['type'] = 4;
                $audit['table'] = 'mediation';
                $audit['url'] = 'mediation/yhdetails';
                $audit['aid'] = $model->id;
                $audit['title'] = $data['title'];
                $audit['publisher'] = '';
                if ($data['images'] && $data['images'] != '""') {
                    $audit['front_cover'] = json_decode($data['images'], true)[0];
                } else {
                    $audit['front_cover'] = '';
                }
                $auditmodel->save($audit);
                return $this->success("提交成功");
            } else {
                return $this->error("提交失败");
            }
        }else{
            $mediatorid = input('id');
            if($mediatorid){
                $mediatorModel = MediationUser::where(['userid' => $mediatorid])->find();
                $mediatorModel['front_cover'] = Picture::where('id',$mediatorModel['front_cover'])->value('path');
                $this->assign('mediatorid',$mediatorid);
                $this->assign('mediator',$mediatorModel['name']);
                $this->assign('mediator_path',$mediatorModel['front_cover']);
            }else{
                $this->assign('mediatorid','');
                $this->assign('mediator','');
                $this->assign('mediator_path','');
            }

            $model = WechatUser::where(['userid' => $userId])->find();

            $map = [
                'status' => ['egt',0],
            ];
            $list = MediationUser::where($map)->order('id desc')->select();
            $this->assign('model',$model);
            $this->assign('list',$list);

            return $this->fetch();
        }
    }

    /**
     * 管理员审核不通过页面
     */
    public function noapprove(){
        $id = input('id');
        $this->assign('id',$id);
        return $this->fetch();
    }
    /**
     * 调解员列表
     */
    public function applicationdetail(){
        $map = [
            'status' => ['egt',0],
        ];
        $list = MediationUser::where($map)->order('id desc')->select();
        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 管理员审核
     */
    public function check(){
        $data = input('post.');
        $status = input('status');
        if($status){//审核通过
            $data['status'] = MediationModel::STATUS_CHECK;
            if($data['mediatorid']){
                $data['mediator'] = MediationUser::getMediator($data['mediatorid']);
            }
        }else{//审核不通过
            $data['status'] = MediationModel::STATUS_NOAPPROVE;
        }
        $data['check_time'] = time();
        $model = MediationModel::update($data,['id'=>input('id')]);
        if($model) {
            if($status){//审核通过
                $audit_status = 1;
            }else{//审核不通过
                $audit_status = -1;
            }
            \app\admin\model\Audit::update(['status' => $audit_status], ['aid' => input('id'), 'type' => 4]);
            if($status){//审核通过
                $userid = MediationModel::getUserid(input('id'));
//                $this->push_review("审核通过通知", "【调解申请】审核已通过", "点击查看", 0, $userid);
                $mediatorid = MediationModel::getMediatorid(input('id'));
//                $this->push_review("调解确认通知", "【调解申请】未确认", "请尽快确认", 0, $mediatorid);
            }else{//审核不通过
                $userid = MediationModel::getUserid(input('id'));
//                $this->push_review("审核不通过通知", "【调解申请】审核不通过", "点击查看", 0, $userid);
            }
            return $this->success("提交成功");
        }else{
            return $this->error("提交失败");
        }
    }

    /**
     * 调解员确认
     */
    public function confirm(){
        $data['status'] = MediationModel::STATUS_CONFIRM;
        $data['confirm_time'] = time();
        $model = MediationModel::update($data,['id'=>input('id')]);
        if($model) {
            $userid = MediationModel::getUserid(input('id'));
//                $this->push_review("调解员已确认通知", "【调解申请】调解员已确认", "点击查看", 0, $userid);
            return $this->success("提交成功");
        }else{
            return $this->error("提交失败");
        }
    }

    /**
     * 调解员处理反馈
     */
    public function opinion(){
        if(IS_POST) {
            $data = input('post.');
            $data['status'] = MediationModel::STATUS_FEEDBACK;
            $data['feedback_time'] = time();
            $model = MediationModel::update($data,['id'=>input('id')]);
            if($model) {
                $userid = MediationModel::getUserid(input('id'));
//                $this->push_review("评价通知", "【调解申请】未评价", "快去评价吧", 0, $userid);
                return $this->success("提交成功");
            }else{
                return $this->error("提交失败");
            }
        }else{
            $id = input('id');
            $this->assign('id',$id);
            return $this->fetch();
        }
    }

    /**
     * 申请人情况反馈
     */
    public function evaluate(){
        if(IS_POST) {
            $data = input('post.');
            $data['status'] = MediationModel::STATUS_ESTIMATE;
            $data['estimate_time'] = time();
            $model = MediationModel::update($data,['id'=>input('id')]);
            if($model) {
                $mediatorid = MediationModel::getMediatorid(input('id'));
//                $this->push_review("评价查看通知", "【调解申请】评价未查看", "快去看看吧", 0, $mediatorid);
                return $this->success("提交成功");
            }else{
                return $this->error("提交失败");
            }
        }else{
            $id = input('id');
            $title = MediationModel::where(['id' => $id])->value("title");
            $this->assign('title',$title);
            $this->assign('id',$id);
            return $this->fetch();
        }
    }

    /**
     * 文本卡片推送公用方法
     */
    public function push_review($title, $pre, $end, $tag, $user=null){
        $httpUrl = config('http_url');
        $send = array(
            "title" => $title,
            "description" => "您有一条".$pre."<br>".$end,
            "url" => $httpUrl."/home/approved/reviewlist",
        );
        //发送给企业号
        $Wechat = new TPQYWechat(Config::get('review'));
        $newsConf = config('review');
        $message = array(
            "msgtype" => 'textcard',
            "agentid" => $newsConf['agentid'],
            "textcard" => $send,
            "safe" => "0"
        );
        if($httpUrl == "http://dzgcpb.0571ztnet.com"){
            $message['totag'] = $tag;
        }else{
            if($tag){
                $message['totag'] = $tag;
            }else{
                if($user){
                    $message['touser'] = $user;
                }else{
                    $message['touser'] = config('touser');
                }

            }
        }
        return $Wechat->sendMessage($message);
    }


}