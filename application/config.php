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
        'login' => 'http://dqpb.0571ztnet.com/home/index/login',
        'token' => '',
        'encodingaeskey' => '',
        'appid' => 'ww58b28476c9e654',
        'appsecret' => 'xkJk0zsFa_df97RIDCuKZDcjYZSZWgaVsWhGjeDgK',
        'agentid' => 1000002,
    ),

    /*红色石淙 */
    'learn' => array(
        'appid' => 'ww58b28476c9e6',
        'appsecret' => '9vHQ5v2f9fhnKn-O7JF44QY1DnRqK5mNYsHwyRsD',
        'agentid' => 1000003
    ),

    /*廉政石淙 */
    'Clean' => array(
        'appid' => 'ww58b28476c9e6',
        'appsecret' => '9vHQ5v2f9fhnKn-O7JF44QY1DnRqK5mNYsHwyRsD',
        'agentid' => 1000003
    ),

    /*美丽乡村 */
    'Country' => array(
        'appid' => 'ww58b28476c9e6',
        'appsecret' => '9vHQ5v2f9fhnKn-O7JF44QY1DnRqK5mNYsHwyRsD',
        'agentid' => 1000003
    ),

    /*掌上石淙 */
    'Hands' => array(
        'appid' => 'ww58b28476c9e6',
        'appsecret' => '9vHQ5v2f9fhnKn-O7JF44QY1DnRqK5mNYsHwyRsD',
        'agentid' => 1000003
    ),


    //  推送网站域名
    'http_url' => "http://ymz.pb.cn",
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
