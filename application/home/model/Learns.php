<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/10/12
 * Time: 10:23
 */

namespace app\home\model;


use think\Model;

class Learns extends Model {
    /**
     * 首页获取推荐的数据
     * @param $length
     * @param string $push 推送数据获取
     */
    public function getDataList($length,$push="0"){
        $map = array(
            'status' => ['egt',0],
            'recommend' => 1,
            'push' => ['egt',$push]
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
    //首页获取已推送的数据
    public function get_list($length,$len){
        $map = array(
            'status' => ['gt',0],
            'recommend' => 1
        );
        $details = $this ->where($map) ->order('create_time desc') ->limit($length,$len) ->field("'learns' as genre, id, title, create_time, front_cover, publisher, status, recommend, views, comments, likes")->select();
        return $details;
    }
}