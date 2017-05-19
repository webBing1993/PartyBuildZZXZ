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
use app\home\model\Game as GameModel;

class Game extends Base{
    /**
     * 游戏首页
     */
    public function index(){
        return $this ->fetch();
    }
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
        $game = new GameModel();
        if(!$_POST){
            $today = $game ->where(['timestamp' => ['EGT',$timestamp0],'type' =>'1']) ->order('score desc') ->limit('10') ->select();
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
            $info = $game ->where(array('userid' =>$userId,'type' => 1)) ->find();
            if($info){
                //如果存在今天的数据,判断修改数据
                if($info['timestamp'] >= $timestamp0){
                    if($info['score'] < $score){
                        $game ->save([
                            'timestamp' => $time,
                            'score' => $score
                        ], ['userid' => $userId ,'type' => 1]);
                        return $this->success('修改数据成功');
                    }else{
                        return $this->success('今日最高分已经存在');
                    }
                }else{
                    $game->save([
                        'timestamp' => $time,
                        'score' => $score,
                        'type' => 1
                    ], ['userid' => $userId ,'type' => 1]);
                    return $this->success('修改数据成功');
                }
            }else{
                //如果不存在,创建数据
                $game -> data([
                    'userid' => $userId,
                    'score' => $score,
                    'timestamp' =>$time,
                    'type' => 1
                ])->save();
                return $this->success('添加数据成功');
            }
        }
    }
    //拼图游戏
    public function jigsaw(){
        $this ->jssdk();
        //每次随机从拼图图库取出图片
        $img_path = Db::table('pb_game_img') ->where('status',1) ->order('rand()') ->limit(1) ->find();
        $this ->assign('img_path',$img_path['img_path']);
        return $this ->fetch();
    }

    /**
     * 读取跟记录拼图用户分数
     */
    public function jigsaw_rank()
    {
        $userId = session('userId');
        //当前时间戳
        $time = time();
        //取出今日排行榜
        $dateStr = date('Y-m-d', $time);
        //获取当天0点的时间戳
        $timestamp0 = strtotime($dateStr);
        $game = new GameModel();
        if (!$_POST) {
            $today = $game->where(['timestamp' => ['EGT', $timestamp0],'type' =>'2']) ->order('score') ->limit('10') ->select();
            if (empty($today)) {
                $this->assign('info', 0);
            } else {
                $this->assign('info', 1);
            }
            $this->assign('today', $today);
            return $this->fetch();
        } else if (!empty($_POST)) {
            //如果是游客就不做任何操作
            if ($userId == 'visitor') {
                return $this->error('游客不做任何操作');
            }
            //查找用户数据
            $score = $_POST['score'];
            $info = $game->where(array('userid' => $userId, 'type' => 2))->find();
            if ($info) {
                //如果存在今天的数据,判断修改数据
                if ($info['timestamp'] >= $timestamp0) {
                    if ($info['score'] > $score) {
                        $game->save([
                            'timestamp' => $time,
                            'score' => $score
                        ], ['userid' => $userId,'type' => 2]);
                        return $this->success('修改数据成功');
                    } else {
                        return $this->success('今日最高分已经存在');
                    }
                } else {
                    $game->save([
                        'timestamp' => $time,
                        'score' => $score,
                        'type' => 2
                    ], ['userid' => $userId,'type' => 2]);
                    return $this->success('修改数据成功');
                }
            } else {
                //如果不存在,创建数据
                $game->data([
                    'userid' => $userId,
                    'score' => $score,
                    'timestamp' => $time,
                    'type' => 2
                ])->save();
                return $this->success('添加数据成功');
            }
        }
    }
}