<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/1/22
 * Time: 15:20
 */

namespace app\admin\model;


use think\Model;

class RedtutorTutor extends Base {
    protected $insert = [
        'create_time' => NOW_TIME,
        'status' => 1,
    ];
}