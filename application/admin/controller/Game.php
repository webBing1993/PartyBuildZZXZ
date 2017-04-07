<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/1
 * Time: 10:33
 */
namespace app\admin\controller;
use think\Db;
class Game extends Admin{
    /**
     * 智慧拼图列表
     */
    public function game_img(){
        $info=$this->lists('GameImg',array('status' => 1),'id desc');
        $this->assign('info',$info);
        return $this ->fetch();
    }
    /**
     * 添加拼图图片
     */
    public function add_img(){
        $img = model('gameImg');
        if(!empty($_POST)){
            $id =  $_POST['id'];
            unset($_POST['id']);
            if(!empty($id)){
                $record = $img ->save($_POST, ['id' => $id]);
                return $this ->success('修改成功');
            }else{
                $_POST['timestamp'] = time();
                $record = $img ->data($_POST) ->save();
                if($record){
                    return $this ->success('添加成功');
                }else{
                    return $this ->error('添加失败');
                }
            }
        }else{
            if(empty($_GET)){
                $this->assign('info', '');
            }else {
                $id = $_GET['id'];
                $info = $img->find($id);
                //分修改跟增加两种情况
                $this->assign('info', $info);
            }
            return $this ->fetch();
        }
    }
    //删除拼图图片
    public function del_img(){
        $img = model('gameImg');
        $record = $img -> save(['status'=>0],['id' => $_GET['id']]);
        if (empty($record)){
            return $this -> error('删除失败');
        }else{
            return $this -> success('删除成功');
        }
    }
}