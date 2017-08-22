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
        'token' => 'N3mIjN',
        'encodingaeskey' => 'RxanruTaFxW7X5r5Cx2xRrI91dhRgNUx77KM3paUS',
        'appid' => 'wx8caedf8a60d075',
        'appsecret' => 'hQ9p_yPJRLWMPVTWNo6PeugiB6gzgSZ_Q2SKhAScy',
        'agentid' => 0
    ),

    /*两学一做 */
    'learn' => array(
        'appid' => 'wx8caedf8a60d0795',
        'appsecret' => 'xqOnHZ6LZ6xZWSocFGXKuAeyVmOJxZu3dpW0dgG0IQ',
        'agentid' => 2
    ),

    /*党建动态 */
    'news' => array(
        'appid' => 'wx8caedf8a60d0795',
        'appsecret' => 'xqOnHZ6LZ6xZWSocFGXKuAeyVmOJxZu3dpW0dgG0IQ',
        'agentid' => 2
    ),

    /*品牌同创 */
    'brand' => array(
        'appid' => 'wx8caedf8a60d0795',
        'appsecret' => 'xqOnHZ6LZ6xZWSocFGXKuAeyVmOJxZu3dpW0dgG0IQ',
        'agentid' => 2
    ),

    //  推送网站域名
    'http_url' => "http://dqpb.0571ztnet.com",
    // 推送对象 发送给全体 @all  个人测试15700004138
    'touser' => '15700004138',
    /* UC用户中心配置 */
    'uc_auth_key' => '(.t!)=JTb_OPCkrD:-i"QEz6KLGq5glnf^[{p;je',
    // 关闭调试模式
    'app_debug' => true,
    // 显示错误信息
    'show_error_msg'        =>  true,
];
