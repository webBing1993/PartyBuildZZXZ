<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/2/7
 * Time: 14:27
 */

namespace app\admin\validate;


use think\Validate;

class VolunteerOrder extends Validate {
    protected $rule = [
        'title' => 'require',
        'content' => 'require',
        'unit' => 'require',
        'demand_number' => 'require|number',
        'contacts' => 'require',
        'telephone' => 'require',
    ];

    protected $message = [
        'title' =>  '标题不能为空',
        'content'  =>  '内容不能为空',
        'unit' => '所属单位不能为空',
        'demand_number' => '领取人数不能为空,且只能填写数字',
        'contacts' => '联系人不能为空',
        'telephone' => '联系方式不能为空',
    ];
}