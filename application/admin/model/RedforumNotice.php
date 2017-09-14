<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/2/3
 * Time: 14:29
 */

namespace app\admin\model;


use think\Model;

class RedforumNotice extends Base{
    protected $insert = [
        'likes' => 0,
        'views' => 0,
        'comments' => 0,
        'create_time' => NOW_TIME,
        'status' => 1,
    ];
}