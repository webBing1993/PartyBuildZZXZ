<?php
/**
 * Created by PhpStorm.
 * User: daire
 * Date: 2017/12/28
 * Time: 16:37
 */

namespace app\home\controller;

use app\admin\model\Question;
use app\home\model\Answer;
use app\home\model\Answers;
use app\home\model\WechatUser;
use think\Db;

class Microtest extends Base
{
    public function index(){
        return $this->fetch();
    }

    /**
     * 互动模式主页
     */
    public function game(){
        $this->anonymous();
//        $this->checkAnonymous();
        $wechatModel = new WechatUser();
        $userId = session('userId');
        
        //所在部门名称
        $dp = Db::table('pb_wechat_department_user')
            ->alias('a')
            ->join('pb_wechat_department b','a.departmentid = b.id','LEFT')
            ->where('a.userid',$userId)
            ->find();
        //个人信息
        $personal = $wechatModel::where('userid',$userId)->find();
        //dump($personal);exit;
//        $personal['score'] = $personal['score'] + 10;  // 关注企业号  基础分10
        $personal['dpname'] = $dp['name'];

        //每日一课数据
        $Answers = Answers::where(['userid' => $userId])->order('id desc')->whereTime('create_time','d')->find();
        $this->assign('check',0);//1为答过题
        if(empty($Answers)){   // 没有数据
            //取单选
            $arr=Question::all(['type'=>0]);
            foreach($arr as $value){
                $ids[]=$value->id;
            }
            //随机获取单选的题目
            $num=3;//题目数目
            $data=array();
            while(true){
                if(count($data) == $num){
                    break;
                }
                $index=mt_rand(0,count($ids)-1);
                $res=$ids[$index];
                if(!in_array($res,$data)){
                    $data[]=$res;
                }
            }
            foreach($data as $value){
                $question[]=Question::get($value);
            }
            
            $this->assign('question',$question);
        }else{  //  有数据
            // 当天已经答过题
            $Qid = json_decode($Answers->question_id);
            $rights=json_decode($Answers->value);
            $re = array();
            if (!empty($Qid)) {
                foreach ($Qid as $key => $value) {
                    $re[$key] = Question::get($value);
                    $re[$key]['right'] = $rights[$key];
                }
            }
            $this->assign('question',$re);
            $this->assign('check',1);//1为答过题
        }
        //在线答题
        $da=strtotime(date('Y-m-d',time()));
        $da2=$da+86400;
        //dump($da2);exit;//1517414400//1517500800
        $where2['create_time']=array(array('gt',$da),array('lt',$da2));
        $info2=Db::table('pb_answer')->where('userid',$userId)->where($where2)->find();
        //dump($info2);exit();
        if (!empty($info2)){
            $check2=1;
            $this->assign('check2',$check2);
        }else{
            $check2=0;
            $this->assign('check2',$check2);
        }
        $info = Answer::get(['userid'=>$userId]);
        if($info) {
            $exist=$info->exist;
            $this->assign('exist',$exist);
            $this->assign('info',$info);
        }else {
            $this->assign('exist',"");
            $this->assign('info',"");
        }

        //总榜
        $answer = Answer::where('1=1')->select();
        $list4 = array();
        foreach($answer as $value){
            $k = $value['userid'];
            $list4[$k][] = $value;
        }
        $news_s = array();
        foreach($list4 as $u => $val){
            $count = 0;
            foreach($val as $valu){
                $count += $valu->score;
            }
            $cen['userid'] = $u;
            $cen['score'] = $count;
            $news_s[] = $cen;
        }
        //倒序，字段score排序
        $sort_s = array(
            'direction' => 'SORT_DESC',
            'field' => 'score',
        );
        $arrSort_s = array();
        foreach ($news_s as $k => $v){
            foreach ($v as $key => $value){
                $arrSort_s[$key][$k] = $value;
            }
        }
        if($sort_s['direction'] && $arrSort_s){
            array_multisort($arrSort_s[$sort_s['field']],constant($sort_s['direction']),$news_s);
        }
        //最终重组，限制输出20名，获取用户个人信息
        $final_s = array();
        foreach ($news_s as $key => $value){
            if($key<20){
                if ($value['score']!=0) {
                    $user = WechatUser::where('userid', $value['userid'])->find();
                    $value['name'] = $user['name'];
                    $final_s[$key] = $value;
                }
            }
            if($value['userid'] == $userId){
                $personal['rank'] = $key+1;
            }
        }
        $this->assign('all',$final_s);
        $this->assign('personal',$personal);

        //获取周榜信息
        date_default_timezone_set("PRC");        //初始化时区
        $y = date("Y");        //获取当天的年份
        $m = date("m");        //获取当天的月份
        $d = date("d");        //获取当天的号数
        $todayTime= mktime(0,0,0,$m,$d,$y);        //将今天开始的年月日时分秒，转换成unix时间戳
        $time = date("N",$todayTime);        //获取星期数进行判断，当前时间做对比取本周一和上周一时间。
        //$t为本周周一，$s为上周周一
        switch($time){
            case 1: $t = $todayTime;
                break;
            case 2: $t = $todayTime - 86400*1;
                break;
            case 3: $t = $todayTime - 86400*2;
                break;
            case 4: $t = $todayTime - 86400*3;
                break;
            case 5: $t = $todayTime - 86400*4;
                break;
            case 6: $t = $todayTime - 86400*5;
                break;
            case 7: $t = $todayTime - 86400*6;
                break;
            default:
        }
        // 本周答题
        $answer = Answer::where(['create_time' => array('egt',$t)])->select();

        $list4 = array();
        foreach($answer as $value){
            $k = $value['userid'];
            $list4[$k][] = $value;
        }
        $news_w = array();
        foreach($list4 as $u => $val){
            $count = 0;
            foreach($val as $valu){
                $count += $valu->score;
            }
            $cen['userid'] = $u;
            $cen['score'] = $count;
            $news_w[] = $cen;
        }

        //倒序，字段score排序
        $sort = array(
            'direction' => 'SORT_DESC',
            'field' => 'score',
        );
        $arrSort = array();
        foreach ($news_w as $k => $v){
            foreach ($v as $key => $value){
                $arrSort[$key][$k] = $value;
            }
        }
        if($sort['direction'] && $arrSort){
            array_multisort($arrSort[$sort['field']],constant($sort['direction']),$news_w);
        }

        //最终重组，限制输出20名，获取用户个人信息
        $final = array();
        foreach ($news_w as $key => $value){
            if($key<20){
                if ($value['score']!=0) {
                    $user = WechatUser::where('userid', $value['userid'])->find();
                    $value['name'] = $user['name'];
                    $final[$key] = $value;
                }
            }
        }

        $this->assign('week',$final);

        //获取月榜信息
        date_default_timezone_set("PRC");        //初始化时区
        $start = mktime(0,0,0,date('m'),1,date('Y'));
        $end = mktime(23,59,59,date('m'),date('t'),date('Y'));

        // 本月答题
        $answer_m = Answer::where(['create_time' => array('between',[$start,$end])])->select();
        $list4_m = array();
        foreach($answer_m as $value){
            $k = $value['userid'];
            $list4_m[$k][] = $value;
        }
        $news_m = array();
        foreach($list4_m as $u => $val){
            $count = 0;
            foreach($val as $valu){
                $count += $valu->score;
            }
            $cen['userid'] = $u;
            $cen['score'] = $count;
            $news_m[] = $cen;
        }

        //倒序，字段score排序
        $sort_m = array(
            'direction' => 'SORT_DESC',
            'field' => 'score',
        );
        $arrSort_m = array();
        foreach ($news_m as $k => $v){
            foreach ($v as $key => $value){
                $arrSort_m[$key][$k] = $value;
            }
        }
        if($sort_m['direction'] && $arrSort_m){
            array_multisort($arrSort_m[$sort_m['field']],constant($sort_m['direction']),$news_m);
        }
        //最终重组，限制输出20名，获取用户个人信息
        $final_m = array();
        foreach ($news_m as $key => $value){
            if($key<20){
                if ($value['score']!=0) {
                    $user = WechatUser::where('userid', $value['userid'])->find();
                    $value['name'] = $user['name'];
                    $final_m[$key] = $value;
                }
            }
        }
        $this->assign('month',$final_m);
        return $this->fetch();

    }

