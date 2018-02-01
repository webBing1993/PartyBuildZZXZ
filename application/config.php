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
        'login' => 'http://zzxz.0571ztnet.com/home/index/login',
        'token' => '',
        'encodingaeskey' => '',
        'appid' => 'ww728d8759e9caec88',
        'appsecret' => 'klgnsaRedcgV8q5iHhWZggl6q6EoUKQBYjJ28cgBNus',
        'agentid' => 1000002,
    ),

    //第一聚焦模块
    'news' => [
        'appid' => 'ww728d8759e9caec88',
        'appsecret' => 'yO0QwVNkzO9HPsYmwcVByYfD5h9gclXE2Q451Oigk9w',
        'agentid' => 1000006
    ],

    //两学一做模块
    'learns' => [
        'appid' => 'ww728d8759e9caec88',
        'appsecret' => '5dvYWpDTVCVj6cDtFQbjW1vjb0odKlNSbsdE4DGXhXM',
        'agentid' => 1000004
    ],

    //综合服务模块
    'meet' => [
        'appid' => 'ww728d8759e9caec88',
        'appsecret' => 'yIF_jhTPPe91F1M-5_QEIVYdbHioJ2wCedvqYWQBbE8',
        'agentid' => 1000003
    ],

    //消息审核模块
    'audit' => [
        'appid' => 'ww728d8759e9caec88',
        'appsecret' => 'vYCfKc9CbzKiywjsm7kmpRLLJ9va0yPRqTlXhSa952A',
        'agentid' => 1000007
    ],


    //  推送网站域名
    'http_url' => "http://zzxz.0571ztnet.com",
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
