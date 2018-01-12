<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/10/12
 * Time: 10:23
 */

namespace app\home\model;


use think\Model;

class News extends Model {
    //首页获取推荐的数据
    public function getDataList($length){
        $map = array(
            'status' => ['egt',0],
//            'end_time' => ['gt',time()],
            'recommend' => 1
        );
        $order = 'create_time desc';
        $limit = "$length,1";
        $list = $this ->where($map) ->order($order) ->limit($limit) ->select();
        if(!empty($list))
        {
            return $list[0] ->data;
        }else{
            return $list;
        }
    }
    /**
     * 获取活动顶部轮播
     */
    public static function getTop() {
        $map = array(
            'status' => array('egt',0),
            'recommend' =>1,
        );
        $order = 'create_time desc';
        $list = self::where($map) ->order($order) ->limit(3) ->select();
        return $list;
    }
    //首页获取已推送的数据
    public function get_list($length,$len){
        $map = array(
            'status' => ['egt',0],
            'recommend' => 1
        );
        $details = $this ->where($map) ->order('create_time desc') ->limit($length,$len) ->select();
        return $details;
    }
}