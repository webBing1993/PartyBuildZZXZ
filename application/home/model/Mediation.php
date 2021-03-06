<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/10/12
 * Time: 10:23
 */

namespace app\home\model;


use think\Model;

class Mediation extends Model {
    public $insert = [
        'status' => 1,
        'create_time' => NOW_TIME,
    ];

    const STATUS_NOAPPROVE = -1;
    const STATUS_COMMIT = 1;
    const STATUS_CHECK = 2;
    const STATUS_CONFIRM = 3;
    const STATUS_FEEDBACK = 4;
    const STATUS_ESTIMATE = 5;
    const STATU_ARRAY = [
        self::STATUS_NOAPPROVE  => '审核不通过',
        self::STATUS_COMMIT  => '已提交',
        self::STATUS_CHECK  => '已审核',
        self::STATUS_CONFIRM  => '调解员已确认',
        self::STATUS_FEEDBACK  => '纠纷已处理',
        self::STATUS_ESTIMATE  => '已评价',
    ];
    const NEXT_STATUS_NOAPPROVE = -1;
    const NEXT_STATUS_COMMIT = 1;
    const NEXT_STATUS_CHECK = 2;
    const NEXT_STATUS_CONFIRM = 3;
    const NEXT_STATUS_FEEDBACK = 4;
    const NEXT_STATUS_ESTIMATE = 5;
    const NEXT_STATU_ARRAY = [
        self::NEXT_STATUS_NOAPPROVE  => '审核不通过',
        self::NEXT_STATUS_COMMIT  => '待审核',
        self::NEXT_STATUS_CHECK  => '调解员待确认',
        self::NEXT_STATUS_CONFIRM  => '纠纷待处理',
        self::NEXT_STATUS_FEEDBACK  => '待评价',
        self::NEXT_STATUS_ESTIMATE  => '',
    ];
    const TOTAL_STATUS_NOAPPROVE = -1;
    const TOTAL_STATUS_COMMIT = 1;
    const TOTAL_STATUS_CHECK = 2;
    const TOTAL_STATUS_CONFIRM = 3;
    const TOTAL_STATUS_FEEDBACK = 4;
    const TOTAL_STATUS_ESTIMATE = 5;
    const TOTAL_STATU_ARRAY = [
        self::TOTAL_STATUS_NOAPPROVE  => '审核不通过',
        self::TOTAL_STATUS_COMMIT  => '待审核',
        self::TOTAL_STATUS_CHECK  => '调解员待确认',
        self::TOTAL_STATUS_CONFIRM  => '纠纷待处理',
        self::TOTAL_STATUS_FEEDBACK  => '待评价',
        self::TOTAL_STATUS_ESTIMATE  => '已评价',
    ];
    const STATUS_TOTIME_NOAPPROVE = -1;
    const STATUS_TOTIME_COMMIT = 1;
    const STATUS_TOTIME_CHECK = 2;
    const STATUS_TOTIME_CONFIRM = 3;
    const STATUS_TOTIME_FEEDBACK = 4;
    const STATUS_TOTIME_ESTIMATE = 5;
    const STATU_TOTIME_ARRAY = [
        self::STATUS_TOTIME_NOAPPROVE  => 'check_time',
        self::STATUS_TOTIME_COMMIT  => 'create_time',
        self::STATUS_TOTIME_CHECK  => 'check_time',
        self::STATUS_TOTIME_CONFIRM  => 'confirm_time',
        self::STATUS_TOTIME_FEEDBACK  => 'feedback_time',
        self::STATUS_TOTIME_ESTIMATE  => 'estimate_time',
    ];
    const STATUS_COLOR_NOAPPROVE = -1;
    const STATUS_COLOR_COMMIT = 1;
    const STATUS_COLOR_CHECK = 2;
    const STATUS_COLOR_CONFIRM = 3;
    const STATUS_COLOR_FEEDBACK = 4;
    const STATUS_COLOR_ESTIMATE = 5;
    const STATU_COLOR_ARRAY = [
        self::STATUS_COLOR_NOAPPROVE  => 'not',
        self::STATUS_COLOR_COMMIT  => 'wait',
        self::STATUS_COLOR_CHECK  => 'verify',
        self::STATUS_COLOR_CONFIRM  => 'message',
        self::STATUS_COLOR_FEEDBACK  => 'appraise',
        self::STATUS_COLOR_ESTIMATE  => 'complete',
    ];

    /**
     * 获取调解员id
     * @param $id
     * @return mixed
     */
    public static function getMediatorid($id) {
        $model = Mediation::get($id);
        return $model['mediatorid'];
    }
    /**
     * 获取申请人id
     * @param $id
     * @return mixed
     */
    public static function getUserid($id) {
        $model = Mediation::get($id);
        return $model['userid'];
    }
}