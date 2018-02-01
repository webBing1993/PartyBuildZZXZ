<?php
/**
 * Created by PhpStorm.
 * User: daire
 * Date: 2018/1/8
 * Time: 13:06
 */

namespace app\home\controller;
use app\home\model\Browse;
use app\home\model\Comment;
use app\home\model\Like;
use app\home\model\Picture;
use app\home\model\WechatDepartmentUser;
use app\home\model\WechatUser;
use app\home\model\WechatDepartment;
use app\home\model\WechatUserTag;
use app\home\model\Meet;
use think\Db;

class Service extends Base
{
    static $MEMBER_TAG = 3; // 党员标签ID
    static $DEFAUL_AVATAR = ''; // 用户默认头像

    /**
     * 党员管理
     */
    public function manage(){
        $this->anonymous();
        //数据列表
        $map = [
            'status' => ['gt',0],
        ];
        $list = Meet::where($map)->limit(10)->order('id desc')->select();
        $this->assign('list',$list);

        // 党员情况

        $dLists = WechatDepartment::where(['parentid'=>1])->select(); // 部门列表
        $this->assign('dList',$dLists);

        return $this->fetch();
    }

    /**
     *  获取部门党员列表
     */
    public function getDepartmentMember($did)
    {
        if (empty($did)) {

            $this->error('部门参数错输！');
        } else {
            $list = Db::table('pb_wechat_user')
                ->alias('a')
                ->join('pb_wechat_department_user b','a.userid = b.userid')
                ->join('pb_wechat_user_tag c','a.userid = c.userid')
//                ->join('pb_wechat_department d','b.departmentid = d.id')
                ->field('a.department_short as dname,a.userid,a.name,a.mobile,a.avatar,a.header,a.gender,a.birthday,a.partytime,a.position')
                ->where(['departmentid'=>$did,'tagid'=>$this::$MEMBER_TAG])
                ->select();

            $charArray = [];//将姓名按照姓的首字母与相对的首字母键进行配对
            foreach ($list as $user) {
                $name = $user['name'];
                if (!empty($user['header'])) { // 获取用户头像

                    $img = Picture::get($user['header']);
                    $user['header'] = $img['path'];
                } else if (empty($user['avatar'])) { // 无头像设置默认

                    $user['header'] = $this::$DEFAUL_AVATAR;
                }

                $char = $this->getFirstChar($name);
                $nameArray = [];

                if (isset($charArray[$char]) && count($charArray[$char])!=0) {
                    $nameArray = $charArray[$char];
                }
                array_push($nameArray,$user);
                $charArray[$char] = $nameArray;
            }
            ksort($charArray);
//            return json_encode($charArray);
            return $this->success('获取成功！',null,json_encode($charArray));
        }
    }

