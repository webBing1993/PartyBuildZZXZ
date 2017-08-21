<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/10/12
 * Time: 10:23
 */

namespace app\home\model;


use think\Model;

class Learn extends Model
{
    protected $insert = [
        'views' => 0,
        'collect' => 0,
        'comments' => 0,
        'create_time' => NOW_TIME,
        'user_type' =>1,
    ];

}