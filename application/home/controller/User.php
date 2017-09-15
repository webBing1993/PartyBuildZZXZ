<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/4/20
 * Time: 15:34
 */

namespace app\home\controller;
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

        if ($userId !== 'visitor') {

            $dp = Db::table('pb_wechat_department_user')
                ->alias('a')
                ->join('pb_wechat_department b','a.departmentid = b.id','LEFT')
                ->where('a.userid',$userId)
                ->find();
            //个人信息
            $personal = WechatUser::where('userid',$userId)->find();
            $personal['dpname'] = $dp['name'];
            //总榜
            $con['score'] = array('neq',0);
            $all  = WechatUser::where($con)->order('score desc')->select();
            foreach ($all as $k => $value){
                if($value['userid'] == $userId){
                    $personal['rank'] = $k+1;
                }
            }
            $this->assign('personal',$personal);
        }

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



}
