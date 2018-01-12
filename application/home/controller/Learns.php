<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/5/11
 * Time: 14:21
 */

namespace app\home\controller;
use app\home\model\Collect;
use app\home\model\Redbook;
use app\home\model\Redfilm;
use app\home\model\Redmusic;
use app\home\model\Redremark;
use app\home\model\WechatUser;
use think\Controller;
use app\admin\model\Question;
use app\home\model\Answers;
use app\home\model\Browse;
use app\home\model\Comment;
use app\admin\model\Picture;
use app\home\model\Learns as LearnsModel;
use app\home\model\Study as StudyModel;
use app\home\model\Like;
use think\Db;
use think\Request;

class Learns extends Base
{
    /**
     * 首页
     */
    public function index()
    {
        return $this->fetch();
    }
    /**
     * 推荐数据更多
     */
    public function moreDataList()
    {
        $len = input('length', 0);
        $res = Db::field("'study' as tab, id, '' as type, title, create_time, front_cover, publisher, status, recommend, views, comments, likes")
            ->table('pb_study')
            ->where('status>=0 and recommend=1')
            ->union("SELECT 'redfilm' as tab, id, '' as type, title, create_time, front_cover, '' as publisher, status, recommend, views, comments, likes FROM pb_redfilm where status>=0 and recommend=1")
            ->union("SELECT 'redbook' as tab, id, '' as type, title, create_time, '' as front_cover, '' as publisher, status, recommend, views, comments, likes FROM pb_redbook where status>=0 and recommend=1")
            ->union("SELECT 'redremark' as tab, id, '' as type, title, create_time, '' as front_cover, '' as publisher, status, recommend, 0 as views, 0 as comments, 0 as likes FROM pb_redremark where status>=0 and recommend=1")
            ->union("SELECT 'redmusic' as tab, id, '' as type, title, create_time, front_cover, '' as publisher, status, recommend, views, comments, likes FROM pb_redmusic where status>=0 and recommend=1")
            ->union("SELECT 'learns' as tab, id, type, title, create_time, front_cover, publisher, status, recommend, views, comments, likes FROM pb_learns where status>=0 and recommend=1 order by create_time desc limit $len,6")
            ->select();
        foreach ($res as $k=>$v) {
            if ($v['front_cover']) {
                $res[$k]['path'] = get_cover($v['front_cover'], 'path');
            } else {
                $res[$k]['path'] = '/home/images/1.jpg';
            }
            $res[$k]['time'] = date("Y-m-d",$v['create_time']);
            switch ($v['tab'])
            {
                case 'study':
                    $res[$k]['pre'] = "【基本书目】";
                    $res[$k]['url'] = "study/detail/id/".$v['id'];
                    break;
                case 'redfilm':
                    $res[$k]['pre'] = "【远程教育】";
                    $res[$k]['url'] = "education/movedetail/id/".$v['id'];
                    break;
                case 'redbook':
                    $res[$k]['pre'] = "【远程教育】";
                    $res[$k]['url'] = "education/bookdetail/id/".$v['id'];
                    break;
                case 'redremark':
                    $res[$k]['pre'] = "【远程教育】";
                    $res[$k]['url'] = "education/textdetail/id/".$v['id'];
                    break;
                case 'redmusic':
                    $res[$k]['pre'] = "【远程教育】";
                    $res[$k]['url'] = "education/musicdetail/id/".$v['id'];
                    break;
                case 'learns':
                    $res[$k]['pre'] = "【十九大专区】";
                    $res[$k]['url'] = $v['type']==1 ? "learns/video/id/".$v['id'] : "learns/article/id/".$v['id'];
                    break;
                default:
                    $res[$k]['url'] = "";
            }
        }
        if (empty($res)) {
            return $this->error('数据为空!');
        } else {
            return $this->success('数据获取成功!',null,$res);
        }
    }

