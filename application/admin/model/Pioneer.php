<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/4/24
 * Time: 15:42
 */

namespace app\admin\model;


class Pioneer extends Base {
    protected $insert = [
        'create_time' => NOW_TIME,
        'status' => 1,
        'views' => 0,
        'likes' => 0,
        'comments' => 0,
    ];
    
}