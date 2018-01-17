<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/10/14
 * Time: 17:23
 */

namespace app\home\controller;

use app\admin\model\WechatDepartment;
use app\home\model\WechatDepartmentUser;
use app\home\model\WechatUser;
use com\wechat\TPQYWechat;
use think\Config;
use think\Controller;
/**
 * Class Statistics
 * @package 统计模块
 */
class Statistics extends Base {

    public function party(){
        return $this->fetch();
    }
    /**
     * 列表统计页
     */
    public function form(){
        //判断是否推送贺卡
//        $this->push_card();
        //判断是否推送建党节推送
//        $this->party_day();
        /*党组织总数*/
        $fa = WechatDepartment::where('parentid',1)->select(); //获取父级部门
        $dpsum = 0; //党组织总数
        foreach ($fa as $key => $value) {
            $dp = WechatDepartment::where('parentid',$value['id'])->count();  //循环输出子部门取数
            $dpsum += $dp;
        }

        //社会组织 id:2
        $organizesum = WechatDepartment::where('parentid',2)->count();   //部门数
        $organizeuser1 = 0; //三级部门人数
        $organizeuser2 = 0; //四级部门人数
        $organizeuser3 = WechatDepartmentUser::where('departmentid',2)->count();    //二级部门用户人数
        $organize1 = WechatDepartment::where('parentid',2)->select();   //查找三级部门
        foreach ($organize1 as $key => $value) {
            $ouser1 = WechatDepartmentUser::where('departmentid',$value['id'])->count(); //统计用户
            $organizeuser1 += $ouser1;
            $organize2 = WechatDepartment::where('parentid',$value['id'])->select();    //查找四级部门
            if($organize2) {
                foreach ($organize2 as $k => $val) {
                    $ouser2 = WechatDepartmentUser::where('departmentid',$value['id'])->count();    //统计人数
                    $organizeuser2 += $ouser2;
                }
            }
        }
        $organizenum = $organizeuser1 + $organizeuser2 + $organizeuser3; //总人数

        //企业工作委员会 id:3
        $companysum = WechatDepartment::where('parentid',3)->count();
        $companyuser1 = 0;
        $companyuser2 = 0;
        $companyuser3 = WechatDepartmentUser::where('departmentid',3)->count();    //二级部门用户人数
        $company1 = WechatDepartment::where('parentid',3)->select();
        foreach ($company1 as $key => $value) {
            $cuser1 = WechatDepartmentUser::where('departmentid',$value['id'])->count();
            $companyuser1 += $cuser1;

            $company2 = WechatDepartment::where('parentid',$value['id'])->select();    //查找四级部门
            if($company2) {
                foreach ($company2 as $k => $val) {
                    $cuser2 = WechatDepartmentUser::where('departmentid',$value['id'])->count();    //统计人数
                    $companyuser2 += $cuser2;
                }
            }
        }
        $companynum = $companyuser1 + $companyuser2 + $companyuser3;

        //村社区 id:4
        $villagesum = WechatDepartment::where('parentid',4)->count();
        $villageuser1 = 0;
        $villageuser2 = 0;
        $villageuser3 = WechatDepartmentUser::where('departmentid',4)->count();    //二级部门用户人数
        $village1 = WechatDepartment::where('parentid',4)->select();
        foreach ($village1 as $key => $value) {
            $vuser1 = WechatDepartmentUser::where('departmentid',$value['id'])->count();
            $villageuser1 += $vuser1;
            $village2 = WechatDepartment::where('parentid',$value['id'])->select();
            if($village2) {
                foreach ($village2 as $k => $val) {
                    $vuser2 = WechatDepartmentUser::where('departmentid',$value['id'])->count();
                    $villageuser2 += $vuser2;
                }
            }
        }
        $villagenum = $villageuser1 + $villageuser2 + $villageuser3;

        //机关企事业 id:10
        $partysum = WechatDepartment::where('parentid',10)->count();
        $partyuser1 = 0;
        $partyuser2 = 0;
        $partyuser3 = WechatDepartmentUser::where('departmentid',10)->count();    //二级部门用户人数
        $party1 = WechatDepartment::where('parentid',10)->select();
        foreach ($party1 as $key => $value) {
            $puser1 = WechatDepartmentUser::where('departmentid',$value['id'])->count();
            $partyuser1 += $puser1;
            $party2 = WechatDepartment::where('parentid',$value['id'])->select();
            if($party2) {
                foreach ($party2 as $k => $val) {
                    $puser2 = WechatDepartmentUser::where('departmentid',$value['id'])->count();
                    $partyuser2 += $puser2;
                }
            }
        }
        $partynum = $partyuser1 + $partyuser2 + $partyuser3;
       
        /*党员信息*/
        $map['department'] = array('neq','[9]');
        $userNum = WechatUser::where($map)->count();     //总人数
        $concernNum = WechatUser::where('status',1)->where($map)->count(); //关注人数

        $avg = WechatUser::avg('age');  //平均年龄
        $age = substr($avg,0,2);
        $age1 = WechatUser::where('age','lt',20)->where($map)->count();  //20一下百分比
        $agepercent1 = round(($age1/$userNum)*100).'%';
        $age2 = WechatUser::where('age','between',[20,30])->where($map)->count();    //20-30
        $agepercent2 = round(($age2/$userNum)*100).'%';
        $age3 = WechatUser::where('age','between',[30,40])->where($map)->count();    //30-40
        $agepercent3 = round(($age3/$userNum)*100).'%';
        $age4 = WechatUser::where('age','between',[40,50])->where($map)->count();    //40-50
        $agepercent4 = round(($age4/$userNum)*100).'%';
        $age5 = WechatUser::where('age','gt',50)->where($map)->count();    //50以上
        $agepercent5 = round(($age5/$userNum)*100).'%';

        $m = WechatUser::where('gender','eq',1)->where($map)->count(); //男性人数
        $male = round(($m/$userNum)*100).'%';
        $w = WechatUser::where('gender','eq',2)->where($map)->count(); //女性人数
        $female = round(($w/$userNum)*100).'%';

        $edu1 = WechatUser::where('education','eq',"初中以下")->where($map)->count();    //统计学历人数
        $edu2 = WechatUser::where('education','eq',"初中")->where($map)->count();
        $edu3 = WechatUser::where('education','eq',"高中")->where($map)->count();
        $edu4 = WechatUser::where('education','eq',"中专")->where($map)->count();
        $edu5 = WechatUser::where('education','eq',"大专")->where($map)->count();
        $edu6 = WechatUser::where('education','eq',"本科")->where($map)->count();
        $edu7 = WechatUser::where('education','eq',"硕士")->where($map)->count();
        $edu8 = WechatUser::where('education','eq',"硕士以上")->where($map)->count();

        $msg = array(
            'allnum' => $dpsum, //党组织总数
            'organizesum' => $organizesum,  //社会组织数
            'organizenum' => $organizenum,  //人数
            'companysum' => $companysum,    //企业工作委员会
            'companynum' => $companynum,
            'villagesum' => $villagesum,    //村社区
            'villagenum' => $villagenum,
            'partysum' => $partysum,        //机关企事业
            'partynum' => $partynum,
            'usernum' => $userNum, //总人数
            'concernnum' => $concernNum, //关注人数
            'avgage' => $age, //平均年龄,
            'agepercent1' => $agepercent1,
            'agepercent2' => $agepercent2,
            'agepercent3' => $agepercent3,
            'agepercent4' => $agepercent4,
            'agepercent5' => $agepercent5,
            'male' => $male,
            'female' => $female,
            'edu1' => $edu1,
            'edu2' => $edu2,
            'edu3' => $edu3,
            'edu4' => $edu4,
            'edu5' => $edu5,
            'edu6' => $edu6,
            'edu7' => $edu7,
            'edu8' => $edu8,
        );
        $this->assign('msg',$msg);


        return $this->fetch();
    }

