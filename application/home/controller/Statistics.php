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
        /*党员信息*/
        $map['department'] = array('neq','[2]');
        $userNum = WechatUser::where($map)->count();     //总人数
        $concernNum = WechatUser::where('status',1)->where($map)->count(); //关注人数
        // 党总支 总个数
        $party_num = WechatDepartment::where(['parentid' => 1 ,'name' => ['neq','之图测试']])->count();
        $party1 = 0;
        $party2 = 0;
        $party3 = 0;
        $party4 = 0;
        $party5 = 0;
        $party6 = 0;
        $party7 = 0;
        $party8 = 0;
        $party9 = 0;
        $party10 = 0;
        $party11 = 0;
        $party = WechatDepartment::where(['parentid' => 1 ,'name' => ['neq','之图测试']])->field('id,name')->select();
        foreach($party as $value){
            switch ($value['name']){
                case "椒江区个协党总支":
                    $arr = WechatDepartment::where(['parentid' => $value['id']])->select();
                    $party1 = 0;
                    foreach($arr as $val){
                        $party1 += WechatUser::where(['department' => "[".$val['id']."]"])->count();
                    }
                    break;
                case "黄岩区个协党总支":
                    $arr = WechatDepartment::where(['parentid' => $value['id']])->select();
                    $party2 = 0;
                    foreach($arr as $val){
                        $party2 += WechatUser::where(['department' => "[".$val['id']."]"])->count();
                    }
                    break;
                case "路桥区个协党总支":
                    $arr = WechatDepartment::where(['parentid' => $value['id']])->select();
                    $party3 = 0;
                    foreach($arr as $val){
                        $party3 += WechatUser::where(['department' => "[".$val['id']."]"])->count();
                    }
                    break;
                case "临海市个协党总支":
                    $arr = WechatDepartment::where(['parentid' => $value['id']])->select();
                    $party4 = 0;
                    foreach($arr as $val){
                        $party4 += WechatUser::where(['department' => "[".$val['id']."]"])->count();
                    }
                    break;
                case "温岭市个协党工委":
                    $arr = WechatDepartment::where(['parentid' => $value['id']])->select();
                    $party5 = 0;
                    foreach($arr as $val){
                        $party5 += WechatUser::where(['department' => "[".$val['id']."]"])->count();
                    }
                    break;
                case "玉环市民个协党总支":
                    $arr = WechatDepartment::where(['parentid' => $value['id']])->select();
                    $party6 = 0;
                    foreach($arr as $val){
                        $party6 += WechatUser::where(['department' => "[".$val['id']."]"])->count();
                    }
                    break;
                case "天台县个协党委":
                    $arr = WechatDepartment::where(['parentid' => $value['id']])->select();
                    $party7 = 0;
                    foreach($arr as $val){
                        $party7 += WechatUser::where(['department' => "[".$val['id']."]"])->count();
                    }
                    break;
                case "仙居县个协党总支":
                    $arr = WechatDepartment::where(['parentid' => $value['id']])->select();
                    $party8 = 0;
                    foreach($arr as $val){
                        $party8 += WechatUser::where(['department' => "[".$val['id']."]"])->count();
                    }
                    break;
                case "三门县个协党总支":
                    $arr = WechatDepartment::where(['parentid' => $value['id']])->select();
                    $party9 = 0;
                    foreach($arr as $val){
                        $party9 += WechatUser::where(['department' => "[".$val['id']."]"])->count();
                    }
                    break;
                case "开发区党支部":
                    $arr = WechatDepartment::where(['parentid' => $value['id']])->select();
                    $party10 = 0;
                    foreach($arr as $val){
                        $party10 += WechatUser::where(['department' => "[".$val['id']."]"])->count();
                    }
                    break;
                case "温岭市市场监管局党委":
                    $arr = WechatDepartment::where(['parentid' => $value['id']])->select();
                    $party11 = 0;
                    foreach($arr as $val){
                        $party11 += WechatUser::where(['department' => "[".$val['id']."]"])->count();
                    }
                    break;
            }
        }
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
            'party' => $party_num,
            'party1' => $party1,
            'party2' => $party2,
            'party3' => $party3,
            'party4' => $party4,
            'party5' => $party5,
            'party6' => $party6,
            'party7' => $party7,
            'party8' => $party8,
            'party9' => $party9,
            'party10' => $party10,
            'party11' => $party11,
        );
        $this->assign('msg',$msg);


        return $this->fetch();
    }

    /**
     * 图表统计页
     */
    public function chart(){
        /*党员信息*/
        $map['department'] = array('neq','[2]');
        $userNum = WechatUser::where($map)->count();     //总人数
        $concernNum = WechatUser::where('status',1)->where($map)->count(); //关注人数
        $nonNum = $userNum - $concernNum; //未关注人数
        $party1 = 0;
        $party2 = 0;
        $party3 = 0;
        $party4 = 0;
        $party5 = 0;
        $party6 = 0;
        $party7 = 0;
        $party8 = 0;
        $party9 = 0;
        $party10 = 0;
        $party11 = 0;
        $party = WechatDepartment::where(['parentid' => 1 ,'name' => ['neq','之图测试']])->field('id,name')->select();
        foreach($party as $value){
            switch ($value['name']){
                case "椒江区个协党总支":
                    $arr = WechatDepartment::where(['parentid' => $value['id']])->select();
                    $party1 = 0;
                    foreach($arr as $val){
                        $party1 += WechatUser::where(['department' => "[".$val['id']."]"])->count();
                    }
                    break;
                case "黄岩区个协党总支":
                    $arr = WechatDepartment::where(['parentid' => $value['id']])->select();
                    $party2 = 0;
                    foreach($arr as $val){
                        $party2 += WechatUser::where(['department' => "[".$val['id']."]"])->count();
                    }
                    break;
                case "路桥区个协党总支":
                    $arr = WechatDepartment::where(['parentid' => $value['id']])->select();
                    $party3 = 0;
                    foreach($arr as $val){
                        $party3 += WechatUser::where(['department' => "[".$val['id']."]"])->count();
                    }
                    break;
                case "临海市个协党总支":
                    $arr = WechatDepartment::where(['parentid' => $value['id']])->select();
                    $party4 = 0;
                    foreach($arr as $val){
                        $party4 += WechatUser::where(['department' => "[".$val['id']."]"])->count();
                    }
                    break;
                case "温岭市个协党工委":
                    $arr = WechatDepartment::where(['parentid' => $value['id']])->select();
                    $party5 = 0;
                    foreach($arr as $val){
                        $party5 += WechatUser::where(['department' => "[".$val['id']."]"])->count();
                    }
                    break;
                case "玉环市民个协党总支":
                    $arr = WechatDepartment::where(['parentid' => $value['id']])->select();
                    $party6 = 0;
                    foreach($arr as $val){
                        $party6 += WechatUser::where(['department' => "[".$val['id']."]"])->count();
                    }
                    break;
                case "天台县个协党委":
                    $arr = WechatDepartment::where(['parentid' => $value['id']])->select();
                    $party7 = 0;
                    foreach($arr as $val){
                        $party7 += WechatUser::where(['department' => "[".$val['id']."]"])->count();
                    }
                    break;
                case "仙居县个协党总支":
                    $arr = WechatDepartment::where(['parentid' => $value['id']])->select();
                    $party8 = 0;
                    foreach($arr as $val){
                        $party8 += WechatUser::where(['department' => "[".$val['id']."]"])->count();
                    }
                    break;
                case "三门县个协党总支":
                    $arr = WechatDepartment::where(['parentid' => $value['id']])->select();
                    $party9 = 0;
                    foreach($arr as $val){
                        $party9 += WechatUser::where(['department' => "[".$val['id']."]"])->count();
                    }
                    break;
                case "开发区党支部":
                    $arr = WechatDepartment::where(['parentid' => $value['id']])->select();
                    $party10 = 0;
                    foreach($arr as $val){
                        $party10 += WechatUser::where(['department' => "[".$val['id']."]"])->count();
                    }
                    break;
                case "温岭市市场监管局党委":
                    $arr = WechatDepartment::where(['parentid' => $value['id']])->select();
                    $party11 = 0;
                    foreach($arr as $val){
                        $party11 += WechatUser::where(['department' => "[".$val['id']."]"])->count();
                    }
                    break;
            }
        }
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
            'malenum' => $m,
            'femalenum' => $w,
            'edu1' => $edu1,
            'edu2' => $edu2,
            'edu3' => $edu3,
            'edu4' => $edu4,
            'edu5' => $edu5,
            'edu6' => $edu6,
            'edu7' => $edu7,
            'edu8' => $edu8,
            'party1' => $party1,
            'party2' => $party2,
            'party3' => $party3,
            'party4' => $party4,
            'party5' => $party5,
            'party6' => $party6,
            'party7' => $party7,
            'party8' => $party8,
            'party9' => $party9,
            'party10' => $party10,
            'party11' => $party11,
        );
        $this->assign('msg',$msg);

        
        return $this->fetch();
    }
}