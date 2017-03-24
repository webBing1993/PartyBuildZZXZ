<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/15
 * Time: 13:36
 */
namespace app\home\controller;
use think\Controller;
use think\Db;

class Game extends Base{
    /**
     * 2048游戏
     */
    public function ttfe(){
        $this->jssdk();
        return $this->fetch();
    }
    /**
     * 记录跟取出2048用户分数
     */
    public function ttfe_rank(){
        $userId = session('userId');
        //当前时间戳
        $time = time();
        //取出今日排行榜
        $dateStr = date('Y-m-d',$time);
        //获取当天0点的时间戳
        $timestamp0=strtotime($dateStr);
        //获取当天24点的时间戳
        $timestamp24=strtotime($dateStr)+ 86400;
        $game = model('Game');
        if(!$_POST){
            $today = Db::query("select t2.name,t1.score,t2.avatar from `pb_game` as t1 LEFT JOIN `pb_wechat_user` as t2 ON t1.id=t2.userid WHERE timestamp>? and timestamp<? ORDER BY t1.score DESC,t1.timestamp limit 0,?",[$timestamp0,$timestamp24,15]);
            if(empty($today)){
                $this ->assign('info',0);
            }else{
                $this ->assign('info',1);
            }
            $this -> assign('today',$today);
            return $this -> fetch();
        }
        else if(!empty($_POST)){
            //如果是游客就不做任何操作
            if ($userId=='visitor'){return $this->error('游客不做任何操作');}
            //查找用户数据
            $score = $_POST['score'];
            $info = $game -> find($userId);
            if($info){
                //如果存在今天的数据,判断修改数据
                $data = $game -> where("timestamp",'>',$timestamp0) -> where("id","$userId")->find();
                if(!empty($data)){
                    $data1=$game -> where('score','<',$score)-> where('id',"$userId") ->find();
                    if($data1){
                        $game->save([
                            'timestamp' => $time,
                            'score' => $score
                        ], ['id' => $userId]);
                        return $this->success('修改数据成功');
                    }else{
                        return $this->success('今日最高分已经存在');
                    }
                }else{
                    $game->save([
                        'timestamp' => $time,
                        'score' => $score
                    ], ['id' => $userId]);
                    return $this->success('修改数据成功');
                }
            }else{
                //如果不存在,创建数据
                $game -> data([
                    'id' => $userId,
                    'score' => $score,
                    'timestamp' =>$time,
                ])->save();
                return $this->success('添加数据成功');
            }
        }
    }
}