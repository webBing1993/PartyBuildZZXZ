<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/4/21
 * Time: 15:58
 */

namespace app\home\controller;

use app\user\controller\Index;
use app\home\model\Collect;
use app\home\model\Comment;
use app\home\model\Learn;
use app\home\model\Like;
use app\user\model\WechatUser;
use think\Config;
use think\Controller;
use com\wechat\TPQYWechat;
use think\Cache;
use think\Input;
use think\Log;

class Base extends Controller {
    public function _initialize(){
        session('userId','lb');
//        session('header','/home/images/vistor.jpg');
//        session('nickname','游客');
        if(!empty($_SERVER['REQUEST_URI'])){
            session('url',$_SERVER['REQUEST_URI']);
        }
        $userId = session('userId');

        if(Config::get('WEB_SITE_CLOSE')){
            return $this->error('站点维护中，请稍后访问~');
        }

        //如果是游客的话默认userid为visitors
        if($userId == 'visitor'){
            session('nickname','游客');
            session('header','/home/images/vistor.jpg');
        }else{
            //微信认证
            $Wechat = new TPQYWechat(Config::get('party'));
            // 1用户认证是否登陆
            if(empty($userId)) {
                $redirect_uri = Config::get("party.login");
                $url = $Wechat->getOauthRedirect($redirect_uri);
                $this->redirect($url);
            }

            // 2获取jsapi_ticket
            $jsApiTicket = session('jsapiticket');
            if(empty($jsApiTicket) || $jsApiTicket=='') {
                session('jsapiticket', $Wechat->getJsTicket()); // 官方7200,设置7000防止误差
            }
        }
    }

    public function setComment() {
        $comment = new Comment();
        $res = $comment->addComment($this->moduleName,1);
        return $res;

    }
   
    /**
     * 微信官方认证URL
     */
    public function oauth(){
        $Wechat = new TPQYWechat(Config::get('party'));
        $Wechat->valid();
    }

    /**
     * 判断是否是游客登录
     */
    public function anonymous() {
        $userId = session('userId');
        //如果userId等于visitor  则为游客登录，否则则正常显示
        if($userId == 'visitor'){
            $this->assign('visit', 1);
        }else{
            $this->assign('visit', 0);
        }
    }

    /**
     * 获取企业号签名
     */
    public function jssdk(){
        $Wechat = new TPQYWechat(Config::get('party'));
        $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $jsSign = $Wechat->getJsSign($url);
        $this->assign("jsSign", $jsSign);
    }

