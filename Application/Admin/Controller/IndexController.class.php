<?php
// +----------------------------------------------------------------------
// | 后台管理员,系统分组,权限分配
// +----------------------------------------------------------------------
// | Copyright (c) www.dayblog.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: kevin.liu <791845283@qq.com>
// +----------------------------------------------------------------------
namespace Admin\Controller;
class IndexController extends PrivateController
{
    public function index(){
        $this->display("Public/block");
    }

    public function main()
    {
        //获取操作系统版本
        $this -> assign('xt_ver',php_uname('r'));
        //获取操作系统
        $this -> assign('mac',php_uname('s'));
        //获取服务器软件
        $this -> assign('soft',$_SERVER['SERVER_SOFTWARE']);
        //获取主机名IP端口
        $this -> assign('ip',$_SERVER['REMOTE_ADDR']);
        //获取磁盘剩余空间
        $this -> assign('disk_free',tosize(disk_free_space("/")));
        //mysql版本
        $system_info_mysql = M()->query("select version() as v;");
        $this -> assign('mysql_v',$system_info_mysql[0]['v']);
        //获取数据库大小
        $this -> assign('mysql_size',_mysql_db_size());
        //获取北京标准时间
        $this -> assign('time',date("Y-m-d H:i:s"));

        $this->display();
    }

}
