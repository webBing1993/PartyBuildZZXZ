<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/2/27
 * Time: 15:20
 */
namespace app\home\model;
use think\Model;
class Product extends Model{
    protected $insert = [
        'create_time' => NOW_TIME
    ];
}