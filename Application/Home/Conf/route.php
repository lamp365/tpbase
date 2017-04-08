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
        'signin'                 => 'Login/index',         //登录
        'signup'                 => 'Login/logout',        //注销
        'register'               => 'Login/register',      //注册
        'findpwd'                => 'Login/findpassword',  //找回密码
        'mobilecode'             => 'Login/sendcode',      //发送验证码

        'center'                 => 'Ucenter/index',        //个人中心

//        'wine/active/:token\w'  => 'Account/confirm_email',   //激活页面
//        'wine/check/:token\w'   => 'Account/check_email',   //激活页面

    ),

);