    /**
     * 首字符排序
     * @param $s 用户姓名
     * @return bool|string
     */
    protected function getFirstChar($s)
    {
        $s0 = mb_substr($s,0,3); // 获取名字的姓
        $s = iconv('UTF-8','gb2312', $s0); // 将UTF-8转换成GB2312编码
        if (ord($s0)>128) { // 汉字开头，汉字没有以U、V开头的
            $asc=ord($s{0})*256+ord($s{1})-65536;
            if($asc>=-20319 and $asc<=-20284)return "A";
            if($asc>=-20283 and $asc<=-19776)return "B";
            if($asc>=-19775 and $asc<=-19219)return "C";
            if($asc>=-19218 and $asc<=-18711)return "D";
            if($asc>=-18710 and $asc<=-18527)return "E";
            if($asc>=-18526 and $asc<=-18240)return "F";
            if($asc>=-18239 and $asc<=-17760)return "G";
            if($asc>=-17759 and $asc<=-17248)return "H";
            if($asc>=-17247 and $asc<=-17418)return "I";
            if($asc>=-17417 and $asc<=-16475)return "J";
            if($asc>=-16474 and $asc<=-16213)return "K";
            if($asc>=-16212 and $asc<=-15641)return "L";
            if($asc>=-15640 and $asc<=-15166)return "M";
            if($asc>=-15165 and $asc<=-14923)return "N";
            if($asc>=-14922 and $asc<=-14915)return "O";
            if($asc>=-14914 and $asc<=-14631)return "P";
            if($asc>=-14630 and $asc<=-14150)return "Q";
            if($asc>=-14149 and $asc<=-14091)return "R";
            if($asc>=-14090 and $asc<=-13319)return "S";
            if($asc>=-13318 and $asc<=-12839)return "T";
            if($asc>=-12838 and $asc<=-12557)return "W";
            if($asc>=-12556 and $asc<=-11848)return "X";
            if($asc>=-11847 and $asc<=-11056)return "Y";
            if($asc>=-11055 and $asc<=-10247)return "Z";
        } else if (ord($s)>=48 and ord($s)<=57) { // 数字开头
            switch(iconv_substr($s,0,1,'utf-8')){
                case 1:return "Y";
                case 2:return "E";
                case 3:return "S";
                case 4:return "S";
                case 5:return "W";
                case 6:return "L";
                case 7:return "Q";
                case 8:return "B";
                case 9:return "J";
                case 0:return "L";
            }
        } else if (ord($s)>=65 and ord($s)<=90) { // 大写英文开头

            return substr($s,0,1);
        } else if (ord($s)>=97 and ord($s)<=122) { // 小写英文开头

            return strtoupper(substr($s,0,1));
        } else { // 中英混合的词语，不适合上面的各种情况，因此直接提取首个字符即可

            return iconv_substr($s0,0,1,'utf-8');
        }
    }

    /**
     * 党员单人搜索
     */
    public function getMember($search)
    {
        if (empty($search)) {

            $this->error('参数错输！');
        } else {
            $list = Db::table('pb_wechat_user')
                ->alias('a')
                ->join('pb_wechat_department_user b','a.userid = b.userid','left')
                ->join('pb_wechat_user_tag c','a.userid = c.userid','left')
                //->join('pb_wechat_department d','b.departmentid = d.id','left')
                ->field('a.department_short as dname,a.userid,a.name,a.mobile,a.avatar,a.header,a.gender,a.birthday,a.partytime,a.position')
                ->where(['a.name'=>['like',"%$search%"],'tagid'=>$this::$MEMBER_TAG])
                ->select();

            foreach ($list as $user) {
                if (!empty($user['header'])) { // 获取用户头像

                    $img = Picture::get($user['header']);
                    $user['header'] = $img['path'];
                } else if (empty($user['avatar'])) { // 无头像设置默认

                    $user['header'] = $this::$DEFAUL_AVATAR;
                }
            }

            return $this->success('搜索获取成功！',null,json_encode($list));
        }

    }
    public function search(){
        return $this->fetch();
    }
    public function detail(){
        return $this->fetch();
    }

    /**
     * 参会情况加载更多
     */
    public function indexmore(){
        $len = input('length');
        $map = [
            'status' => ['gt',0],
        ];
        $list = Meet::where($map)->order('id desc')->limit($len,6)->select();
        foreach($list as $value){
            $img = Picture::get($value['front_cover']);
            $value['path'] = $img['path'];
            $value['time'] = date("Y-m-d",$value['create_time']);
        }
        if($list){
            return $this->success("加载成功",'',$list);
        }else{
            return $this->error("加载失败");
        }
    }
    /**
     * 详情页
     */
    public function meetdetail(){
        $this->anonymous();        //判断是否是游客
        $this->jssdk();

        $userId = session('userId');
        $id = input('id');
        $meetModel = new Meet();
        //浏览加一
        $info['views'] = ['exp','`views`+1'];
        $meetModel::where('id',$id)->update($info);
        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = [
                'user_id' => $userId,
                'meet_id' => $id,
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
        $article = $meetModel::get($id);
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
        $like = $likeModel->getLike(7,$id,$userId);
        $article['is_like'] = $like;
        $this->assign('article',$article);
        //获取 评论
        $commentModel = new Comment();
        $comment = $commentModel->getComment(7,$id,$userId);
        $this->assign('comment',$comment);

        return $this->fetch();
    }


}