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
use think\Db;

class Structure extends Base{
    /*
     * 组织架构主页
     */
    public function index(){
        $list = WechatDepartment::where(['parentid'=>1, 'id'=>['neq', 2]])->order('id')->select(); // 部门列表
        $arr = $list[28];
        unset($list[28]);
        array_unshift($list, $arr);
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
        $modelAll = Db::table('pb_wechat_department_user')
            ->alias('a')
            ->join('pb_wechat_user b','a.userid = b.userid')
            ->join('pb_wechat_user_tag c','a.userid = c.userid')
            ->field('b.*')
            ->where(['departmentid'=>$party,'tagid'=>4])
            ->order('a.id')
            ->select();
        foreach ($modelAll as $k => $model){
            $modelAll[$k]['surname'] = mb_substr($model['name'], 0, 1,'utf-8');
//            $model['color'] = $bg_color[substr($model['mobile'], 7, 1)];
        }
        $this->assign('departmentName',$departmentName);
        $this->assign('modelAll',$modelAll);

        return $this->fetch();
    }
}
