<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/2/7
 * Time: 14:00
 */

namespace app\admin\model;


class VolunteerOrder extends Base {
    protected $insert = [
        'create_time' => NOW_TIME,
        'status' => 1,
    ];
}