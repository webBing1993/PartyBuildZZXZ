<?php
/**
 * Created by PhpStorm.
 * User: wzf
 * Date: 2018/01/29
 * Time: 13:44
 */

namespace app\admin\model;
use think\Model;

class Study extends Base {
    public $insert = [
        'views' => 0,
        'likes' => 0,
        'comments' => 0,
        'create_time' => NOW_TIME,
    ];

    //获取后台用户名称
    public function user(){
        return $this->hasOne('Member','id','create_user');
    }
//推送数据
    /*public function newsPush($info,$idArr=[]) {
        if(empty($idArr)) {
            $newsArr = $this ->where($info)->select();
        } elseif(!empty($idArr) && $idArr[0] == 4) {
            $newsArr = $this ->where($info) ->where(['id'=>['neq',$idArr[1]]])->select();
        } else {
            $newsArr = $this ->where($info)->select();
        }

        foreach($newsArr as  $value){
            switch ($value['type']){
                case 1:
                    $value['title'] = '【重点工作-谈心谈话】'.$value['title'];
                    break;
                case 2:
                    $value['title'] = '【重点工作-党建文章】'.$value['title'];
                    break;
                case 3:
                    $value['title'] = '【重点工作-党员评议】'.$value['title'];
                    break;
                case 4:
                    $value['title'] = '【重点工作-党风廉政】'.$value['title'];
                    break;
                case 0:
                    $value['title'] = '【重点工作-星级创建】'.$value['title'];
                    break;
            }
            $value['id'] = '4-'.$value->id;
        }
        return $newsArr;
    }*/
}