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
    public function details(){
        return $this->fetch();
    }
    public function medcase(){
        return $this->fetch();
    }
    public function newdetail(){
        return $this->fetch();
    }
    public function applicationdetail(){
        return $this->fetch();
    }
    public function tjydetails(){
        return $this->fetch();
    }
    public function yhdetails(){
        return $this->fetch();
    }
    public function evaluate(){
        return $this->fetch();
    }
}