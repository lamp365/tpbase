<?php
/**
 * Created by PhpStorm.
 * User: kevin www.dayblog.cn  791845283@qq.com
 */
namespace Admin\Controller;

class UserController extends PrivateController
{
    public function userLists()
    {
        ppd('还没有哟过户');
    }

    public function rootLists()
    {
        $this->model = D('Admin');
        //分配按钮
        $where = array(
            'status' => 1,
        );
        $list    = self::_modelCount($where);
        $dataArr = self::_modelSelect($where, "id,username,phone,last_time,email,last_ip,login_num",'sort DESC', $list['limit']);
        $this->assign('dataArr',$dataArr);
        $this->display();
    }
}