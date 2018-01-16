<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/4/20
 * Time: 15:34
 */

namespace app\home\controller;
use app\home\model\Collect;
use app\home\model\Picture;
use app\home\model\WechatUser;
use app\home\model\WechatUserTag;
use think\Db;
use think\Request;

/**
 * Class User
 * 用户个人中心
 */
class User extends Base {
    /**
     * 个人中心主页
     */
    public function index(){
        //是否游客登录
        $this->anonymous();
        $userId = session('userId');
        $user = WechatUser::where('userid',$userId)->find();
        $this->assign('user',$user);

        $Tourist = WechatUserTag::where(['tagid' => 1, 'userid' => $userId])->find();
        if ($Tourist){
            $this ->assign('tourist',1);
        }else{
            $this ->assign('tourist',0);
        }

        return $this->fetch();
    }

    public function collect(){
        return $this->fetch();
    }
    public function chart(){
        return $this->fetch();
    }
    /**
     * 个人信息页
     */
    public function personal()
    {
        $request = Request::instance();
        $id = input('id');
        if (empty($id)) {

            $userId = session('userId');
        } else {

            $userId = $id;
        }
        $user = WechatUser::where('userid',$userId)->find();
        switch ($user['gender']) {
            case 0:
                $user['sex'] = "未定义";
                break;
            case 1:
                $user['sex'] = "男";
                break;
            case 2:
                $user['sex'] = "女";
                break;
            default:
                break;
        }


        $this->assign('user',$user);
        $this->assign('link',$request->domain());

        return $this->fetch();
    }

    /**
     * 设置头像
     */
    public function setHeader(){
        $userId = session('userId');
        $header = input('header');
        $map = array(
            'header' => $header,
        );
        $info = WechatUser::where('userid',$userId)->update($map);
        if($info){
            return $this->success("修改成功");
        }else{
            return $this->error("修改失败");
        }
    }

    /***
     * 签到二维码
     */
    public function qcode()
    {
        $this->assign('userId',session('userId'));
        return $this->fetch();
    }
    /**
     * 我的收藏
     */
    public function myCollect(){
        $userId = session('userId');
        $order = array("create_time desc");
        $collectModelAll = Collect::where(['uid' => $userId])->order($order)->select();
        $res = [];
        foreach ($collectModelAll as $model) {
            $res[] = Db::name($model['table'])->where(['id' => $model['aid']])->field('id,title,create_time,'.$model['type'].' as tab')->find();
        }
        $this->assign('res',$res);
        return $this->fetch();
    }
    /**
     * 获取更多数据
     */
    public function listMore(){

    }


}
