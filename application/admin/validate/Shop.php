<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/2/27
 * Time: 14:42
 */
namespace app\admin\validate;
use think\Validate;
class Shop extends Validate{
    protected $rule = [
        'title' => 'require|max:150',
        'tel' =>'require',
        'position_x' => 'require',
        'position_y' => 'require',
        'content' => 'require',
        'address' => 'require',
    ];

    protected $message = [
        'title.require' => '商铺名称不能为空',
        'title.max' => '商铺名称长度不能超过50个字',
        'tel.require' => '联系方式不能为空',
        'content.require' => '主营内容不能为空',
        'address.require' => '商铺地址不能为空',
        'position_x.require' => '店铺位置X坐标不能为空',
        'position_y.require' => '店铺位置Y坐标不能为空',
    ];
    protected $scene = [
        'act' => ['title','position_x','position_y','tel','content','address'],
    ];
}