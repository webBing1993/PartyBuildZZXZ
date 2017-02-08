<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/2/8
 * Time: 13:52
 */

namespace app\admin\model;


class VolunteerRecruit extends Base {
    protected $insert = [
        'create_time' => NOW_TIME,
        'status' => 1,
    ];
}