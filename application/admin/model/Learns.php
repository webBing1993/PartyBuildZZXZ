<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/10/12
 * Time: 10:23
 */

namespace app\admin\model;


use think\Model;

class Learns extends Model {
    protected $insert = [
        'views' => 0,
        'comments' => 0,
        'likes' => 0,
        'collect' => 0,
        'create_time' => NOW_TIME,
    ];

    //获取后台用户名称
    public function user(){
        return $this->hasOne('Member','id','create_user');
    }
}