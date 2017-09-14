<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/1/23
 * Time: 9:59
 */

namespace app\admin\model;

class RedtutorCourse extends Base {
    protected $insert = [
        'likes' => 0,
        'views' => 0,
        'comments' => 0,
        'create_time' => NOW_TIME,
        'status' => 1,
    ];
    
    public function tutor() {
        return $this->hasOne('RedtutorTutor','id','tid');
    }
}