<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

return [
    'url_route_on' => true,
//    'log'          => [
//        'type' => 'trace', // 支持 socket trace file
//    ],

    /* 默认模块和控制器 */
    'default_module' => 'home',

    /* URL配置 */
    'base_url'=>'',
    'parse_str'=>[
        '__ROOT__' => '/',
        '__STATIC__' => '/static',
        '__ADMIN__' => '/admin',
        '__HOME__' => '/home',
    ],
    
    /* 企业配置   新手指南*/
    'party' => array(
        'login' => 'http://szzpb.0571ztnet.com/home/index/login',
        'token' => '',
        'encodingaeskey' => '',
        'appid' => 'wwdc9046238ff986dd',
        'appsecret' => 'hlwoxVTsz3-uVm2_xaTS_4dk6UvqJ2Gy1Qf8NNXZEvY',
        'agentid' => 1000002,
    ),

    /*红色石淙 */
    'Learn' => array(
        'appid' => 'wwdc9046238ff986dd',
        'appsecret' => 'hlwoxVTsz3-uVm2_xaTS_4dk6UvqJ2Gy1Qf8NNXZEvY',
        'agentid' => 1000002
    ),

    /*廉政石淙 */
    'Clean' => array(
        'appid' => 'wwdc9046238ff986dd',
        'appsecret' => 'Izg3mHtwqxcTbrh7Kb_xVd-9btzCq9mmP7WFsgjCfZk',
        'agentid' => 1000008
    ),

    /*美丽乡村 */
    'Country' => array(
        'appid' => 'wwdc9046238ff986dd',
        'appsecret' => 'ksvZ7Afupe_jouG_8onwnt9qzwWGsvtY6JoMDSvSWP4',
        'agentid' => 1000006
    ),

    /*掌上石淙 */
    'Hands' => array(
        'appid' => 'wwdc9046238ff986dd',
        'appsecret' => 'XgkQXpjOSR9r8bNwyjvrsQsQwyETZQ6gp_3sJdltnUw',
        'agentid' => 1000007
    ),


    //  推送网站域名
    'http_url' => "http://szzpb.0571ztnet.com",
    // 推送对象 发送给全体 @all  个人测试15700004138
    'touser' => '15700004138',
    /* UC用户中心配置 */
    'uc_auth_key' => '(.t!)=JTb_OPCkrD:-i"QEz6KLGq5glnf^[{p;je',
    // 关闭调试模式
    'app_debug' => true,
    // 显示错误信息
    'show_error_msg'        =>  true,

    /*'log' => [
        'type'                  =>  'socket',
        'host'                  =>  'localhost',
        'show_included_files'   =>  true,
        'force_client_ids'      =>  ['ymz_1259'],
        'allow_client_ids'      =>  ['ymz_1259'],
    ],*/
];
