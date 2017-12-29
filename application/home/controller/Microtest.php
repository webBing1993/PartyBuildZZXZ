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
}