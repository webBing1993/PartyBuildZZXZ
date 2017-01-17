<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/10/14
 * Time: 14:51
 */

namespace app\home\model;


use think\Model;

class Like extends Model {
    protected $insert = [
        'create_time' => NOW_TIME,
        'score' => 2,
        'status' => 0,
    ];
}