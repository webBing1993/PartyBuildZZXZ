<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/2/3
 * Time: 14:29
 */

namespace app\admin\model;


class Redforum extends Base {
    protected $insert = [
        'create_time' => NOW_TIME,
        'status' => 1,
    ];

    /**
     * 获取所属通知
     */
    public function note() {
        return $this->hasOne('RedforumNotice','id','nid');
    }
}