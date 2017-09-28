<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/4/28
 * Time: 11:09
 */

namespace app\home\model;


use think\Model;

class Pioneer extends Model {
    /**
     * 获取首页三条轮播数据
     */
    public function getThreeTop() {
        $map = array(
            'status' => 1,
            'type' => 1,
        );
        $order = array("create_time desc");
        $res = $this->where($map)->order($order)->limit(3)->select();
        return $res;
    }

    /**
     * 获取新闻资讯
     */
    public function getNewMessage() {
        $map = array(
            'status' => 1,
            'type' => 1
        );
        $order = array("create_time desc");
        $res = $this->where($map)->order($order)->limit(7)->select();
        return $res;
    }

    /**
     * 获取文件通知
     */
    public function getFileNote() {
        $map = array(
            'status' => 1,
            'type' => 2
        );
        $order = array("create_time desc");
        $res = $this->where($map)->order($order)->limit(7)->select();
        return $res;
    }
    
    /**
     * 获取更多
     */
    public function getMoreList($data) {
        $map = array(
            'status' => 1,
            'type' => $data['type']
        );
        $order = array('create_time desc');
        $list = $this->where($map)->order($order)->limit($data['length'],5)->select();
        foreach($list as $value){
            $img = Picture::get($value['front_cover']);
            $value['src'] = $img['path'];
            $value['time'] = date("Y-m-d",$value['create_time']);
        }

        return $list;
    }
}