<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/1/16
 * Time: 16:04
 */

namespace app\admin\model;


class Talent extends Base {
    protected $insert = [
        'likes' => 0,
        'views' => 0,
        'comments' => 0,
        'create_time' => NOW_TIME,
        'status' => 1,
    ];

}