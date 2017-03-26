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
            'system_mange' =>       //系统管理
                array(
                    array(
                        'name' => '网站配置',
                        'icon' => 'icon-home',
                        'url'  => 'Admin/Config'
                    ),
                    array(
                        'name' => '菜单管理',
                        'icon' => 'icon-home',
                        'url'  => 'Admin/Menus'
                    ),
                    array(
                        'name' => '微信管理',
                        'icon' => 'icon-home',
                        'url'  => 'Admin/Weixin'
                    ),
                    array(
                        'name' => '数据管理',
                        'icon' => 'icon-home',
                        'url'  => 'Admin/Database'
                    )
                ),
            'user_mange' =>         //用户管理
                array(
                    array(
                        'name' => '会员管理',
                        'icon' => 'icon-home',
                        'url'  => 'Admin/User'
                    ),
                    array(
                        'name' => '权限管理',
                        'icon' => 'icon-home',
                        'url'  => 'Admin/Auth'
                    )
                ),
        );
        if(array_key_exists($key,$data))
            return $data[$key];
        else
            return array();
    }
}