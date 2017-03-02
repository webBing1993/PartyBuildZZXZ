<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/2/27
 * Time: 14:22
 */
namespace app\admin\model;
use think\Model;
class Shop extends Model{
    protected $insert = [
        'create_time' => NOW_TIME
    ];
    //获取后台用户名称
    public function user(){
        return $this->hasOne('Member','id','create_user');
    }
}