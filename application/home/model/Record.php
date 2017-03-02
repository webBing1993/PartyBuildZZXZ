<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/2/28
 * Time: 16:25
 */
namespace app\home\model;
use think\Model;
class Record extends Model{
    protected $insert = [
        'create_time' => NOW_TIME,
        'status' => 0
    ];
    //获取商品详情
    public function product(){
        return $this->hasOne('Product','id','product_id');
    }
    // 获取 商铺名称
    public function shop(){
        return $this->hasOne('Shop','id','shop_id');
    }
    // 获取用户信息
    public function user(){
        return $this->hasOne('WechatUser','userid','userid');
    }
}