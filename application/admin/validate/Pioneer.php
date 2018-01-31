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
        /*'type' => 'require',*/
        'name' => 'require',
        'birthday' => 'require',
        'position' => 'require',
        'description' => 'require',
        'content' => 'require',
        'publisher' => 'require',
        /*'name' => 'require',*/
    ];

    protected $message = [
        'front_cover.require' => '封面图片不能为空',
        /*'type' => '类型不能为空',*/
        'name.require' =>  '姓名不能为空',
        'birthday.require' =>  '出生年月不能为空',
        'position.require' =>  '职务不能为空',
        'description.require' =>  '口号不能为空',

        'content.require'  =>  '内容不能为空',
        'publisher.require' => '发布者不能为空',
        /*'name' => '姓名不能为空',*/
    ];
   /* protected $scene = [
        'news' => ['front_cover','type','title','content','publisher'],
    ];*/

}