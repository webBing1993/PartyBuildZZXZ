<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/1/22
 * Time: 15:23
 */

namespace app\admin\validate;


use think\Validate;

class RedtutorTutor extends Validate {
    protected $rule = [
        'header' => 'require',
        'name' => 'require',
        'title' => 'require',
        'content' => 'require',
    ];

    protected $message = [
        'header' => '请上传专家头像',
        'name' => '姓名不能为空',
        'title' =>  '专家职称不能为空',
        'content'  =>  '专家简介不能为空',
    ];
}