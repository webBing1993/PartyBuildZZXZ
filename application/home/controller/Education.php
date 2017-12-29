<?php
/**
 * Created by PhpStorm.
 * User: daire
 * Date: 2017/12/28
 * Time: 9:24
 */

namespace app\home\controller;


class Education extends Base
{
    public function index(){
        return $this->fetch();
    }
    public function movedetail(){
        return $this->fetch();
    }
    public function bookdetail(){
        return $this->fetch();
    }
    public function textdetail(){
        return $this->fetch();
    }
    public function musicdetail(){
        return $this->fetch();
    }
    public function detail(){
        return $this->fetch();
    }
}