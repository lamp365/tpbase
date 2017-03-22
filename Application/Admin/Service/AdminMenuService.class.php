<?php
/**
 * Created by PhpStorm.
 * User: kevin.liu www.dayblog.cn
 */
namespace Admin\Service;

use Think\Model;

class AdminMenuService extends Model{

    protected $autoCheckFields = False;

    public function __construct()
    {
        parent::__construct();
    }

    public function adminMenuEnum(){
       return array(
           'system_mange' => array(
                                   'name' => '系统管理',
                                   'icon' => 'icon-home'
                               ),
           'user_mange'   => array(
                                   'name' => '用户管理',
                                   'icon' => 'icon-home'
                             ),

       );
    }

    public function adminLeftMenu($key){
        //模块 控制器
        $data =  array(
            'system_mange' =>
                array(
                    array(
                        'name' => '微信设置',
                        'icon' => 'icon-home',
                        'url'  => 'Admin/Weixin'
                    ),
                    array(
                        'name' => '网站配置',
                        'icon' => 'icon-home',
                        'url'  => 'Admin/Config'
                    ),
                    array(
                        'name' => '支付方式',
                        'icon' => 'icon-home',
                        'url'  => 'Admin/Payway'
                    ),
                    array(
                        'name' => '数据管理',
                        'icon' => 'icon-home',
                        'url'  => 'Admin/Database'
                    ),
                    array(
                        'name' => '后台主页',
                        'icon' => 'icon-home',
                        'url'  => 'Admin/Admin'
                    )
                ),
            'user_mange' =>
                array(
                    array(
                        'name' => '会员列表',
                        'icon' => 'icon-home',
                        'url'  => 'Admin/User'
                    ),
                    array(
                        'name' => '管理员列表',
                        'icon' => 'icon-home',
                        'url'  => 'Admin/Root'
                    ),
                    array(
                        'name' => '权限管理',
                        'icon' => 'icon-home',
                        'url'  => 'Admin/Auth'
                    ),
                    array(
                        'name' => '分组管理',
                        'icon' => 'icon-home',
                        'url'  => 'Admin/Group'
                    )
                ),
        );
        if(array_key_exists($key,$data))
            return $data[$key];
        else
            return array();
    }
}