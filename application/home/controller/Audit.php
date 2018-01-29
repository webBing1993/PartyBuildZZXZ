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
use app\home\model\Audit as AuditModel;
use think\Db;

/**
 * Class Audit
 * @package 审核
 */
class Audit extends Base
{
    /**
     * 未审核
     */
    public function reviewlist(){
        $userId = session('userId');
        $tag = WechatUserTag::where(['userid' => $userId, 'tagid' => 1])->find();
        if(!$tag){
            return $this->redirect("verify/null");
        }else{
            $map = [
                'status' => 0,
                'type' => ['neq',4],
            ];
            $list = AuditModel::where($map)->order('id desc')->limit(10)->select();
            foreach ($list as $key => $value){
                $list[$key]['time'] = date("Y-m-d",$value['create_time']);
                $img = Picture::get($value['front_cover']);
                $list[$key]['path'] = $img['path'];
                $list[$key]['pre'] = AuditModel::STATU_ARRAY[$value['type']];
                $list[$key]['url'] = "/home/".$value['url']."/id/".$value['aid'];
                $list[$key]['color'] = AuditModel::STATU_COLOR_ARRAY[$value['status']];
                $list[$key]['status_text'] = AuditModel::STATU_TEXT_ARRAY[$value['status']];
            }
            $this->assign('list',$list);

            $map1 = [
                'status' => 0,
                'type' => 4,
            ];
            $list1 = AuditModel::where($map1)->order('id desc')->limit(10)->select();
            foreach ($list1 as $key => $value){
                $list1[$key]['time'] = date("Y-m-d",$value['create_time']);
                $img = Picture::get($value['front_cover']);
                $list1[$key]['path'] = $img['path'];
                $list1[$key]['pre'] = AuditModel::STATU_ARRAY[$value['type']];
                $list1[$key]['url'] = "/home/".$value['url']."/id/".$value['aid'];
                $list1[$key]['color'] = AuditModel::STATU_COLOR_ARRAY[$value['status']];
                $list1[$key]['status_text'] = AuditModel::STATU_TEXT_ARRAY[$value['status']];
            }
            $this->assign('list1',$list1);
            return $this->fetch();
        }
    }

    /**
     * 已审核
     */
    public function passlist(){
        $userId = session('userId');
        $tag = WechatUserTag::where(['userid' => $userId, 'tagid' => 1])->find();
        if(!$tag){
            return $this->redirect("verify/null");
        }else{
            $map = [
                'status' => ['neq',0],
                'type' => ['neq',4],
            ];
            $list = AuditModel::where($map)->order('id desc')->limit(10)->select();
            foreach ($list as $key => $value){
                $list[$key]['time'] = date("Y-m-d",$value['create_time']);
                $img = Picture::get($value['front_cover']);
                $list[$key]['path'] = $img['path'];
                $list[$key]['pre'] = AuditModel::STATU_ARRAY[$value['type']];
                $list[$key]['url'] = "/home/".$value['url']."/id/".$value['aid'];
                $list[$key]['color'] = AuditModel::STATU_COLOR_ARRAY[$value['status']];
                $list[$key]['status_text'] = AuditModel::STATU_TEXT_ARRAY[$value['status']];
            }
            $this->assign('list',$list);

            $map1 = [
                'status' => ['neq',0],
                'type' => 4,
            ];
            $list1 = AuditModel::where($map1)->order('id desc')->limit(10)->select();
            foreach ($list1 as $key => $value){
                $list1[$key]['time'] = date("Y-m-d",$value['create_time']);
                $img = Picture::get($value['front_cover']);
                $list1[$key]['path'] = $img['path'];
                $list1[$key]['pre'] = AuditModel::STATU_ARRAY[$value['type']];
                $list1[$key]['url'] = "/home/".$value['url']."/id/".$value['aid'];
                $list1[$key]['color'] = AuditModel::STATU_COLOR_ARRAY[$value['status']];
                $list1[$key]['status_text'] = AuditModel::STATU_TEXT_ARRAY[$value['status']];
            }
            $this->assign('list1',$list1);
            return $this->fetch();
        }
    }

    /**
     * 加载更多
     */
    public function indexmore(){
        $len = input('length');
        $type = input('type');
        $status = input('status');
        if($status == 0){
            $map = [
                'status' => 0,
            ];
        }else{
            $map = [
                'status' => ['neq',0],
            ];
        }
        if($type == 1){
            $map['type'] = 4;
        }else{
            $map['type'] = ['neq',4];
        }
        $list = AuditModel::where($map)->order('id desc')->limit($len,6)->select();
        foreach($list as $key => $value){
            $list[$key]['time'] = date("Y-m-d",$value['create_time']);
            $img = Picture::get($value['front_cover']);
            $list[$key]['path'] = $img['path'];
            $list[$key]['pre'] = AuditModel::STATU_ARRAY[$value['type']];
            $list[$key]['url'] = "/home/".$value['url']."/id/".$value['aid'];
            $list[$key]['color'] = AuditModel::STATU_COLOR_ARRAY[$value['status']];
            $list[$key]['status_text'] = AuditModel::STATU_TEXT_ARRAY[$value['status']];
        }
        if($list){
            return $this->success("加载成功",'',$list);
        }else{
            return $this->error("加载失败");
        }
    }
    /**
     * 审核操作
     */
    public function review()
    {
        $id = input('id');
        $status = input('status');
        $info = AuditModel::update(['status' => $status], ['id' => $id]);
        if ($info) {
            if($status == 1){
                $model = AuditModel::get($id);
                $pre = AuditModel::STATU_ARRAY[$model['type']];
                $url = "/home/".$model['url']."/id/".$model['aid'];
//            $this->push($model['title'], $model['front_cover'], $url, $pre);
                Db::name($model['table'])->update(['status' => $status, 'id' => $model['aid']]);
            }

            return $this->success();
        } else {
            return $this->error();
        }
    }
    /**
     * 图文推送公用方法
     */
    public function push($title, $front_cover, $url, $pre){
        $httpUrl = config('http_url');
        $img = Picture::get($front_cover);
        $path = $httpUrl.$img['path'];
        $info = array(
            "title" => $pre,
            "description" => $title,
            "url" => $httpUrl.$url,
            "picurl" => $path,
        );
        //重组成article数据
        $send = array();
        $send['articles'][0] = $info;
        //发送给企业号
        $Wechat = new TPQYWechat(Config::get('news'));
        $newsConf = config('news');
        $message = array(
            "msgtype" => 'news',
            "agentid" => $newsConf['agentid'],
            "news" => $send,
            "safe" => "0"
        );
        if($httpUrl == "http://swim.0571ztnet.com"){
//            if(isset($model['tag'])){
//                $message['totag'] = join('|', json_decode($model['tag'], true));
//            }else{
                $message['touser'] = config('touser');//发送给全体，@all
//            }
        }else{
            $message['touser'] = config('touser');//发送给全体，@all
        }
        return $Wechat->sendMessage($message);
    }
}

