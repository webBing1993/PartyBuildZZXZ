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
        'userid' =>  'require',
        'description' =>  'require',
        'birthday' =>  'require',
        'name' =>  'require',
        'education'  =>  'require',
        'content'  =>  'require',
    ];

    protected $message = [
        'front_cover.require'  =>  '请添加封面图片！',
        'userid.require' =>  '请添加调解员手机号！',
        'description.require' =>  '请添加口号！',
        'birthday.require' =>  '请添加出生年月！',
        'name.require' =>  '此调解员还未导入通讯录！',
        'education.require'  =>  '请选择学历！',
        'content.require'  =>  '请填写工作简历！',
    ];
}