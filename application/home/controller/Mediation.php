<?php
/**
 * Created by PhpStorm.
 * User: daire
 * Date: 2017/12/25
 * Time: 14:51
 */

namespace app\home\controller;


class Mediation extends Base
{
    public function index(){
        return $this->fetch();
    }
    public function application(){
        return $this->fetch();
    }
}