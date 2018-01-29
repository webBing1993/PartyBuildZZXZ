<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/4/24
 * Time: 16:15
 */

namespace app\admin\validate;


use think\Validate;

class Mediation extends Validate {
    protected $rule = [
        'images'  =>  'require',
        'proposer' =>  'require',
        'mobile' =>  'require',
        'parties' =>  'require',
        'title' =>  'require',
        'content'  =>  'require',
        'publisher'  =>  'require',
    ];

    protected $message = [
        'images.require'  =>  '请添加纠纷图片！',
        'proposer.require' =>  '请添加申请人姓名！',
        'mobile.require' =>  '请添加申请人手机号！',
        'parties.require' =>  '请添加当事人姓名！',
        'title.require' =>  '请添加事件名称！',
        'content.require'  =>  '请填写内容！',
        'publisher.require'  =>  '请填写发布人名称！',
    ];
}