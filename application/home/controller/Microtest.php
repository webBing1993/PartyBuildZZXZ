<?php
/**
 * Created by PhpStorm.
 * User: daire
 * Date: 2017/12/28
 * Time: 16:37
 */

namespace app\home\controller;


class Microtest extends Base
{
    public function index(){
        return $this->fetch();
    }

    /**
     * 互动模式主页
     */
    public function game(){

        return $this->fetch();
    }

    /**
     * 抄写页面
     */
    public function copy(){
        $userId = session('userId');
        $id = input('id');
        if(IS_POST){
            //传递数组
            $data = array(
                'chapter' => $id,
                'length' => input('length'),
                'status' => input('status'),
                'create_user' => $userId,
            );
            $map = array(
                'chapter' => $id,
                'create_user' => $userId,
            );

            $info = ConstitutionCopy::where($map)->find();  //查看是否存在记录，存在则更新，不存在新增
            if($info) {
                $mes = ConstitutionCopy::where($map)->update($data);
                if($mes){
                    $userId = session('userId');
                    //总字数
                    $list = ConstitutionModel::all();
                    $sum = 0;
                    foreach ($list as $value) {
                        $sum += $value['len'];
                    }
                    //当前字数
                    $all = ConstitutionCopy::where('create_user',$userId)->select();
                    $number = 0;
                    foreach ($all as $value) {
                        $number += $value['length'];
                    }
                    $score = round(($number/$sum)*100);   //四舍五入百分比
                    $times = WechatUser::where('userid',$userId)->find();   //抄写次数
                    //总分，不足百分之一记1分。
                    if($score < 1) {
                        $mark = 1 + 100*$times['times'];
                    }else {
                        $mark = $score + 100*$times['times'];
                    }
                    $code['trad_score'] = $mark;
                    WechatUser::where('userid',$userId)->update($code);

                    return $this->success("更新成功");
                }else{
                    return $this->error("更新失败");
                }
            }else {
                $mes = ConstitutionCopy::create($data);
                if($mes) {
                    $userId = session('userId');
                    //总字数
                    $list = ConstitutionModel::all();
                    $sum = 0;
                    foreach ($list as $value) {
                        $sum += $value['len'];
                    }
                    //当前字数
                    $all = ConstitutionCopy::where('create_user',$userId)->select();
                    $number = 0;
                    foreach ($all as $value) {
                        $number += $value['length'];
                    }
                    $score = round(($number/$sum)*100);   //四舍五入百分比
                    $times = WechatUser::where('userid',$userId)->find();   //抄写次数
                    //总分，不足百分之一记1分。
                    if($score < 1) {
                        $mark = 1 + 100*$times['times'];
                    }else {
                        $mark = $score + 100*$times['times'];
                    }
                    $code['trad_score'] = $mark;
                    WechatUser::where('userid',$userId)->update($code);

                    return $this->success("新增成功");
                }else{
                    return $this->error("新增失败");
                }
            }
        }else{
            //党章内容
            $msg = ConstitutionModel::get($id);
            switch ($msg['id']){
                case 1:
                    $msg['title'] = "第一章 总 纲";
                    break;
                case 2:
                    $msg['title'] = "第二章 党 员";
                    break;
                case 3:
                    $msg['title'] = "第三章 党的组织制度";
                    break;
                case 4:
                    $msg['title'] = "第四章 党的中央组织";
                    break;
                case 5:
                    $msg['title'] = "第五章 党的地方组织";
                    break;
                case 6:
                    $msg['title'] = "第六章 党的基层组织";
                    break;
                case 7:
                    $msg['title'] = "第七章 党的干部";
                    break;
                case 8:
                    $msg['title'] = "第八章 党的纪律";
                    break;
                case 9:
                    $msg['title'] = "第九章 党的纪律检查机关";
                    break;
                case 10:
                    $msg['title'] = "第十章 党 组";
                    break;
                case 11:
                    $msg['title'] = "第十一章 党和共产主义青年团的关系";
                    break;
                case 12:
                    $msg['title'] = "第十二章 党徽党旗";
                    break;
            }
            $this->assign('msg',$msg);

            //文章必须按照顺序来抄写。
            $con = array(
                'chapter' => $id,
                'create_user' => $userId,
            );
            $info = ConstitutionCopy::where($con)->find();
            /* status : 0 可提交，1已完成 ，2未到顺序
               length : 完成/未完成返回文章长度，未开始则为空 */
            $return = array();
            if($id == 1) {
                if($info && $info['status'] == 1) {
                    $return['status'] = 1;    //初始文章存在记录，显示已完成
                    $return['length'] = $info['length'];
                }else{
                    $return['status'] = 0;    //不存在记录或未完成，可提交
                    ($info['length']) ? $return['length'] = $info['length'] : $return['length'] = "";
                }
            }else{
                if($info) {
                    if($info['status'] == 1) {
                        $return['status'] = 1;    //存在且完成
                    }else{
                        $return['status'] = 0;    //未完成
                    }
                    $return['length'] = $info['length'];
                }else{
                    $return['status'] = 2;     //不存在按顺序
                    $return['length'] = "";

                    //上一篇完成，开启下篇可写
                    $con2 = array(
                        'chapter' => $id-1,
                        'create_user' => $userId,
                    );
                    $info2 = ConstitutionCopy::where($con2)->find();
                    if($info2['status'] == 1){
                        $return['status'] = 0;
                        $return['length'] = "";
                    }else{
                        $return['status'] = 2;
                        $return['length'] = "";
                    }

                }
            }
            $this->assign('return',json_encode($return));
            $this->assign('visit',$userId);
            return $this->fetch();
        }
    }

    /**
     * 重置文章
     */
    public function restart(){
        $userId = session('userId');
        $all = ConstitutionCopy::where('create_user',$userId)->delete();
        if($all) {
            //wechatuser抄写次数加1
            $map['times'] = array('exp','`times`+1');
            $first = WechatUser::where('userid',$userId)->update($map);
            if($first) {
                //重置刷新排行榜分数
                $time = WechatUser::where('userid',$userId)->find();
                $info['trad_score'] = 100*$time['times'];
                WechatUser::where('userid',$userId)->update($info);
                return $this->success("重置成功");
            }
        }else{
            return $this->error("重置失败");
        }
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