    /**
     * 首页获取不同模块数据
     */
    public function lists($length = 0)
    {
        //不是get请求默认初始数据
        if (empty($length)) {
            $length = [];
            $length['study'] = 0;   // 基本书目
            $length['redfilm'] = 0;  // 红色电影
            $length['redbook'] = 0;  // 红色书籍
            $length['redremark'] = 0;  // 经典语录
            $length['redmusic'] = 0;  // 红色音乐
            $length['learns'] = 0;  // 十九大专区
        } else {
            $length = json_decode($length);
            $length['study'] = $length[0];
            $length['redfilm'] = $length[1];
            $length['redbook'] = $length[2];
            $length['redremark'] = $length[3];
            $length['redmusic'] = $length[4];
            $length['learns'] = $length[5];
        }
        //已取到的数据数
        $sumList = [0, 0, 0, 0, 0, 0];
        $data = $this->getLists($length, $sumList);
        var_dump($data);die;
        return $data;
    }
    /**
     *从各模块获取数据
     */
    public function getLists($length, $sumList, $more = 0, $data = []){
        $more = (int)$more;
        $study = new StudyModel();
        $redfilm = new Redfilm();
        $redbook = new Redbook();
        $redremark = new Redremark();
        $redmusic = new Redmusic();
        $learns = new LearnsModel();
        $studyList = [];
        $redfilmList = [];
        $redbookList = [];
        $redremarkList = [];
        $redmusicList = [];
        $learnsList = [];

        // 基本书目
        $studyList = $study->get_list($sumList[0]+$length['study'], 1);
        if (count($studyList) < 1)
        {
            $more = $more +  1 - count($studyList);
        } else {
            $data[] = count($studyList);
            $more = 0;
        }
        $sumList[0] += count($studyList);

        if ( count($data) >= 6 ) {
            return $data;
        }
        // 红色电影
        $redfilmList = $redfilm->get_list($sumList[1]+$length['redfilm'], 1 + $more);
        if (count($redfilmList) < (1 + $more)) {
            $more = $more + 1 - count($redfilmList);
        } else {
            $data[] = $redfilmList;
            $more = 0;
        }
        $sumList[1] += count($redfilmList);
        if ( count($data) >= 6 ) {
            return $data;
        }
        // 红色书籍
        $redbookList = $redbook->get_list($sumList[2]+$length['redbook'], 1 + $more);
        if (count($redbookList) < (1 + $more)) {
            $more = $more + 1 - count($redbookList);
        } else {
            $data[] = $redbookList;
            $more = 0;
        }
        $sumList[2] += count($redbookList);
        if ( count($data) >= 6 ) {
            return $data;
        }
        // 经典语录
        $redremarkList = $redremark->get_list($sumList[3]+$length['redremark'], 1 + $more);
        if (count($redremarkList) < (1 + $more)) {
            $more = $more + 1 - count($redremarkList);
        } else {
            $data[] = $redremarkList;
            $more = 0;
        }
        $sumList[3] += count($redremarkList);
        if ( count($data) >= 6 ) {
            return $data;
        }
        // 红色音乐
        $redmusicList = $redmusic->get_list($sumList[4]+$length['redmusic'], 1 + $more);
        if (count($redmusicList) < (1 + $more)) {
            $more = $more + 1 - count($redmusicList);
        } else {
            $data[] = $redmusicList;
            $more = 0;
        }
        $sumList[4] += count($redmusicList);
        if ( count($data) >= 6 ) {
            return $data;
        }
        // 十九大专区
        $learnsList = $learns->get_list($sumList[5]+$length['learns'], 1 + $more);
        if (count($learnsList) < (1 + $more)) {
            $more = $more + 1 - count($learnsList);
        } else {
            $data[] = $learnsList;
            $more = 0;
        }
        $sumList[5] += count($learnsList);
        if ( count($data) >= 6 ) {
            return $data;
        }
        $this->getLists($length, $sumList, $more, $data);
    }