    /**
     * 默认图片
     * $front_pic 封面图片id：1-10
     * $list_pic 列表图片id：35-44
     * $carousel_pic 轮播图片id: 45-54
     */
    public function default_pic(){
        //随机轮播图
        $c = array('1'=>'a','2'=>'b','3'=>'c','4'=>'d','5'=>'e','6'=>'f','7'=>'g','8'=>'h','9'=>'i','10'=>'j','11'=>'k','12'=>'l','13'=>'m','14'=>'n','15'=>'o',
            '16'=>'p','17'=>'q','18'=>'r','19'=>'s','20'=>'t','21'=>'u','22'=>'v','23'=>'w','24'=>'x','25'=>'y','26'=>'z');
        $carousel_pic1 = array_rand($c,1);
        $this->assign('p1',$carousel_pic1);
        $carousel_pic2 = array_rand($c,1);
        $this->assign('p2',$carousel_pic2);
        $carousel_pic3 = array_rand($c,1);
        $this->assign('p3',$carousel_pic3);

    }
    /**
     * 用户登入获取信息(登陆方法在index控制器中)
     */
    public function login(){
        // 获取用户信息
        $Wechat = new TPQYWechat(Config::get('party'));
        $result = $Wechat->getUserId(input('code'), Config::get('party.agentid'));
        if(isset($result['UserId'])) {
            $user = $Wechat->getUserInfo($result['UserId']);

            // 添加本地数据
            $UserAPI = new Index();
            $localUser = $UserAPI->checkWechatUser($result['UserId']);
            if($localUser) {
                $UserAPI->updateWechatUser($user);
            } else {
                $UserAPI->addWechatUser($user);
            }

            session("userId", $result['UserId']);
            session("nickname", isset($user['nickname'])?:"");
            session("header", isset($user['header'])?:"");

            $this->redirect("News/index");
        } else {
            return $this->error("企业成员未授权");
        }
    }
    /**
     * 点赞，type=1 文章点赞  type=2 评论点赞
     * class：1 **** ，2 支部活动 ，3 两学一做，4 ****
     */
    public function like(){
        $type = input('type'); //获取点赞类型
        $userId = session('userId'); //点赞人
        $likeModel = new Like();
        $comment_id = input('comment_id') ? input('comment_id') : 0;
        $user_id = input('user_id') ? input('user_id') : ""; //被点赞
        $data['class'] = input('class');
        if($data['class'] == 1) {
            $news_id = input('id');
            $notice_id = 0;
            $learn_id = 0;
            $opinion_id = 0;
        }
        if($data['class'] == 2) {
            $news_id = 0;
            $notice_id = input('id');
            $learn_id = 0;
            $opinion_id = 0;
        }
        if($data['class'] == 3) {
            $news_id = 0;
            $notice_id = 0;
            $learn_id = input('id');
            $opinion_id = 0;
        }
        if($data['class'] == 4) {
            $news_id = 0;
            $notice_id = 0;
            $learn_id = 0;
            $opinion_id = input('id');
        }
        $data['news_id'] = $news_id;
        $data['notice_id'] = $notice_id;
        $data['learn_id'] = $learn_id;
        $data['opinion_id'] = $opinion_id;
        $data['type'] = $type;
        $data['user_id'] = $user_id;
        $data['comment_id'] = $comment_id;
        $data['create_user'] = $userId;

        //是否存在点赞表
        $like = $likeModel::where($data)->find();
        if(empty($like)){
            $model = Like::create($data);
            if($model){
                if($type == 1) {    //文章点赞
                    //点赞成功积分+2
                    $score['score'] = array('exp','`score`+2');
                    WechatUser::where('userid',$userId)->update($score);

//                    if($news_id) {
//                        //点赞次数+1
//                        $info['likes'] = array('exp','`likes`+1');
//                        NewsModel::where('id',$news_id)->update($info);
//                    }
//                    if($notice_id) {
//                        //点赞次数+1
//                        $info['likes'] = array('exp','`likes`+1');
//                        Notice::where('id',$notice_id)->update($info);
//                    }
                    if($learn_id) {
                        //点赞次数+1
                        $info['likes'] = array('exp','`likes`+1');
                        Learn::where('id',$learn_id)->update($info);
                    }
//                    if($opinion_id) {
//                        $info['likes'] = array('exp','`likes`+1');
//                        Opinion::where('id',$opinion_id)->update($info);
//                    }
                }else{
                    //点赞成功积分+2
                    $score['score'] = array('exp','`score`+2');
                    WechatUser::where('userid',$userId)->update($score);

                    //点赞次数+1
                    $info['likes'] = array('exp','`likes`+1');
                    Comment::where('id',$comment_id)->update($info);
                }
                return $this->success('点赞成功','',1);
            }else{
                return $this->error($model->getError());
            }
        }else{
            $result = $likeModel::where($data)->delete();
            if($result){
                if($type == 1) {
                    //点赞成功积分+2
                    $score['score'] = array('exp','`score`-2');
                    WechatUser::where('userid',$userId)->update($score);

//                    if($news_id) {
//                        //点赞次数+1
//                        $info['likes'] = array('exp','`likes`-1');
//                        NewsModel::where('id',$news_id)->update($info);
//                    }
//                    if($notice_id) {
//                        //点赞次数+1
//                        $info['likes'] = array('exp','`likes`-1');
//                        Notice::where('id',$notice_id)->update($info);
//                    }
                    if($learn_id) {
                        //点赞次数+1
                        $info['likes'] = array('exp','`likes`-1');
                        Learn::where('id',$learn_id)->update($info);
                    }
//                    if($opinion_id) {
//                        $info['likes'] = array('exp','`likes`-1');
//                        Opinion::where('id',$opinion_id)->update($info);
//                    }
                }else{
                    //点赞成功积分+2
                    $score['score'] = array('exp','`score`-2');
                    WechatUser::where('userid',$userId)->update($score);

                    //点赞次数+1
                    $info['likes'] = array('exp','`likes`-1');
                    Comment::where('id',$comment_id)->update($info);
                }
                return $this->success('取消成功','',0);
            }else{
                return $this->error('取消失败');
            }
        }
    }
    /**
     * 收藏
     */
    public function collect(){
        //执行收藏操作
        $userId = session('userId');
        $collectModel = new Collect();
        $id = input('id');
        $data['type'] = input('type');
        if($data['type'] == 1) {
            $data['news_id'] = $id;
        }elseif($data['type'] == 2){
            $data['notice_id'] = $id;
        }elseif($data['type'] == 3){
            $data['learn_id'] = $id;
        }elseif($data['type'] == 4) {
            $data['film_id'] = $id;
        }elseif($data['type'] == 5) {
            $data['music_id'] = $id;
        }else{
            $data['book_id'] = $id;
        }
        $data['create_user'] = $userId;
        $collect = $collectModel::where($data)->find();
        if(empty($collect)){
            $model = $collectModel->create($data);
            if($model){
                if($data['type'] == 1) {
                    //第一聚焦新增收藏collect字段+1
                    $info['collect'] = array('exp','`collect`+1');
//                    $msg = NewsModel::where('id',$id)->update($info);
                }elseif($data['type'] == 2){
                    //支部活动新增收藏collect字段+1
                    $info['collect'] = array('exp','`collect`+1');
//                    $msg = Notice::where('id',$id)->update($info);
                }elseif($data['type'] == 3){
                    //两学一做新增收藏collect字段+1
                    $info['collect'] = array('exp','`collect`+1');
                    $msg = Learn::where('id',$id)->update($info);
                }elseif($data['type'] == 4) {
                    $info['collect'] = array('exp','`collect`+1');
//                    $msg = Redfilm::where('id',$id)->update($info);
                }elseif($data['type'] == 5) {
                    $info['collect'] = array('exp','`collect`+1');
//                    $msg = Redmusic::where('id',$id)->update($info);
                }else{
                    $info['collect'] = array('exp','`collect`+1');
//                    $msg = Redbook::where('id',$id)->update($info);
                }
                if($msg){
                    return $this->success('收藏成功', Cookie('__forward__'), 1);
                }else{
                    return $this->error('收藏失败', Cookie('__forward__'), false);
                }
            }else{
                //验证错误getError()
                return $this->error($collectModel->getError());
            }
        }else{
            if($collect['status'] == -1){
                $map['status'] = 0;
                $collectModel::where($data)->update($map);
                if($data['type'] == 1) {
                    //第一聚焦新增收藏collect字段+1
                    $info['collect'] = array('exp','`collect`+1');
//                    $msg = NewsModel::where('id',$id)->update($info);
                }elseif($data['type'] == 2){
                    //支部活动新增收藏collect字段+1
                    $info['collect'] = array('exp','`collect`+1');
//                    $msg = Notice::where('id',$id)->update($info);
                }elseif($data['type'] == 3){
                    //两学一做新增收藏collect字段+1
                    $info['collect'] = array('exp','`collect`+1');
                    $msg = Learn::where('id',$id)->update($info);
                }elseif($data['type'] == 4) {
                    $info['collect'] = array('exp','`collect`+1');
//                    $msg = Redfilm::where('id',$id)->update($info);
                }elseif($data['type'] == 5) {
                    $info['collect'] = array('exp','`collect`+1');
//                    $msg = Redmusic::where('id',$id)->update($info);
                }else{
                    $info['collect'] = array('exp','`collect`+1');
//                    $msg = Redbook::where('id',$id)->update($info);
                }
                if($msg){
                    return $this->success('收藏成功', Cookie('__forward__'), 1);
                }else{
                    return $this->error('收藏失败', Cookie('__forward__'), false);
                }
            }else{
                //存在执行该操作则为取消收藏
                $result = $collectModel::where($data)->delete();
                if($result) {
                    if($data['type'] == 1) {
                        //第一聚焦取消收藏collect字段-1
                        $info['collect'] = array('exp','`collect`-1');
//                        $msg = NewsModel::where('id',$id)->update($info);
                    }elseif($data['type'] == 2){
                        //支部活动取消收藏collect字段-1
                        $info['collect'] = array('exp','`collect`-1');
//                        $msg = Notice::where('id',$id)->update($info);
                    }elseif($data['type'] == 3){
                        //两学一做取消收藏collect字段-1
                        $info['collect'] = array('exp','`collect`-1');
                        $msg = Learn::where('id',$id)->update($info);
                    }elseif($data['type'] == 4) {
                        $info['collect'] = array('exp','`collect`-1');
//                        $msg = Redfilm::where('id',$id)->update($info);
                    }elseif($data['type'] == 5) {
                        $info['collect'] = array('exp','`collect`-1');
//                        $msg = Redmusic::where('id',$id)->update($info);
                    }else{
                        $info['collect'] = array('exp','`collect`-1');
//                        $msg = Redbook::where('id',$id)->update($info);
                    }
                    if($msg){
                        return $this->success('取消收藏成功', Cookie('__forward__'), 0);
                    }else{
                        return $this->error('取消收藏失败',Cookie('__forward__'), false);
                    }
                } else {
                    return $this->error($collectModel->getError());
                }
            }
        }
    }
    /**
     * 添加评论
     */
    public function comment(){
        if(IS_POST){
            $userId = session('userId');
            $newsId = input('news_id') ? input('news_id') : 0;
            $noticeId = input('notice_id') ? input('notice_id') : 0;
            $learnId = input('learn_id') ? input('learn_id') : 0;
            $opinionId = input('opinion_id') ? input('opinion_id') : 0;
            $filmId = input('film_id') ? input('film_id') : 0;
            $musicId = input('music_id') ? input('music_id') : 0;
            $bookId = input('book_id') ? input('book_id') : 0;

            //重组数据
            $commentModel = new Comment();
            $data = array(
                'content' => input('content'),
                'news_id' => $newsId,
                'notice_id' => $noticeId,
                'learn_id' => $learnId,
                'opinion_id' => $opinionId,
                'film_id' => $filmId,
                'music_id' => $musicId,
                'book_id' => $bookId,
                'create_user' => $userId,
            );
            if($newsId) { $data['type'] = 1;}
            if($noticeId) { $data['type'] = 2;}
            if($learnId) { $data['type'] = 3;}
            if($opinionId) { $data['type'] = 4;}
            if($filmId) { $data['type'] = 5;}
            if($musicId) { $data['type'] = 6;}
            if($bookId) { $data['type'] = 7;}
            $model = $commentModel->create($data);
            if ($model) {
                $id = $model['id'];
                if ($id) {
                    //评论成功增加三分
                    $score['score'] = array('exp','`score`+3');
                    WechatUser::where('userid',$userId)->update($score);

                    //更新主表评论数
                    $map['comments'] = array('exp', '`comments`+1');
//                    if ($newsId) NewsModel::where('id', $newsId)->update($map);
//                    if ($noticeId) Notice::where('id', $noticeId)->update($map);
                    if ($learnId) Learn::where('id',$learnId)->update($map);
//                    if($opinionId) Opinion::where('id',$opinionId)->update($map);
//                    if($filmId) Redfilm::where('id',$filmId)->update($map);
//                    if($musicId) Redmusic::where('id',$musicId)->update($map);
//                    if($bookId) Redbook::where('id',$bookId)->update($map);

                    //获取用户头像和昵称
                    $user = WechatUser::where('userid',$userId)->find();
                    $nickname = ($user['nickname']) ? $user['nickname'] : $user['name'];
                    $header =  ($user['header']) ? $user['header'] : $user['avatar'];
                    //返回用户数据
                    $jsonData = array(
                        'time' => date("Y-m-d",time()),
                        'status' => 1,
                        'content' => input('content'),
                        'nickname' => $nickname,
                        'header' => $header,
                        'id' => $id,
                        'news_id' => $model['news_id'],
                        'notice_id' => $model['notice_id'],
                        'learn_id' => $model['learn_id'],
                        'opinion_id' => $model['opinion_id'],
                        'film_id' => $model['film_id'],
                        'music_id' => $model['music_id'],
                        'book_id' => $model['book_id'],
                        'create_time' => time(),
                        'likes' => 0,
                        'is_like' => 0,
                        'create_user' => $userId,
                        'self' => 1,
                    );

                    return $this->success('评论成功','', $jsonData);
                } else {
                    return $this->error('评论失败');
                }
            } else {
                return $this->error($commentModel->getError());
            }
        }
    }
    /**
     * 加载更多评论
     */
    public function morecontent(){
        $news_id = input('news_id') ? input('news_id') : 0;
        $notice_id = input('notice_id') ? input('notice_id') : 0;
        $learn_id = input('learn_id') ? input('learn_id') : 0;
        $opinion_id = input('opinion_id') ? input('opinion_id') : 0;
        $film_id = input('film_id') ? input('film_id') : 0;
        $music_id = input('music_id') ? input('music_id') : 0;
        $book_id = input('book_id') ? input('book_id') : 0;
        $userId = input('userId');
        $length = input('length');
        if($news_id) {
            $map = array(
                'news_id' => $news_id,
                'status' => 0
            );
        }
        if($notice_id) {
            $map = array(
                'notice_id' => $notice_id,
                'status' => 0
            );
        }
        if($learn_id) {
            $map = array(
                'learn_id' => $learn_id,
                'status' => 0
            );
        }
        if($opinion_id) {
            $map = array(
                'opinion_id' => $opinion_id,
                'status' => 0
            );
        }
        if($film_id) {
            $map = array(
                'film_id' => $film_id,
                'status' => 0
            );
        }if($music_id) {
            $map = array(
                'music_id' => $music_id,
                'status' => 0
            );
        }if($book_id) {
            $map = array(
                'book_id' => $book_id,
                'status' => 0
            );
        }

        //敏感词屏蔽
        $badword = array(
            '法轮功','法轮','FLG','六四','6.4','flg'
        );
        $badword1 = array_combine($badword,array_fill(0,count($badword),'***'));
        $comment = Comment::where($map)->limit($length,5)->order('likes desc,id desc')->select();
        if($comment) {
            foreach($comment as $value){
                $content = $value['content'];
                $str = strtr($content, $badword1);
                $value['content'] = $str;
                if($value['create_user'] == $userId){
                    $value['self'] = 1;
                }else{
                    $value['self'] = 0;
                }
                $map1 = array(
                    'create_user' => $userId,
                    'comment_id' => $value['id'],
                    'status' => array('egt',0),
                    'type' => 2,
                );

                $like = Like::where($map1)->find();
                ($like)?$value['is_like'] = 1:$value['is_like'] = 0;
                $users = WechatUser::where('userid',$value['create_user'])->find();
                $value['header'] = ($users['header']) ? $users['header'] : $users['avatar'];
                $value['nickname'] = ($users['nickname']) ? $users['nickname'] : $users['name'];
                $value['time'] = date("Y-m-d",$value['create_time']);
            }
            return $this->success("加载成功","",$comment);
        }else{
            return $this->error("没有更多");
        }
    }
}