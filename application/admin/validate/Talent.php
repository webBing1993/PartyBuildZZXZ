<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/1/16
 * Time: 16:56
 */

namespace app\admin\validate;


use think\Validate;

class Talent extends Validate {
    protected $rule = [
        'type' => 'require',
        'title' => 'require',
        'content' => 'require',
        'publisher' => 'require',

    ];

    protected $message = [
        'type' => '类型不能为空',
        'title' =>  '标题不能为空',
        'content'  =>  '内容不能为空',
        'publisher' => '发布者不能为空',
    ];
}