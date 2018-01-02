<?php
/**
 * Created by PhpStorm.
 * User: daire
 * Date: 2017/12/29
 * Time: 15:59
 */

namespace app\home\controller;


class Study extends Base
{
    public function index(){
        return $this->fetch();
    }
}