    /**
     * 答题页面
     */
    public function answer(){
        $this->checkAnonymous();
        $this->anonymous();
        //取单选
        $arr=Question::all(['type'=>0]);
        foreach($arr as $value){
            $ids[]=$value->id;
        }
        //获取用户已经得到的题目
        $users=$users=session('userId');
        $List=Answer::get(['userid'=>$users]);
        if($List !==null){
            $list=$List->question_id;
            $lists=json_decode($list);
        }else{
            $lists=array();
        }
        //随机获取单选的题目
        $num=20;//题目数目
        $data=array();
        while(true){
            if(count($data)==$num){
                break;
            }
            $index=mt_rand(0,count($ids)-1);
            $res=$ids[$index];
            if(!in_array($res,$data) && !in_array($res,$lists)){
                $data[]=$res;
            }
        }
        foreach($data as $value){
            $question[]=Question::get($value);
        }

        //取多选
        $arr2=Question::all(['type'=>1]);
        foreach($arr2 as $value){
            $ids2[]=$value->id;
        }
        //随机获取多选
        $num2=10;//题目数目
        $data2=array();
        while(true){
            if(count($data2)==$num2){
                break;
            }
            $index2=mt_rand(0,count($ids2)-1);
            $res2=$ids2[$index2];
            if(!in_array($res2,$data2) && !in_array($res2,$lists)){
                $data2[]=$res2;
            }
        }
        foreach($data2 as $value){
            $questions[]=Question::get($value);
        }
        $this->assign('question',$question);
        $this->assign('questions',$questions);
        return $this->fetch();
    }
    /*
     * 用户题目保存
     */
    public function save(){
        $this->checkAnonymous();
        //获取用户提交信息
        $data=input('post.');
        //将题目ID数目json编码
        $questions=json_encode($data['arrId']);
        //将用户提交答案数组json编码
        $rights=json_encode($data['arr']);
        $users=session('userId');
        //若该用户已经存在则更新
        if(Answer::get(['userid'=>session('userId')])){
            $answer=Answer::get(['userid'=>session('userId')]);
            $answer->question_id=$questions;
            $answer->value=$rights;
            $answer->exist=0;
            if($answer->save()){
                return $this->success('保存成功');
            }else{
                return $this->error('保存失败');
            };
        }
        //若该用户不存在则添加
        $Answer=new Answer();
        $Answer->userid=$users;
        $Answer->question_id=$questions;
        $Answer->value=$rights;
        $Answer->exist=0;

        if($Answer->save()){
            return $this->success('保存成功');
        }else{
            return $this->error('保存失败');
        }
    }
    /*
     * 继续答题
     */
    public function goon(){
        $this->checkAnonymous();
        $this->anonymous();
        //获取该用户的已经保存的信息
        $users=$users=session('userId');
        $Info=Answer::get(['userid'=>$users]);
        //获取的题目ID,将json格式转化为数组
        $arr=json_decode($Info->question_id,true);
        //获取单选和多选的题目
        foreach($arr as $key=>$value){
            if($key>19){
                $arr2[]=Question::get($value);
            }else{
                $arr1[]=Question::get($value);
            }
        }
        //获取的题目答案,将json格式转化为数组
        $rights=json_decode($Info->value,true);
        //获取单选和多选的答案
        foreach($rights as $key=>$value){
            if($key<20){
                $right1[]=$value;
            }else{
                $right2[]=$value;
            }
        }
        $this->assign('right1',$right1);
        $this->assign('right2',$right2);
        $this->assign('arr1',$arr1);
        $this->assign('arr2',$arr2);
        return $this->fetch();
    }
    /*
     * 用户题目提交
     */
    public function submits(){
        $this->checkAnonymous();
        //获取用户提交信息
        $data=input('post.');
        $score=0;
        $num = 0;
        $sum = 0;
        foreach($data['arr'] as $value){
            if ($value != 0){
                $sum ++;
            }
        }
        //判断题目的对错,并改变分数
        foreach($data['arrId'] as $key=>$value){
            $question=Question::get($value);
            if($key <20){
                if($data['arr'][$key]==$question->value){
                    $status[$key]=1;
                    $score++;
                    $num ++;
                }else{
                    $status[$key]=0;
                }
            }else{
                if($data['arr'][$key]===explode(':',$question->value)){
                    $status[$key]=1;
                    $score++;
                    $num ++;
                }else{
                    $status[$key]=0;
                }
            }
        }
        //将获取的数据进行json格式转化
        $questions=json_encode($data['arrId']);
        $rights=json_encode($data['arr']);
        $status=json_encode($status);
        $users=session('userId');
        //将分数添加至用户积分排名
        $wechatModel = new WechatUser();
        $wechatModel->where('userid',session('userId'))->setInc('game_score',$score);
        // 统计该成员  答题总数   答对 题数
        Db::name('answer_data')->insert(['userid' => $users,'create_time' => time(),'num' => $num,'sum' => $sum]);
        //若该用户存在则修改数据
        if(Answer::get(['userid'=>session('userId')])){
            $answer=Answer::get(['userid'=>session('userId')]);
            $answer->question_id=$questions;
            $answer->value=$rights;
            $answer->status=$status;
            $answer->score=$score;
            $answer->exist=1;
            if($answer->save()){
                return $this->success('提交成功');
            }else{
                return $this->error('提交失败');
            };
        }else{
            //若该用户不存在则添加数据
            $Answer=new Answer();
            $Answer->userid=$users;
            $Answer->question_id=$questions;
            $Answer->value=$rights;
            $Answer->status=$status;
            $Answer->score=$score;
            $Answer->exist=1;
            if($Answer->save()){
                return $this->success('提交成功');
            }else{
                return $this->error('提交失败');
            }
        }
    }
    /*
     * 查看分数
     */
    public function score(){
        $this->checkAnonymous();
        $this->anonymous();
        $Answer=Answer::get(['userid'=>session('userId')]);
        $WechatUser=WechatUser::get(['userid'=>session('userId')]);
        $num=$WechatUser->game_score;
        $score=$Answer->score;
        $this->assign('num',$num);
        $this->assign('score',$score);
        return $this->fetch();
    }
    /*
     * 查看错题
     */
    public function errors(){
        $this->checkAnonymous();
        $this->anonymous();
        $Answer=Answer::get(['userid'=>session('userId')]);
        $arr=json_decode($Answer->status,true);
        $lists=json_decode($Answer->question_id,true);
        $rights=json_decode($Answer->value,true);
        foreach($arr as $key=>$value){
            if($value == 0){
                $Question=Question::get($lists[$key]);
                if($key <20 ){
                    $re[$key]=$Question;
                    $right1[$key]=$rights[$key];
                }else{
                    $res[$key]=$Question;
                    $right2[$key]=$rights[$key];
                }
            }
        }
        $this->assign('question',$re);
        $this->assign('questions',$res);
        $this->assign('right1',$right1);
        $this->assign('right2',$right2);
        return $this->fetch();
    }
    /*
     * 每日一课 页面
     */
    public function course(){
        $this->checkAnonymous();
        $this->anonymous();
        $userid = session('userId');
        $map = array(
            'userid' => $userid,
        );
        $Answers = Answers::where($map)->whereTime('create_time','d')->find();
        if(empty($Answers)){   // 没有数据
            //取单选
            $arr=Question::all(['type'=>0]);
            foreach($arr as $value){
                $ids[]=$value->id;
            }
            //随机获取单选的题目
            $num=3;//题目数目
            $data=array();
            while(true){
                if(count($data) == $num){
                    break;
                }
                $index=mt_rand(0,count($ids)-1);
                $res=$ids[$index];
                if(!in_array($res,$data)){
                    $data[]=$res;
                }
            }
            foreach($data as $value){
                $question[]=Question::get($value);
            }
            $this->assign('question',$question);
            return $this->fetch();
        }else{  //  有数据
            // 当天已经答过题
            $Qid = json_decode($Answers->question_id);
            $rights=json_decode($Answers->value);
            $re = array();
            foreach($Qid as $key => $value){
                $re[$key] = Question::get($value);
                $re[$key]['right'] = $rights[$key];
            }
            $this->assign('question',$re);
            return $this->fetch('scan');
        }
    }
    /*
     * 每日一课 提交
     */
    public function commit(){
        $this->checkAnonymous();
        // 获取用户提交数据
        $data = input('post.');
        $arr = $data['data'];
        $question = array();
        $status = array();
        $options = array();
        $Right = array();
        $score = 0;
        foreach($arr as $key => $value){
            $Question=Question::get($value[0]);
            switch($Question->value){
                case 1:
                    $right = "A";
                    break;
                case 2:
                    $right = "B";
                    break;
                case 3:
                    $right = "C";
                    break;
                case 4:
                    $right = "D";
                    break;

            }
            $Right[$key+1] = $right;
            $question[$key] = $value[0];
            $options[$key] = $value[1];
            // 判断 题目的对错
            if($value[1] == $Question->value){
                $status[$key] = 1;
                $score ++;
            }else{
                $status[$key] = 0;
            }
        }
        //将获取的数据进行json格式转化
        $questions = json_encode($question);
        $rights = json_encode($options);
        $status = json_encode($status);
        $users = session('userId');
        //将分数添加至用户积分排名
        $wechatModel = new WechatUser();
        $wechatModel->where('userid',$users)->setInc('score',$score);
        //  存储 表
        $Answers = new Answers();
        $Answers->userid = $users;
        $Answers->question_id = $questions;
        $Answers->value = $rights;
        $Answers->status = $status;
        $Answers->score = $score;
        $Answers->create_time = time();
        $res = $Answers->save();
        if($res){
            return $this->success('提交成功',array('id' =>$res,'score'=>$score,'right'=>$Right));
        }else{
            return $this->error('提交失败');
        }
    }
    /*
    * 每日一课  查看详情
    */
    public function scan(){
        $this->checkAnonymous();
        $this->anonymous();
        $id = input('id');
        if (empty($id)){
            return $this->error('系统错误');
        }
        $Answers = Answers::get($id);
        $Qid = json_decode($Answers->question_id);
        $rights=json_decode($Answers->value);
        $re = array();
        foreach($Qid as $key => $value){
            $re[$key] = Question::get($value);
            $re[$key]['right'] = $rights[$key];
        }
        $this->assign('question',$re);
        return $this->fetch();
    }
}