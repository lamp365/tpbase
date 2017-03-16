<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2015/8/16
 * Time: 20:46
 */
return array(
    'URL_ROUTER_ON'   => true,
    'URL_ROUTE_RULES'=>array(
        'signin'                 => 'Public/login',      //登录
        'logout'                 => 'Public/logout',      //注销

//        'wine/active/:token\w'  => 'Account/confirm_email',   //激活页面
//        'wine/check/:token\w'   => 'Account/check_email',   //激活页面

    ),

);