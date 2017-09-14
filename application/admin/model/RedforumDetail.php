<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/2/3
 * Time: 14:30
 */

namespace app\admin\model;


class RedforumDetail extends Base {
    protected $insert = [
        'likes' => 0,
        'views' => 0,
        'comments' => 0,
        'create_time' => NOW_TIME,
        'status' => 1,
    ];

    /**
     * 获取所属主题
     */
    public function theme() {
        return $this->hasOne('Redforum','id','rid');
    }
}