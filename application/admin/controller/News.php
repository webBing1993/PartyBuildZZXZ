<?php
/**
 * 党建动态
 * Created by PhpStorm.
 * User: sitff
 * Date: 2017/8/21
 * Time: 下午2:59
 */
namespace app\admin\controller;
use app\admin\model\News as NewsModel;
use app\admin\model\Picture;
use com\wechat\TPQYWechat;
use app\admin\model\Push;

class News extends Admin
{
    /**
     * 首页
     * @param $type
     */
    public function index($table_type,$type)
    {
        $map = [
            'table_type' => $table_type, // 党建动态
            'type' => $type,
            'status' => ['egt',0]
        ];

        $list = $this->lists('News',$map);
        int_to_string($list,array(
            'status' => [0=>"已发布"],
            'type' => [
                1 => "新闻聚焦",
                2 => "各地动态",
                3 => "主题党日活动",
                4 => "双报到",
                5 => "党员先锋活动",
                6 => "竞赛辅导",
                7 => "心理辅导",
                8 => "家校联系",
                9 => "好人好事",
                10 => "支部风采"
            ]
        ));
        $this->assign('list',$list);
        $this->assign('table_type',$table_type);
        $this->assign('type',$type);
        return $this->fetch();
    }


    /**
     * 添加跟修改
     */
    public function add()
    {
        if(IS_POST){
            $data = input('post.');
            $newsModel = new NewsModel();
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];

            if(empty($data['id'])) {

                $statusMsg = '新增';
                unset($data['id']);
                $res = $newsModel->validate('News')->save($data);
            } else {

                $statusMsg = '修改';
                $id = $data['id'];
                unset($data['id']);
                $res = $newsModel->validate('News')->save($data,['id' => $id]);
            }

            if($res){

                return $this->success($statusMsg.'成功!');
            } else if ($res === 0){

                return $this->success('没有修改内容!');
            } else {

                return $this->error($newsModel->getError());
            }

        }else{

            $type = input('type');
            $tableType = input('table_type');
            $id = input('id');
            $msg = '';
            if (!empty($id)) {
                $msg = NewsModel::get($id);
            }
            $this->assign('msg',$msg);
            $this->assign('table_type',$tableType);
            $this->assign('type',$type);
            return $this->fetch();
        }

    }

    /**
     * 推送列表
     * $class 2党建动态
     */
    public function pushlist($class)
    {
        if (IS_POST) {

            //副图文获取
            $id = input('id');
            $class = input('class');
            $info = [
                'id' => ['neq',$id],
                'table_type' => $class -1,
                'status' => 0,
                'user_type' => 0,
                'type' => ['not in','4,5,6,7,8,10'] // 双报到 党员先锋活动 竞赛辅导 心理辅导 家校联系 支部风采
            ];
            $infoes = NewsModel::where($info)->select();
            int_to_string($infoes,array(
                'type' => [
                    1 => "新闻聚焦",
                    2 => "各地动态",
                    3 => "主题党日活动",
                    4 => "双报到",
                    5 => "党员先锋活动",
                    6 => "竞赛辅导",
                    7 => "心理辅导",
                    8 => "家校联系",
                    9 => "好人好事",
                    10 => "支部风采"
                ],
            ));
            return $this->success($infoes);

        } else {

            $type = input('type');

            //已推送消息列表
            $map = array(
                'class' => $class,
                'status' => array('egt',-1),
            );
            $list = $this->lists('Push',$map);
            int_to_string($list,array(
                'status' => array(-1 => '不通过', 0 => '未审核', 1=> '已推送'),
            ));

            //数据重组
            foreach ($list as $value) {
                $msg = NewsModel::where('id',$value['focus_main'])->find();
                $value['title'] = $msg['title'];
            }
            $this->assign('list',$list);

            //主图文本获取
            $info = array(
                'table_type' => $class -1,
                'status' => 0,
                'user_type' => 0,
                'type' => ['not in','4,5,6,7,8,10'] // 双报到 党员先锋活动 竞赛辅导 心理辅导 家校联系 支部风采
            );
            $infoes = NewsModel::where($info)->select();
            int_to_string($infoes,array(
                'type' => [
                    1 => "新闻聚焦",
                    2 => "各地动态",
                    3 => "主题党日活动",
                    4 => "双报到",
                    5 => "党员先锋活动",
                    6 => "竞赛辅导",
                    7 => "心理辅导",
                    8 => "家校联系",
                    9 => "好人好事",
                    10 => "支部风采"
                ],
            ));
            $this->assign('info',$infoes);
            $this->assign('class',$class);
            $this->assign('type',$type);
            return $this->fetch();
        }

    }

    /**
     * 推送
     */
    public function push()
    {
        $data = input('post.');
        $httpUrl = config('http_url');
        $arr1 = $data['focus_main'];    //主图文id
        isset($data['focus_vice']) ? $arr2 = $data['focus_vice'] : $arr2 = "";    //副图文id
        if($arr1 == -1){
            return $this->error("请选择主图文!");
        }else{
            //主图文信息
            $focus1 = NewsModel::where('id',$arr1)->find();
            $title1 = $focus1['title'];
            $str1 = strip_tags($focus1['content']);
            $des1 = mb_substr($str1,0,100);
            $content1 = str_replace("&nbsp;","",$des1);  //空格符替换成空
            // 标题
            switch ($focus1['type']) {
                case 1:
                    $pre1 = "【新闻聚焦】";
                    break;
                case 2:
                    $pre1 = "【各地动态】";
                    break;
                case 3:
                    $pre1 = "【主题党日活动】";
                    break;
                case 9:
                    $pre1 = "【好人好事】";
                    break;
                default:
                    break;
            }

            // 链接跳转地址
            $url1 = $httpUrl."/home/news/detail/id/".$focus1['id'].".html";

            $img1 = Picture::get($focus1['front_cover']);
            $path1 = $httpUrl.$img1['path'];
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
                $focus = NewsModel::where('id',$value)->find();
                $title = $focus['title'];
                $str = strip_tags($focus['content']);
                $des = mb_substr($str,0,100);
                $content = str_replace("&nbsp;","",$des);  //空格符替换成空
                switch ($focus['type']) {
                    case 1:
                        $pre = "【新闻聚焦】";
                        break;
                    case 2:
                        $pre = "【各地动态】";
                        break;
                    case 3:
                        $pre = "【主题党日活动】";
                        break;
                    case 9:
                        $pre = "【好人好事】";
                        break;
                    default:
                        break;
                }

                // 链接跳转地址
                $url = $httpUrl."/home/news/detail/id/".$focus1['id'].".html";

                $img = Picture::get($focus['front_cover']);
                $path = $httpUrl.$img['path'];
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

        //发送给企业号
        if ($data['class'] == 2) {

            // 党建动态
            $newsConf = config('news');
        } else if ($data['class'] == 3) {

            // 品牌同创
            $newsConf = config('brand');
        }

        $Wechat = new TPQYWechat($newsConf);
        $touser = config('touser');

        $message = array(
            "touser" => $touser, //发送给全体，@all
            "msgtype" => 'news',
            "agentid" => $newsConf['agentid'],
            "news" => $send,
            "safe" => "0"
        );
        $msg = $Wechat->sendMessage($message);

        if ($msg['errcode'] == 0){
            $data['focus_vice'] ? $data['focus_vice'] = json_encode($data['focus_vice']) : $data['focus_vice'] = null;
            $data['create_user'] = session('user_auth.username');
            $data['status'] = 1;
            $data['class'] = $data['table_type'];

            unset($data['table_type']);
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

    /**
     * 删除
     */
    public function del()
    {
        $id = input('id');
        $data['status'] = '-1';
        $info = NewsModel::where('id',$id)->update($data);
        if($info) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }

}