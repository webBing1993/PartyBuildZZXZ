<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/10/12
 * Time: 10:23
 */

namespace app\home\model;


use think\Model;

class MediationUser extends Model {
    const STATUS_MASSES = 1;
    const STATUS_MEMBER = 2;
    const STATUS_PARTY = 3;
    const STATU_ARRAY = [
        self::STATUS_MASSES  => '群众',
        self::STATUS_MEMBER  => '团员',
        self::STATUS_PARTY  => '党员',
    ];
    /**
     * 获取调解员姓名
     * @param $id
     * @return mixed
     */
    public static function getMediator($id) {
        $mediator = MediationUser::where(['userid' => $id])->value('name');
        return $mediator;
    }
}