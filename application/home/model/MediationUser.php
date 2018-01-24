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