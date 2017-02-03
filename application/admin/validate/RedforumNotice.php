<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/2/3
 * Time: 14:59
 */

namespace app\admin\validate;


use think\Validate;

class RedforumNotice extends Validate {
    protected $rule = [
        'title' => 'require',
        'theme' => 'require',
        'time' => 'require',
        'address' => 'require',
        'content' => 'require',
    ];

    protected $message = [
        'title' =>  '标题不能为空',
        'theme' => '主题不能为空',
        'time' => '时间不能为空',
        'address' => '地址不能为空',
        'content'  =>  '内容不能为空',
    ];
}