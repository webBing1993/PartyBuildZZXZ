<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/1/19
 * Time: 10:49
 */

namespace app\home\model;


use think\Model;

class AllianceShow extends Model {
    public function getShow()
    {
        $map['status'] = 1;
        $order = array('create_time desc','type asc');
        return $this->where($map)->order($order)->limit(7)->select();
    }
}