<?php
/**
 * Created by PhpStorm.
 * User: wzf
 * Date: 2018/01/25
 * Time: 13:28
 */

namespace app\admin\validate;


use think\Validate;

class News extends Validate {
    protected $rule = [
        'front_cover'  =>  'require',
        'title' =>  'require',
        'content'  =>  'require',
        'publisher'  =>  'require',
    ];

    protected $message = [
        'front_cover.require'  =>  '请添加封面图片！',
        'title.require' =>  '请添加标题！',
        'content.require'  =>  '请填写内容！',
        'publisher.require'  =>  '请填写发布人名称！',
    ];

}