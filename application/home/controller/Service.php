<?php
/**
 * Created by PhpStorm.
 * User: daire
 * Date: 2018/1/8
 * Time: 13:06
 */

namespace app\home\controller;


class Service extends Base
{
    public function organization(){
        return $this->fetch();
    }
    public function ordetail(){
        return $this->fetch();
    }
}