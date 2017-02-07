<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/2/7
 * Time: 16:17
 */

namespace app\admin\model;


class VolunteerOrderReceive extends Base {
    /**
     * 获取微信用户信息
     */
    public function user() {
        return $this->hasOne('WechatUser','userid','userid');
    }

    /**
     * 获取订单信息
     */
    public function order() {
        return $this->hasOne('VolunteerOrder','id','oid');
    }
}