    /**
     * 图表统计页
     */
    public function chart(){
        //判断是否推送贺卡
//        $this->push_card();
        //判断是否推送建党节推送
//        $this->party_day();
        /*党组织总数*/
        $fa = WechatDepartment::where('parentid',1)->select(); //获取父级部门
        $dpsum = 0; //党组织总数
        foreach ($fa as $key => $value) {
            $dp = WechatDepartment::where('parentid',$value['id'])->count();  //循环输出子部门取数
            $dpsum += $dp;
        }

        //社会组织 id:2
        $organizesum = WechatDepartment::where('parentid',2)->count();   //部门数
        $organizeuser1 = 0; //三级部门人数
        $organizeuser2 = 0; //四级部门人数
        $organizeuser3 = WechatDepartmentUser::where('departmentid',2)->count();    //二级部门用户人数
        $organize1 = WechatDepartment::where('parentid',2)->select();   //查找三级部门
        foreach ($organize1 as $key => $value) {
            $ouser1 = WechatDepartmentUser::where('departmentid',$value['id'])->count(); //统计用户
            $organizeuser1 += $ouser1;
            $organize2 = WechatDepartment::where('parentid',$value['id'])->select();    //查找四级部门
            if($organize2) {
                foreach ($organize2 as $k => $val) {
                    $ouser2 = WechatDepartmentUser::where('departmentid',$value['id'])->count();    //统计人数
                    $organizeuser2 += $ouser2;
                }
            }
        }
        $organizenum = $organizeuser1 + $organizeuser2 + $organizeuser3; //总人数

        //企业工作委员会 id:3
        $companysum = WechatDepartment::where('parentid',3)->count();
        $companyuser1 = 0;
        $companyuser2 = 0;
        $companyuser3 = WechatDepartmentUser::where('departmentid',3)->count();    //二级部门用户人数
        $company1 = WechatDepartment::where('parentid',3)->select();
        foreach ($company1 as $key => $value) {
            $cuser1 = WechatDepartmentUser::where('departmentid',$value['id'])->count();
            $companyuser1 += $cuser1;

            $company2 = WechatDepartment::where('parentid',$value['id'])->select();    //查找四级部门
            if($company2) {
                foreach ($company2 as $k => $val) {
                    $cuser2 = WechatDepartmentUser::where('departmentid',$value['id'])->count();    //统计人数
                    $companyuser2 += $cuser2;
                }
            }
        }
        $companynum = $companyuser1 + $companyuser2 + $companyuser3;

        //村社区 id:4
        $villagesum = WechatDepartment::where('parentid',4)->count();
        $villageuser1 = 0;
        $villageuser2 = 0;
        $villageuser3 = WechatDepartmentUser::where('departmentid',4)->count();    //二级部门用户人数
        $village1 = WechatDepartment::where('parentid',4)->select();
        foreach ($village1 as $key => $value) {
            $vuser1 = WechatDepartmentUser::where('departmentid',$value['id'])->count();
            $villageuser1 += $vuser1;
            $village2 = WechatDepartment::where('parentid',$value['id'])->select();
            if($village2) {
                foreach ($village2 as $k => $val) {
                    $vuser2 = WechatDepartmentUser::where('departmentid',$value['id'])->count();
                    $villageuser2 += $vuser2;
                }
            }
        }
        $villagenum = $villageuser1 + $villageuser2 + $villageuser3;

        //机关企事业 id:10
        $partysum = WechatDepartment::where('parentid',10)->count();
        $partyuser1 = 0;
        $partyuser2 = 0;
        $partyuser3 = WechatDepartmentUser::where('departmentid',10)->count();    //二级部门用户人数
        $party1 = WechatDepartment::where('parentid',10)->select();
        foreach ($party1 as $key => $value) {
            $puser1 = WechatDepartmentUser::where('departmentid',$value['id'])->count();
            $partyuser1 += $puser1;
            $party2 = WechatDepartment::where('parentid',$value['id'])->select();
            if($party2) {
                foreach ($party2 as $k => $val) {
                    $puser2 = WechatDepartmentUser::where('departmentid',$value['id'])->count();
                    $partyuser2 += $puser2;
                }
            }
        }
        $partynum = $partyuser1 + $partyuser2 + $partyuser3;

        /*党员信息*/
        $map['department'] = array('neq','[9]');
        $userNum = WechatUser::where($map)->count();     //总人数
        $concernNum = WechatUser::where('status',1)->where($map)->count(); //关注人数
        $nonNum = $userNum - $concernNum; //未关注人数

        $avg = WechatUser::avg('age');  //平均年龄
        $age = substr($avg,0,2);
        $age1 = WechatUser::where('age','lt',20)->where($map)->count();  //20一下百分比
        $age2 = WechatUser::where('age','between',[20,30])->where($map)->count();    //20-30
        $age3 = WechatUser::where('age','between',[30,40])->where($map)->count();    //30-40
        $age4 = WechatUser::where('age','between',[40,50])->where($map)->count();    //40-50
        $age5 = WechatUser::where('age','gt',50)->where($map)->count();    //50以上

        $m = WechatUser::where('gender','eq',1)->where($map)->count(); //男性人数
        $male = round(($m/$userNum)*100);
        $w = WechatUser::where('gender','eq',2)->where($map)->count(); //女性人数
        $female = round(($w/$userNum)*100);

        $edu1 = WechatUser::where('education','eq',"初中以下")->where($map)->count();    //统计学历人数
        $edu2 = WechatUser::where('education','eq',"初中")->where($map)->count();
        $edu3 = WechatUser::where('education','eq',"高中")->where($map)->count();
        $edu4 = WechatUser::where('education','eq',"中专")->where($map)->count();
        $edu5 = WechatUser::where('education','eq',"大专")->where($map)->count();
        $edu6 = WechatUser::where('education','eq',"本科")->where($map)->count();
        $edu7 = WechatUser::where('education','eq',"硕士")->where($map)->count();
        $edu8 = WechatUser::where('education','eq',"硕士以上")->where($map)->count();

        $msg = array(
            'allnum' => $dpsum, //党组织总数
            'organizesum' => $organizesum,  //社会组织数
            'organizenum' => $organizenum,  //人数
            'companysum' => $companysum,    //企业工作委员会
            'companynum' => $companynum,
            'villagesum' => $villagesum,    //村社区
            'villagenum' => $villagenum,
            'partysum' => $partysum,        //机关企事业
            'partynum' => $partynum,
            'usernum' => $userNum, //总人数
            'concernnum' => $concernNum, //关注人数
            'nonnum' => $nonNum, //未关注人数
            'avgage' => $age, //平均年龄,
            'agepercent1' => $age1,
            'agepercent2' => $age2,
            'agepercent3' => $age3,
            'agepercent4' => $age4,
            'agepercent5' => $age5,
            'male' => $male,
            'female' => $female,
            'edu1' => $edu1,
            'edu2' => $edu2,
            'edu3' => $edu3,
            'edu4' => $edu4,
            'edu5' => $edu5,
            'edu6' => $edu6,
            'edu7' => $edu7,
            'edu8' => $edu8,
        );
        $this->assign('msg',$msg);

        
        return $this->fetch();
    }
}