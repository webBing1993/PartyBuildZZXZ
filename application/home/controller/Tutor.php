<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2016/11/2
 * Time: 13:21
 */
namespace app\home\controller;

use app\home\model\Picture;
use think\Controller;
use app\home\model\Talent as TalentModel;

class Tutor extends Base{
    /*
     * 红领学院
     */
    public function index(){

        return $this->fetch();
    }


}