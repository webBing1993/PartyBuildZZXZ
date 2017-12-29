<?php
/**
 * Created by PhpStorm.
 * User: daire
 * Date: 2017/12/22
 * Time: 14:23
 */

namespace app\home\controller;


class Learns extends Base
{
    public function index(){
        return $this->fetch();
    }
    public function meeting(){
        return $this->fetch();
    }
}