<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/10/12
 * Time: 10:23
 */

namespace app\admin\model;


use think\Model;

class Audit extends Model {
    const STATUS_NEWS = 1;
    const STATUS_MEET = 2;
    const STATUS_LEARNS = 3;
    const STATUS_MEDIATION = 4;
    const STATU_ARRAY = [
        self::STATUS_NEWS  => '第一聚焦',
        self::STATUS_MEET  => '参会情况',
        self::STATUS_LEARNS  => '十九大专区',
        self::STATUS_MEDIATION  => '调解',
    ];
    protected $insert = [
        'status' => 0,
        'create_time' => NOW_TIME,
    ];
}