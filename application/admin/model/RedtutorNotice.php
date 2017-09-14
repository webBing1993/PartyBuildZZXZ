<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/1/22
 * Time: 14:01
 */

namespace app\admin\model;


class RedtutorNotice extends Base {
    protected $insert = [
        'likes' => 0,
        'views' => 0,
        'comments' => 0,
        'create_time' => NOW_TIME,
        'status' => 1,
    ];
}