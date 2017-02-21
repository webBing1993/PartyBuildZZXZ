<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/1/17
 * Time: 15:42
 */

namespace app\admin\validate;


use think\Validate;

class Alliance extends Validate {
    protected $rule = [
        'title' => 'require',
        'type' => 'require',
        'theme' => 'require',
        'time' => 'require',
        'address' => 'require',
        'content' => 'require',
        'publisher' => 'require',

    ];

    protected $message = [
        'title' =>  '标题不能为空',
        'type' => '类型不能为空',
        'theme' => '主题不能为空',
        'time' => '时间不能为空',
        'address' => '地址不能为空',
        'content'  =>  '内容不能为空',
        'publisher' => '发布者不能为空',
    ];

    protected $scene = [
        'arrange'  =>  ['title','theme','time','address','content'],
        'show' => ['title','type','content','publisher'],
    ];
}