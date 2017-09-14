<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/10/11
 * Time: 16:36
 */

namespace app\admin\validate;


use think\Validate;

class Meet extends Validate {
    protected $rule = [
        'front_cover' => 'require',
        'title' => 'require',
        'meet_time' => 'require',
        'content' => 'require',
        'publisher' => 'require',
    ];

    protected $message = [
        'front_cover' => '封面图片不能为空',
        'title' =>  '会议主题不能为空',
        'meet_time' => '会议开始时间不能为空',
        'content'  =>  '内容不能为空',
        'publisher' => '发布者不能为空',
    ];

}