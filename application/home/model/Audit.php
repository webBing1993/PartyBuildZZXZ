<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/10/12
 * Time: 10:23
 */

namespace app\home\model;


use think\Model;

class Audit extends Model {
    const STATUS_NEWS = 1;
    const STATUS_MEET = 2;
    const STATUS_LEARNS = 3;
    const STATUS_MEDIATION = 4;
    const STATU_ARRAY = [
        self::STATUS_NEWS  => '第一聚焦',
        self::STATUS_MEET  => '参会情况',
        self::STATUS_LEARNS  => '十九大专区',
        self::STATUS_MEDIATION  => '调解',
    ];
    const STATU_TEXT_NOPASS = -1;
    const STATU_TEXT_APPLY = 0;
    const STATU_TEXT_PASS = 1;
    const STATU_TEXT_ARRAY = [
        self::STATU_TEXT_NOPASS  => '审核未通过',
        self::STATU_TEXT_APPLY  => '',
        self::STATU_TEXT_PASS  => '审核已通过',
    ];
    const STATU_COLOR_ARRAY = [
        self::STATU_TEXT_NOPASS  => '#F96D5D',
        self::STATU_TEXT_APPLY  => '',
        self::STATU_TEXT_PASS  => '#A8D354',
    ];
    //首页获取已推送的数据
    public function get_list($length,$len){
        $map = array(
            'status' => ['egt',0],
            'recommend' => 1
        );
        $details = $this ->where($map) ->order('create_time desc') ->limit($length,$len) ->select();
        return $details;
    }
}