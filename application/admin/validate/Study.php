<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/10/8
 * Time: 16:28
 */

namespace app\admin\validate;


use think\Validate;

class Study extends Validate {
    protected $rule = [
        'front_cover'  =>  'require',
        'title' =>  'require',
        /*'description' =>  'require',*/
        'publisher'  =>  'require',
        'content'  =>  'require',
    ];

    protected $message = [
        'front_cover.require'  =>  '请添加封面图片！',
        'title.require' =>  '请添加标题！',
       /* 'description.require' =>  '请添加简介！',*/
        'content.require'  =>  '请填写内容！',
        'publisher.require'  =>  '请填写发布人名称！',
    ];

}