<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/3/3
 * Time: 16:16
 */
namespace app\home\model;
use think\Model;
class Car extends Model{
    protected $insert = [
        'create_time' => NOW_TIME
    ];
    // 获取商品信息
    public function get_p(){
        return $this->hasOne('Product','id','product_id');
    }
}