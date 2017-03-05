<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/2/27
 * Time: 14:08
 */
namespace app\admin\controller;
use app\admin\model\Shop as ShopModel;
use app\admin\model\Product;
use com\wechat\QYWechat;
use think\Config;
use app\home\model\Car;
/*
  * 积分商城
  */
class Shop extends Admin{
    /*
     * 商铺首页
     */
    public function index(){
        $map = array(
            'status' => 0,
        );
        $list = $this->lists('Shop',$map);
        int_to_string($list,array(
            'status' => array(0 => "已发布"),
            'recommend' =>array(0 => "否",1 => "是")
        ));
        $this->assign('list',$list);
        return $this->fetch();
    }
    /*
     * 商铺  同步
     */
    public function updateShop(){
        $Wechat = new QYWechat(Config::get('party'));
        if($Wechat->errCode != 40001) {
            return $this->error("同步出错");
        }
        /* 同步部门 */
        $list = $Wechat->getDepartment();
        $users = $Wechat->getUserListInfo($list['department'][5]['id']);
        foreach ($users['userlist'] as $user) {
            $data = array();
            $data['department'] = json_encode($user['department']);
            $data['title'] = $user['name'];
            $data['tel'] = $user['mobile'];
            $data['userid'] = $user['userid'];
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            if(ShopModel::get(['userid'=>$user['userid']])) {
                ShopModel::where(['userid'=>$user['userid']])->update($data);
            }else{
                ShopModel::create($data);
            }
        }
        return $this->success("同步成功");
    }
    /*
     * 商铺 修改
     */
    public function add(){
        $id = input('param.id');
        if($id){
            // 修改 
            if (IS_POST){
                // 保存
                $data = input('post.');
                $shopModel = new ShopModel();
                $data['update_user'] = $_SESSION['think']['user_auth']['id'];
                $data['update_time'] = time();
                $model = $shopModel->validate('Shop.act')->save($data,['id' => $data['id']]);
                if($model){
                    return $this->success('修改成功',Url("Shop/index"));
                }else{
                    return $this->error($shopModel->getError());
                }
            }else{
                $msg = ShopModel::where(['id' => $id,'status' => 0])->find();
                $this->assign('msg',$msg);
                return $this->fetch();
            }
        }
    }
    /*
     * 产品 删除
     */
    public function del(){
        $id = input('id');
        $map['status'] = "-1";
        $info = Product::where('id',$id)->update($map);
        if($info) {
            $Car = Car::where('product_id',$id)->find();
            if (empty($Car)){
                return $this->success("删除成功");
            }else{
                $res = Car::where('product_id',$id)->delete();
                if ($res){
                    return $this->success("删除成功");
                }else{
                    return $this->error("删除失败");
                }
            }
        }else{

        }
    }
    /*
     * 产品 首页
     */
    public function product(){
        $map = array(
            'status' => 0,
        );
        $list = $this->lists('Product',$map);
        int_to_string($list,array(
            'status' => array(0=>"已发布"),
        ));
        $this->assign('list',$list);
        return $this->fetch();
    }
    /*
     * 产品  添加/修改
     */
    public function edit(){
        $id = input('param.id');
        if($id){
            // 修改
            if (IS_POST){
                // 保存
                $data = input('post.');
                $Product = new Product();
                $data['update_user'] = $_SESSION['think']['user_auth']['id'];
                $data['left'] = $data['num'];
                $data['update_time'] = time();
                $model = $Product->validate('Product.atc')->save($data,['id' => $data['id']]);
                if($model){
                    return $this->success('修改成功',Url("Shop/product"));
                }else{
                    return $this->error($Product->getError());
                }
            }else{
                $msg = Product::where(['id' => $id,'status' => 0])->find();
                $shop = ShopModel::where('status',0)->order('id desc')->select();
                $this->assign('shop',$shop);
                $this->assign('msg',$msg);
                return $this->fetch();
            }
        }else{
            // 添加
            if (IS_POST){
                // 保存
                $data = input('post.');
                if(empty($data['id'])) {
                    unset($data['id']);
                }
                $Product = new Product();
                $data['create_user'] = $_SESSION['think']['user_auth']['id'];
                $data['left'] = $data['num'];
                $model = $Product->validate('Product.act')->save($data);
                if($model){
                    return $this->success('新增成功!',Url("Shop/product"));
                }else{
                    return $this->error($Product->getError());
                }
            }else{
                $this->default_pic();
                $shop = ShopModel::where('status',0)->order('id desc')->select();
                $this->assign('shop',$shop);
                $this->assign('msg','');
                return $this->fetch();
            }
        }
    }
}