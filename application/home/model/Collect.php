<?php
/**
 * Created by PhpStorm.
 * User: sitff
 * Date: 2017/8/17
 * Time: 下午3:29
 */

namespace app\home\model;


use think\Model;

class Collect extends Model {
    protected $insert = [
        'create_time' => NOW_TIME,
        'score' => 0,
        'status' => 0,
    ];

    public function getCollect($type,$aid,$uid) {
        $map = array(
            'type' => $type,
            'aid' => $aid,
            'uid' => $uid,
        );
        $like = Collect::where($map)->find();
        ($like) ? $res = 1 : $res = 0;
        return $res;
    }
}