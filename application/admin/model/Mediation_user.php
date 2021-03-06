<?php
/**
 * Created by PhpStorm.
 * User: wzf
 * Date: 2018/01/24
 * Time: 13:26
 */

namespace app\admin\model;
use think\Model;

class Mediation_user extends Base {
    public $insert = [
        'views' => 0,
        'collect' => 0,
        'comments' => 0,
        'create_time' => NOW_TIME,
    ];

    //获取后台用户名称
    public function user(){
        return $this->hasOne('Member','id','create_user');
    }

}