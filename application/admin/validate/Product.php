<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/2/28
 * Time: 10:17
 */
namespace  app\admin\validate;
use think\Validate;
class Product extends Validate{
    protected $rule = [
        'title' => 'require|max:150',
        'front_cover' => 'require',
        'shop_id' =>'require',
        'content' => 'require',
        'price' => 'require',
        'num' => 'require',
    ];

    protected $message = [
        'title.require' => '产品名称不能为空',
        'title.max' => '产品名称长度不能超过50个字',
        'front_cover.require' => '产品图片不能为空',
        'shop_id.require' => '所属店铺不能为空',
        'content.require' => '产品参数不能为空',
        'price.require' => '价格积分不能为空',
        'num.require' => '价格数量不能为空',
    ];
    protected $scene = [
        'act' => ['title','front_cover','shop_id','content','price','num'],
    ];
}