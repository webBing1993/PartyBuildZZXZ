<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/8
 * Time: 9:24
 */
namespace app\home\controller;
use app\admin\model\Shop;
use app\admin\model\Product;
use app\admin\model\WechatUser;
use app\home\model\Record;
use com\wechat\TPQYWechat;
use think\Config;
use app\home\model\Car;
use app\home\model\Years;

class Exchange extends Base{
    /**
     * 积分兑换
     */
    public function index(){
        $userId = session('userId');
        $Wechat = WechatUser::where('userid',$userId)->find();  //  获取总积分
        $Record = Record::where('userid',$userId)->select();  // 获取兑换过得积分
        $score = 0;
        foreach($Record as $value){
            $score += $value['content'];
        }
        $now = $Wechat->score - $score;  // 剩余积分
        $Product = Product::where(['status' =>0])->order('id desc')->select();
        $Shop = Shop::where(['status' => 0 ,'recommend' => 1])->limit(3)->select();
        if($Shop){
            $this->assign('is',1);
        }else{
            $this->assign('is',0);
        }
        // 重新排序 将兑光的排在最后
        foreach($Product as $key => $value){
            if ($value->left == 0){
                $temp = $value;
                unset($Product[$key]);
                array_push($Product,$temp);
            }
        }
        $this->assign('now',$now);  // 剩余积分
        $this->assign('score',$Wechat->score);  // 总积分
        $this->assign('product',$Product);
        $this->assign('shop',$Shop);
        return $this->fetch();
    }
    /**
     * 用户 兑换记录
     */
    public function record(){
        $userId = session('userId');
        $list = Record::where('userid',$userId)->order('id desc')->select();
        if ($list){
            $this->assign('is',1);
        }else{
            $this->assign('is',0);
        }
        $this->assign('list',$list);
        return $this->fetch();
    }
    /**
     * 兑换记录详情
     */
    public function recordmain(){
        $id = input('param.id');
        $record = Record::where('id',$id)->find();
        if($record['type'] == 0){
            $record['type'] = "扫码支付";
        }elseif ($record['type'] == 1){
            $record['type'] = "线上支付";
        }
        if ($record['status'] == 0){
            $record['status'] = "支付成功";
        }
        $this->assign('record',$record);
        return $this->fetch();
    }
    /**
     * 商品详情
     */
    public function commodity(){
        $id = input('id');
        $product = Product::where('id',$id)->find();  // 获取该商品详情
        $shop = Shop::where('id',$product['shop_id'])->find();  // 获取店铺详情
        $other = Product::where(['shop_id' => $shop['id'],'status' => 0,'id' => array('<>',$id)])->order('id desc')->select();
        // 重新排序 将兑光的排在最后
        foreach($other as $key => $value){
            if ($value->left == 0){
                $temp = $value;
                unset($other[$key]);
                array_push($other,$temp);
            }
        }
        $this->assign('shop',$shop);  //  店铺详情
        $this->assign('product',$product);  // 商品详情
        $this->assign('other',$other);
        $this->assign('link',$_SERVER['HTTP_HOST']);
        return $this->fetch();
    }
    /**
     * 商家商品列表
     */
    public function merchant(){
        $userId = session('userId');
        $Shop = Shop::where('userid',$userId)->find(); // 获取相应店铺的数据
        $Product = Product::where(['shop_id'=>$Shop['id'],'status' =>0])->order('id desc')->select();
        $Car = Car::where('userid',$userId)->count();
        if($Product){
            $this->assign('is',1);
        }else{
            $this->assign('is',0);
        }
        // 重新排序 将兑光的排在最后
        foreach($Product as $key => $value){
            if ($value->left == 0){
                $temp = $value;
                unset($Product[$key]);
                array_push($Product,$temp);
            }
        }
        $this->assign('car',$Car);
        $this->assign('shop',$Shop);
        $this->assign('product',$Product);
        return $this->fetch();
    }
    /**
     * 商家商品列表详情
     */
    public function merchantmain(){
        $this->jssdk();
        $id = input('id');
        $product = Product::where('id',$id)->find();
        if ($product['left'] == 0){
            $this->assign('is',0);
        }else{
            $this->assign('is',1);
        }
        $this->assign('product',$product);
        $this->assign('link',$_SERVER['HTTP_HOST']);
        return $this->fetch();
    }
    /*
     * 购物车 页面
     */
    public function shopcar(){
        $userId = session('userId');
        $Car = Car::where('userid',$userId)->select();  // 获取购物车的数据
        foreach($Car as $key=>$value){
            $Product = Product::where(['id' => $value['product_id'] , 'status' =>0])->find();
            $num = $Product['left'];  // 该商品库存
            if ($num == 0){
                unset($Car[$key]);  // 如果库存为0  就从购物车删除该商品
                Car::where('id',$value['id'])->delete();
            }else{
                if ($num < $value['num']){
                    $value['num'] = $num;  // 如果库存少于  就留最大库存
                    Car::where('id',$value['id'])->update(array('num'=>$num));
                }
            }
        }
        if($Car){
            $this->assign('is',1);
        }else{
            $this->assign('is',0);
        }
        $this->assign('car',$Car);
        return $this->fetch();
    }
    /*
     * 加入购物车
     */
    public function addcar(){
        $id = input('id');
        $num = input('num');
        $userId = session('userId');
        $num1 = Car::where(['userid' => $userId,'product_id'=>$id])->count();  // 购物车的数量
        $Product= Product::where(['id' => $id,'status' => 0])->find();
        $num2 = $Product->left;  // 库存剩余
        $num3 = $num2 - $num1;
        if ($num3 > $num){
            // 最后剩余  足够
            $data['userid'] = $userId;
            $data['product_id'] = $id;
            $data['num'] = $num;
            $res = Car::where(['userid' => $userId,'product_id'=>$id])->find();
            $Car = new Car();
            if ($res){
                $result = $Car->where(['id' => $res['id']])->setInc('num',$num);
            }else{
                $result = $Car->save($data);
            }
            if ($result){
                return array('status' => 1);
            }else{
                return array('status' => 2);
            }
        }else{
            //  添加的商品超出最后剩余
            return array('status' => 0 ,'num' => $num3);
        }
    }
    /*
     * 清空 购物车
     */
    public function clearcar(){
        $userId = session('userId');
        $res = Car::where('userid',$userId)->delete();
        if ($res){
            return 1 ; // 清空成功
        }else{
            return 0 ;  // 清空失败
        }
    }
    /*
     * 删除 购物车商品
     */
    public function delcar(){
        $id = input('id');
        $userId = session('userId');
    }
    /*
     * 购物车 加商品
     */
    public function pluscar(){
        $id = input('id'); // 产品id
    }
    /*
     * 购物车 减商品
     */
    public function cutcar(){
        $id = input('id'); // 产品id
    }
    /*
     * 地图 首页
     */
    public function map(){
        $id = input('id');
        $shop = Shop::where('id',$id)->find();
        $this->assign('shop',$shop);
        return $this->fetch();
    }
    /*
     * 商家扫码
     */
    public function scan(){
        $id = input('id');  //产品id
        $userid = input('user_id');  // 买商品的用户
        $price = input('price');  // 商品价格
        $num = input('num');  // 商品数量
        $sum = $num * $price;  // 总价
        $Wechat = new WechatUser();
        $user = $Wechat->where('userid',$userid)->find();
        if ($user){
            $Record = Record::where('userid',$userid)->select();
            $score = 0;
            foreach($Record as $value){
                $score += $value->content;
            }
            $now = $user['score'] - $score; // 剩余积分
            if ($now < $sum){
                return array('status'=>1) ; //  积分不足
            }else{
                // 更新产品表
                $Obj= Product::where('id',$id)->find();
                $temp = $Obj->left - $num;
                $result = Product::where(['id' => $id])->update(['left' => $temp]);
                if ($result){
                    // 产品表更新成功 存储兑换记录表
                    $info['userid'] = $userid;
                    $info['shop_id'] = $Obj['shop_id'];
                    $info['product_id'] = $id;
                    $info['content'] = $sum;
                    $info['num'] = $num;
                    $info['type'] = 0;  // 扫码支付
                    $Record = new Record();
                    $flag = $Record->save($info);
                    if ($flag){
                        $Shops = Shop::where('id',$Obj->shop_id)->find();
                        // 存储成功推送消息
                        $content = "尊敬的莫干红云党员用户，恭喜您已成功在".$Shops->title."购买".$Obj->title."，产品总数".$num."件。本次消费共计".$sum."积分，期待您的再次惠顾！";
                        $Wechat = new TPQYWechat(Config::get('party'));
                        $message = array(
                            'touser' => $userid,
                            "msgtype" => 'text',
                            "agentid" => 9,
                            "text" => array(
                                "content" => $content
                            ),
                            "safe" => "0"
                        );
                        $suc = $Wechat->sendMessage($message);
                        //推送成功后数据显示在个人中心我的消息列表中
                        if($suc) {
                            $MsgModel = new Years();
                            $name = WechatUser::where('userid',$userid)->find();
                            $arr = array(
                                'userid' => $userid,
                                'name' => $name['name'],
                                'type' => 1,
                                'years' => $content,
                            );
                            $info = $MsgModel->create($arr);
                            if($info) {
                                $Wechats = WechatUser::where(['userid'=>$userid])->find();
                                return array('status'=>2,'header'=>$Wechats['avatar'],'name'=>$Wechats['name'],'remain' => $temp) ; // 记录成功
                            }
                        }
                    }
                }
            }
        }else{
            return  array('status'=>0);  // 该用户不存在
        }
    }
    /**
     * 商家商品兑换记录列表详情
     */
    public function merchantrecord(){
        $userId = session('userId');
        $Shop = Shop::where(['userid' => $userId,'status' =>0])->find();  // 获取商家信息
        $list = Record::where(['shop_id'=>$Shop['id'],'status' => 0])->order('id desc')->select();  // 获取商家的交易记录
        $sum = 0;
        foreach($list as $value){
            $sum += $value->content;  // 获取商家的交易积分
        }
        if ($list){
            $this->assign('is',1);
        }else{
            $this->assign('is',0); // 记录是否存在
        }
        $this->assign('list',$list);
        $this->assign('sum',$sum);
        return $this->fetch();
    }
    /**
     * 商家商品兑换记录列表详情
     */
    public function merchantrecordmain(){
        $id = input('param.id');
        $record = Record::where('id',$id)->find();  // 获取记录详情
        if($record['type'] == 0){
            $record['type'] = "扫码支付";
        }elseif ($record['type'] == 1){
            $record['type'] = "线上支付";
        }
        if ($record['status'] == 0){
            $record['status'] = "支付成功";
        }
        $this->assign('record',$record);
        return $this->fetch();
    }
    //买家版的卖家店铺详情
    public function shopmain(){
        $id = input('id');
        $Shop = Shop::where('id',$id)->find();
        $Product = Product::where(['shop_id'=>$id,'status'=>0])->order('id desc')->select();
        // 重新排序 将兑光的排在最后
        foreach($Product as $key => $value){
            if ($value->left == 0){
                $temp = $value;
                unset($Product[$key]);
                array_push($Product,$temp);
            }
        }
        $this->assign('product',$Product);
        $this->assign('shop',$Shop);
        return $this->fetch();
    }
    //  客服页面 
    public function service(){
        return $this->fetch();
    }
}