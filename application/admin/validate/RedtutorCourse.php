<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/1/23
 * Time: 10:21
 */

namespace app\admin\validate;


use think\Validate;

class RedtutorCourse extends Validate {
    protected $rule = [
        'title' => 'require',
        'content' => 'require',
    ];

    protected $message = [
        'title' =>  '标题不能为空',
        'content'  =>  '内容不能为空',
    ];
}