    /**
     * 推荐列表加载更多
     */
    public function more(){
        $length = input('get.length');
        $details = $this ->lists($length);
        foreach($details as $key => $value) {
            foreach ($value as $k => $v) {
                if ($v['front_cover']) {
                    $v['front_cover'] = get_cover($v['front_cover'], 'path');
                } else {
                    $v['front_cover'] = '/home/images/1.jpg';
                }
                $v['time'] = date('Y-m-d',$v['create_time']);
            }
        }
        return $details;
    }
    /**
     * 十九大专题
     */
    public function meeting(){
        $this->anonymous();
        //数据列表
        $map = [
            'type' => ['in',[1,2]],
            'status' => ['egt',0],
        ];
        $list = LearnsModel::where($map)->limit(5)->order('id desc')->select();
        foreach($list as $value){
            if ($value['create_time'] >= strtotime(date('Y-m-d'))) {
                $value['time'] = date("H:i",$value['create_time']);
            }else{
                if ($value['create_time'] >= strtotime(date("Y-m-d",strtotime("-1 day")))) {
                    $value['time'] = date("昨天 H:i",$value['create_time']);
                }else{
                    $value['time'] = date("Y-n-j H:i",$value['create_time']);
                }
            }
        }
        $this->assign('list',$list);
        return $this->fetch();
    }
    /**
     * 主页加载更多
     */
    public function indexmore(){
        $len = input('length');
        $map = [
            'type' => ['in',[1,2]],
            'status' => ['egt',0],
        ];
        $list = LearnsModel::where($map)->order('id desc')->limit($len,3)->select();
        foreach($list as $value){
            $img = Picture::get($value['front_cover']);
            $value['path'] = $img['path'];
            if ($value['create_time'] >= strtotime(date('Y-m-d'))) {
                $value['time'] = date("H:i",$value['create_time']);
            }else{
                if ($value['create_time'] >= strtotime(date("Y-m-d",strtotime("-1 day")))) {
                    $value['time'] = date("昨天 H:i",$value['create_time']);
                }else{
                    $value['time'] = date("Y-n-j H:i",$value['create_time']);
                }
            }
        }
        if($list){
            return $this->success("加载成功",'',$list);
        }else{
            return $this->error("加载失败");
        }
    }
    /**
     * 视频课程
     */
    public function video(){
        $this->anonymous();        //判断是否是游客
        $this->jssdk();

        $userId = session('userId');
        $id = input('id');
        $learnsModel = new LearnsModel();
        //浏览加一
        $info['views'] = ['exp','`views`+1'];
        $learnsModel::where('id',$id)->update($info);
        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = [
                'user_id' => $userId,
                'learns_id' => $id,
            ];
            $history = Browse::get($con);
            if(!$history && $id != 0){
                $s['score'] = ['exp','`score`+1'];
//                if ($this->score_up()){
                    // 未满 15分
                    Browse::create($con);
                    WechatUser::where('userid',$userId)->update($s);
//                }
            }
        }
        $video = $learnsModel::get($id);
        $video['user'] = session('userId');
        //分享图片及链接及描述
        $image = Picture::where('id',$video['front_cover'])->find();
        $video['share_image'] = "http://".$_SERVER['SERVER_NAME'].$image['path'];
        $video['link'] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'];
        $video['desc'] = str_replace('&nbsp;','',strip_tags($video['content']));
        $video['desc'] = str_replace(" ",'',$video['desc']);
        $video['desc'] = str_replace("\n",'',$video['desc']);

        //获取 文章点赞
        $likeModel = new Like;
        $like = $likeModel->getLike(1,$id,$userId);
        $video['is_like'] = $like;
        //获取 收藏
        $collectModel = new Collect();
        $collect = $collectModel->getCollect(1,$id,$userId);
        $video['is_collect'] = $collect;
        $this->assign('video',$video);
        //获取 评论
        $commentModel = new Comment();
        $comment = $commentModel->getComment(1,$id,$userId);
        $this->assign('comment',$comment);
        return $this->fetch();
    }

    /**
     * 图文课程
     */
    public function article(){
        $this->anonymous();        //判断是否是游客
        $this->jssdk();

        $userId = session('userId');
        $id = input('id');
        $learnsModel = new LearnsModel();
        //浏览加一
        $info['views'] = ['exp','`views`+1'];
        $learnsModel::where('id',$id)->update($info);
        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = [
                'user_id' => $userId,
                'learns_id' => $id,
            ];
            $history = Browse::get($con);
            if(!$history && $id != 0){
                $s['score'] = ['exp','`score`+1'];
//                if ($this->score_up()){
                    // 未满 15 分
                    Browse::create($con);
                    WechatUser::where('userid',$userId)->update($s);
//                }
            }
        }
        $article = $learnsModel::get($id);
        $article['user'] = session('userId');
        //分享图片及链接及描述
        $image = Picture::where('id',$article['front_cover'])->find();
        $article['share_image'] = "http://".$_SERVER['SERVER_NAME'].$image['path'];
        $article['link'] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'];
        $article['desc'] = str_replace('&nbsp;','',strip_tags($article['content']));
        $article['desc'] = str_replace(" ",'',$article['desc']);
        $article['desc'] = str_replace("\n",'',$article['desc']);

        //获取 文章点赞
        $likeModel = new Like;
        $like = $likeModel->getLike(1,$id,$userId);
        $article['is_like'] = $like;
        //获取 收藏
        $collectModel = new Collect();
        $collect = $collectModel->getCollect(1,$id,$userId);
        $article['is_collect'] = $collect;
        $this->assign('article',$article);
        //获取 评论
        $commentModel = new Comment();
        $comment = $commentModel->getComment(1,$id,$userId);
        $this->assign('comment',$comment);

        return $this->fetch();
    }
}