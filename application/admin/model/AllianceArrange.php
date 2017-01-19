<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/1/17
 * Time: 14:46
 */

namespace app\admin\model;


class AllianceArrange extends Base {
    protected $insert = [
        'likes' => 0,
        'views' => 0,
        'comments' => 0,
        'create_time' => NOW_TIME,
        'status' => 0,
    ];
}