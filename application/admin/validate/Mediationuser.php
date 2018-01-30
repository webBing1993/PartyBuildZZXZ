<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/4/24
 * Time: 16:15
 */

namespace app\admin\validate;


use think\Validate;

class Mediationuser extends Validate {
    protected $rule = [
        'front_cover'  =>  'require',
        'name' =>  'require',
        'description' =>  'require',
        'birthday' =>  'require',
       /* 'politics_status' =>  'require',*/
        'education'  =>  'require',
        'content'  =>  'require',
    ];

    protected $message = [
        'front_cover.require'  =>  '请添加封面图片！',
        'name.require' =>  '请添加姓名！',
        'description.require' =>  '请添加简介！',
        'birthday.require' =>  '请添加出生年月！',
       /* 'politics_status.require' =>  '请添加政治面貌！',*/
        'education.require'  =>  '请填写学历！',
        'content.require'  =>  '请填写内容！',
    ];
}