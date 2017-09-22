<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/5/24
 * Time: 9:22
 */
namespace app\home\controller;
use think\Controller;

class Verify extends Controller{

    public function null(){
        return $this ->fetch();
    }

    /**
     * 党建产品介绍
     */
    public function introduce()
    {

        return $this->fetch();
    }

    public function intro (){

        return $this ->fetch();
    }
}