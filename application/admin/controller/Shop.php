<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/2/27
 * Time: 14:08
 */
namespace app\admin\controller;
use app\admin\model\Picture;
use app\admin\model\Push;
use app\admin\model\Shop as ShopModel;
use app\admin\model\Product;
use com\wechat\QYWechat;
use com\wechat\TPQYWechat;
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

    /**
     * 推送列表
     */
    public function pushlist() {
        if(IS_POST){
            $id = input('id');
            //副图文本周内的新闻消息
            $t = $this->week_time();
            $info = array(
                'id' => array('neq',$id),
                'create_time' => array('egt',$t),
                'status' => 0,
            );
            $infoes = Product::where($info)->select();
            foreach ($infoes as $value) {
                $value['type_text'] = $value->shop->title;
            }
            return $this->success($infoes);
        }else{
            //消息列表
            $map = array(
                'class' => 13,
                'status' => array('egt',-1),
            );
            $list = $this->lists('Push',$map);
            int_to_string($list,array(
                'status' => array(-1 => '不通过', 0 => '未审核', 1=> '已发送'),
            ));
            //数据重组
            foreach ($list as $value) {
                $msg = Product::where('id',$value['focus_main'])->find();
                $value['title'] = $msg['title'];
            }
            $this->assign('list',$list);

            //主图文本周内的新闻消息
            $t = $this->week_time();    //获取本周一时间
            $info = array(
                'create_time' => array('egt',$t),
                'status' => 0,
            );
            $infoes = Product::where($info)->select();
            foreach ($infoes as $value) {
                $value['type_text'] = $value->shop->title;
            }
            $this->assign('info',$infoes);

            return $this->fetch();
        }
    }

    /**
     * 推送
     */
    public function push(){
        $data = input('post.');
        $arr1 = $data['focus_main'];    //主图文id
        isset($data['focus_vice']) ? $arr2 = $data['focus_vice'] : $arr2 = "";    //副图文id
        if($arr1 == -1){
            return $this->error("请选择主图文!");
        }else{
            //主图文信息
            $focus1 = Product::where('id',$arr1)->find();
            $title1 = $focus1['title'];
            $str1 = strip_tags($focus1['content']);
            $des1 = mb_substr($str1,0,100);
            $content1 = str_replace("&nbsp;","",$des1);  //空格符替换成空
            $url1 = "http://dqpb.0571ztnet.com/home/exchange/commodity/id/".$focus1['id'].".html";
            $pre1 = $focus1->shop->title;
            $img1 = Picture::get($focus1['front_cover']);
            $path1 = "http://dqpb.0571ztnet.com".$img1['path'];
            $information1 = array(
                "title" => $pre1.$title1,
                "description" => $content1,
                "url" => $url1,
                "picurl" => $path1,
            );
        }

        $information = array();
        if(!empty($arr2)) {
            //副图文信息
            $information2 = array();
            foreach ($arr2 as $key=>$value){
                $focus = Product::where('id',$value)->find();
                $title = $focus['title'];
                $str = strip_tags($focus['content']);
                $des = mb_substr($str,0,100);
                $content = str_replace("&nbsp;","",$des);  //空格符替换成空
                $url = "http://dqpb.0571ztnet.com/home/exchange/commodity/id/".$focus1['id'].".html";
                $pre = $focus->shop->title;
                $img = Picture::get($focus1['front_cover']);
                $path = "http://dqpb.0571ztnet.com".$img['path'];
                $info = array(
                    "title" => $pre.$title,
                    "description" => $content,
                    "url" => $url,
                    "picurl" => $path,
                );
                $information2[] = $info;
            }

            //数组合并，主图文放在首位
            foreach ($information2 as $k=>$v){
                $information[0] = $information1;
                $information[$k+1] = $v;
            }
        }else{
            $information[0] = $information1;
        }

        //重组成article数据
        $send = array();
        $re[] = $information;
        foreach ($re as $key => $value){
            $key = "articles";
            $send[$key] = $value;
        }

        //发送给服务号
        $Wechat = new TPQYWechat(Config::get('party'));
        $message = array(
//            'totag' => "18", //审核标签用户
            "touser" => "18768112486",
//            "touser" => "@all",   //发送给全体，@all
            "msgtype" => 'news',
            "agentid" => 8,
            "news" => $send,
            "safe" => "0"
        );
        $msg = $Wechat->sendMessage($message);

        if ($msg['errcode'] == 0){
            $data['focus_vice'] ? $data['focus_vice'] = json_encode($data['focus_vice']) : $data['focus_vice'] = null;
            $data['create_user'] = session('user_auth.username');
            $data['status'] = 1;
            $data['class'] = 13;
            //保存到推送列表
            $s = Push::create($data);
            if ($s){
                return $this->success("发送成功");
            }else{
                return $this->error("发送失败");
            }
        }else{
            return $this->error("发送失败");
        }
    }
}