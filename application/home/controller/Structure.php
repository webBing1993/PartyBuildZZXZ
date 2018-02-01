<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2016/11/2
 * Time: 13:21
 */
namespace app\home\controller;
use app\home\model\WechatDepartment;
use app\home\model\WechatDepartmentUser;
use app\home\model\WechatTest;
use app\home\model\WechatUser;
use app\home\model\WechatUserTag;

class Structure extends Base{
    /*
     * 组织架构主页
     */
    public function index(){
        $list = WechatDepartment::where(['parentid' => 1])->order('id')->select();
        $this->assign('list',$list);
        return $this->fetch();
    }
    /*
     * 组织架构详情页
     */
    public function detail(){
        $this ->checkAnonymous();
        $party = input('id');
        $departmentName = WechatDepartment::where(['id' => $party])->order('id')->value('name');
        $modelAll = WechatDepartmentUser::where(['departmentid' => $party])->order('id')->select();
//        $bg_color=["#b1e3fc", "#aeefef", "#ffa351", "#9393f5", "#cf88f7", "#65abfa", "#ebcffb", "#76f4f0", "#ffcf6e", "#ff8ff4"];
        foreach ($modelAll as $k => $model){
            $tag = WechatUserTag::where(['userid' => $model['userid'], 'tagid' => 4])->find();
            if(!$tag){
                unset($modelAll[$k]);
                continue;
            }
            $model['name'] = WechatUser::where(['userid' => $model['userid']])->value('name');
            $model['surname'] = mb_substr($model['name'], 0, 1,'utf-8');
//            $model['color'] = $bg_color[substr($model['mobile'], 7, 1)];
        }
//        var_dump($modelAll);die;
        $this->assign('departmentName',$departmentName);
        $this->assign('modelAll',$modelAll);

        return $this->fetch();
    }
}
