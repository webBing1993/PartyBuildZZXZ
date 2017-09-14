<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/2/3
 * Time: 17:37
 */

namespace app\admin\validate;


use think\Validate;

class RedforumDetail extends Validate {
    protected $rule = [
        'rid' => 'require',
        'title' => 'require',
        'content' => 'require',
        'publisher' => 'require'
    ];

    protected $message = [
        'rid' => '请选择所属主题',
        'title' =>  '标题不能为空',
        'content'  =>  '内容不能为空',
        'publisher' => '发布人不能为空',
    ];
}