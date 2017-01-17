<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/10/9
 * Time: 16:09
 */

namespace app\admin\validate;


use think\Validate;

class Notice extends Validate {
    protected $rule = [
        'front_cover' => 'require',
        'carousel_images' => 'require',
        'title' => 'require|max:150',
        'description' => 'require',
        'content' => 'require',
        'sponsor' => 'require',
        'telephone' => 'require',
        'cost' => 'require|number',
        'start_time' => 'require',
        'end_time' => 'require',
        'address' => 'require',
        'meet_time' => 'require',
        'meet_home' => 'require',
        'publisher' => 'require|max:15',
    ];

    protected $message = [
        'title.require' => '名称不能为空',
        'title.max' => '名称长度不能超过50个字',
        'front_cover.require' => '封面图片不能为空',
        'carousel_images.require' => '轮播图片不能为空',
        'description.require' => '简介不能为空',
        'content.require' => '内容不能为空',
        'sponsor.require' => '主办方不能为空',
        'cost.require' => '花费不能为空',
        'cost.number' => '花费只能填写数字',
        'start_time.require' => '活动开始时间不能为空',
        'end_time.require' => '活动结束时间不能为空',
        'telephone.require' => '联系电话不能为空',
        'address.require' => '地址不能为空',
        'publisher.require' => '发布人不能为空',
        'meet_time.require' => '时间不能为空',
        'meet_home.require' => '地点不能为空',
        'publisher.max' => '发布人长度不能超过5个字',
    ];

    protected $scene = [
        'act' => ['front_cover','title','description','content','sponsor','telephone','cost','start_time','end_time','address','publisher'],
        'other' => ['front_cover','title','content','publisher','meet_time','meet_home'],
    ];

}