<?php
/**
 * Created by PhpStorm.
 * User: sitff
 * Date: 2017/8/26
 * Time: 上午9:52
 */
namespace app\home\controller;
use app\home\model\Like;
use app\home\model\Comment;
use app\user\model\WechatUser;
use app\home\model\Browse;

class Live extends Base
{
    /**
     *
     * @return mixed
     */
    public function index()
    {

        $this->anonymous();

        $userId = session('userId');
        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = array(
                'user_id' => $userId,
                'live_id' => 1,
                'create_time' => ['egt',strtotime(date('Y-m-d'))]
            );
            $history = Browse::get($con);
            if(!$history){
//                $s['score'] = array('exp','`score`+1');
//                if ($this->score_up()){
//                    // 未超过 15分
//                    WechatUser::where('userid',$userId)->update($s);
//                    unset($con['create_time']);
//                    Browse::create($con);
//                }
            }

        }

        $sum = WechatUser::count();
        $this->assign('live_path',config('live_path'));
        $this->assign('live_sum',$sum);

        return $this->fetch();
    }

    /**
     * 获取在线人数
     */
    public function getOnlineNumber()
    {
        //浏览不存在则存入pb_browse表
        $con = array(
            'live_id' => 1,
            'create_time' => ['egt',strtotime(date('Y-m-d'))]
        );
        $sum = Browse::where($con)->count();

        return $this->success('获取成功!',null,$sum);
    }


    /**
     * 获取实时评论
     * @return array|void
     */
    public function getComment()
    {
        $id = input('id')?input('id'):0;
        $uid = session('userId');
        //获取 评论
        $commentModel = new Comment();
        $map = array(
            'type' => 7,
            'aid' => 1,
            'status' => 0,
            'create_time' => ['egt',strtotime(date('Y-m-d'))],
            'id' => ['gt',$id]
        );

        $comment = $commentModel->where($map)->order('create_time desc')->limit(7)->select();
        //敏感词屏蔽
        $badword = array(
            '法轮功','法轮','FLG','六四','6.4','flg'
        );
        $badword1 = array_combine($badword,array_fill(0,count($badword),'***'));

        if($comment) {
            foreach ($comment as $value) {
                $user = WechatUser::where('userid',$value['uid'])->find();
                $value['nickname'] = $user['name'];
                $value['header'] = ($user['header']) ? $user['header'] : $user['avatar'];
                $value['time'] = date('H:i:s',$value['create_time']);
                $value['content'] = strtr($value['content'], $badword1);
            }
            return $this->success("加载成功","",$comment);
        }else{
            return $this->error("没有更多");
        }

    }


}