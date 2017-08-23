<?php
/**
 * Created by PhpStorm.
 * User: laowang
 * Date: 2017/1/16
 * Time: 15:10
 */
namespace app\home\model;
use think\Model;

class News extends Model {

    protected $insert = [
        'views' => 0,
        'comments' => 0,
        'create_time' => NOW_TIME,
        'user_type' =>1,
    ];

}