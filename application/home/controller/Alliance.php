<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2016/11/2
 * Time: 13:21
 */
namespace app\home\controller;
use app\home\model\AllianceArrange;
use app\home\model\AllianceShow;
use think\Log;

class Alliance extends Base{
    /*
     * 地信红盟主页
     */
    public function index(){
        $model = new AllianceShow();
        $data = $model->getShow();
        if (empty($data)) {
            return $this->error("没有取到数据!");
        }
        $list = [];
        foreach($data as $value)
        {
            $year = date('Y',$value['create_time']);
            $month = date('m',$value['create_time']);
            $quarter = $month%3 +1;
            switch ($value['type']) {
                case "1":
                    $list[$year][$quarter][$month][] = $value;
                    break;
                case "2":
                    $list[$year][$quarter][$month][] = $value;
                    break;
                case "3":
                    $list[$year][$quarter][] = $value;
                    break;
                default:
                    Log::error("");
                    break;
            }
        }
        $this->assign('list',$list);
        dump($list);
        
        
        return $this->fetch();
    }

    /*
     * 活动安排更多页
     */
    public function lists(){
        $map = array(
            'status' => 1,
        );
        $arrangeModel = new AllianceArrange();
        $list = $arrangeModel->where($map)->order('create_time desc')->limit(7)->select();
        foreach ($list as $value) {
            if($value['time'] < time()) {
                $value['begin'] = 0;    //未开始
            }else{
                $value['begin'] = 1;    //已结束
            }
        }
        $this->assign('list',$list);
        return $this->fetch();
    }

    /*
     * 加载更多
     */
    public function listsmore() {
        $len = input('length');
        $map =  array(
            'status' => 1,
        );
        $arrangeModel = new AllianceArrange();
        $list = $arrangeModel->where($map)->order('create_time desc')->limit($len,7)->select();
        foreach ($list as $value) {
            $value['time'] = date('Y-m-d',$value['time']);
        }
        if($list) {
            return $this->success("加载成功","",$list);
        }else{
            return $this->error("加载失败");
        }

    }

    /*
     * 通知详情页
     */
    public function informdetail(){
        $id = input('id');
        $arrangeModel = new AllianceArrange();
        $info = $arrangeModel->get($id);
        $this->assign('info',$info);
        return $this->fetch();
    }

    /*
     * 文章详情页
     */
    public function articledetail(){
        $id = input('id');
        $showModel = new AllianceShow();
        $info = $showModel->get($id);
        $this->assign('info',$info);
        return $this->fetch();
    }

}
