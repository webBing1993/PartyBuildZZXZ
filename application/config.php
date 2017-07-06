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
        'token' => 'N3mIjNX',
        'encodingaeskey' => 'RxanruTaFxW7X5r5Cx2xRrI91dhRgNUx77KM3paUfS7',
        'appid' => 'wx8caedf8a60d0795c',
        'appsecret' => 'hQ9p_yPJRLWMPVTWNo6PeugiB6gzgSZ_Q2SKhAScyQw',
        'agentid' => 0
    ),
    /*地信红盟 */
    'alliance' => array(
        'appid' => 'wx8caedf8a60d0795c',
        'appsecret' => 'xqOnHZ6LZ6xZWSocFGXKuAeyVmOJxZu3dpW0dgG0IQs',
        'agentid' => 2
    ),
    /*红领学院*/
    'redcollege' => array(
        'appid' => 'wx8caedf8a60d0795c',
        'appsecret' => 'f4VyQSkCqpsIO5A-9ZPrE3ZZm1axmmFcI5hGyJpByoY',
        'agentid' => 4
    ),
    /*人才服务*/
    'talent' => array(
        'appid' => 'wx8caedf8a60d0795c',
        'appsecret' => '5vxPTO4J7bIJDeuVtcRZVYz3857xSdhR1rGCMTZc1zg',
        'agentid' => 5
    ),
    /*个人中心*/
    'user' => array(
        'appid' => 'wx8caedf8a60d0795c',
        'appsecret' => '0XI4XrdPJCaBSSwF8qjTfSgCk-ZslFrR3jbwkG8eWU0',
        'agentid' => 8
    ),
    /*消息审核*/
    'review' => array(
        'appid' => 'wx8caedf8a60d0795c',
        'appsecret' => 'fVkyaUm7TEU_Q-uCT2YegxEIDGwXjnvDmDxWBv-srOM',
        'agentid' => 11
    ),
    /*志愿服务*/
    'volunteer' => array(
        'appid' => 'wx8caedf8a60d0795c',
        'appsecret' => 'LcM9iK4xXoFSkH19ssG9xzfTUplP7lcAwM84qoJ1Rcw',
        'agentid' => 25
    ),
    /*小镇动态*/
    'news' => array(
        'appid' => 'wx8caedf8a60d0795c',
        'appsecret' => 'oXs3Wvs0N8I_uO39vxTVCZCjmFzuXHWnwHKEiWe5NzA',
        'agentid' => 26
    ),
    /*两学一做*/
    'learn' => array(
        'appid' => 'wx8caedf8a60d0795c',
        'appsecret' => 'TlzE0Fwk5KBZJx_ticbfWytrmyQSapvy27a316WpBcI',
        'agentid' => 27
    ),
    /*支部活动*/
    'notice' => array(
        'appid' => 'wx8caedf8a60d0795c',
        'appsecret' => 'ix-HaRO0Hozhlt-LbOORl2pl8ymn8dAiEBSXPJgGj6w',
        'agentid' => 28
    ),
    /* UC用户中心配置 */
    'uc_auth_key' => '(.t!)=JTb_OPCkrD:-i"QEz6KLGq5glnf^[{p;je',
    // 关闭调试模式
'app_debug' => false,
    // 显示错误信息
    'show_error_msg'        =>  true,
];
