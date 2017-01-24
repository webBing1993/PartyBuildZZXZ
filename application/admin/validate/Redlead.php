<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/1/24
 * Time: 14:16
 */

namespace app\admin\validate;


use think\Validate;

class Redlead extends Validate {
    protected $rule = [
        'type' => 'require',
        'title' => 'require',
        'content' => 'require',
        'publisher' => 'require'
    ];

    protected $message = [
        'type' => '请选择类型',
        'title' =>  '标题不能为空',
        'content'  =>  '内容不能为空',
        'publisher' => '发布人不能为空',
    ];
}