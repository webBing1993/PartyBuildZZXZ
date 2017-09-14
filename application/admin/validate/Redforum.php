<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/2/3
 * Time: 15:30
 */

namespace app\admin\validate;


use think\Validate;

class Redforum extends Validate {
    protected $rule = [
        'nid' => 'require',
        'title' => 'require',
    ];

    protected $message = [
        'nid' => '所属通知不能为空',
        'title' =>  '标题不能为空',
    ];
}