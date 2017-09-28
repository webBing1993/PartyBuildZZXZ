<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/4/24
 * Time: 16:15
 */

namespace app\admin\validate;


use think\Validate;

class Pioneer extends Validate {
    protected $rule = [
        'front_cover' => 'require',
        'type' => 'require',
        'title' => 'require',
        'content' => 'require',
        'publisher' => 'require',
        'name' => 'require',
    ];

    protected $message = [
        'front_cover' => '封面图片不能为空',
        'type' => '类型不能为空',
        'title' =>  '标题不能为空',
        'content'  =>  '内容不能为空',
        'publisher' => '发布者不能为空',
        'name' => '姓名不能为空',
    ];
    protected $scene = [
        'news' => ['front_cover','type','title','content','publisher'],
    ];

}