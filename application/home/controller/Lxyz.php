<?php
/**
 * Created by PhpStorm.
 * User: daire
 * Date: 2017/12/22
 * Time: 14:23
 */

namespace app\home\controller;


class Lxyz extends Base
{
    public function index(){
        return $this->fetch();
    }
}