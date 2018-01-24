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
        'login' => 'http://dzgcpb.0571ztnet.com/home/index/login',
        'token' => '',
        'encodingaeskey' => '',
        'appid' => 'ww361a838e44ae696f',
        'appsecret' => '1S1aMeIkq7YeESEjDiP5e5h2YTefvs47b88deKUNdlg',
        'agentid' => 1000003,
    ),

    //消息通知模块
    'news' => [
        'appid' => 'ww361a838e44ae696f',
        'appsecret' => '05FC2Dycjf421Uhz2_YGb7j_IeArwgCe47u2Ucd1tZE',
        'agentid' => 1000004
    ],
    //消息审核模块
    'review' => [
        'appid' => 'ww361a838e44ae696f',
        'appsecret' => 'lXzi5CRoSBp0MoTw-pQY7LS1EVNDWXQfOO1Lzoi1lx4',
        'agentid' => 1000002
    ],


    //  推送网站域名
    'http_url' => "http://xpf.free.cn",
    // 推送对象 发送给全体 @all  个人测试15700004138
    'touser' => '18767104335',
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
