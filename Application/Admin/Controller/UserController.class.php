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

    }

    public function rootLists()
    {
        $this->model = D('Admin');
        //分配按钮
        $where = array(
            'status' => 1,
            'type'   => 0  //type 1分页用，可获取分页信息  否则返回总条数
        );
        $list    = self::_modelCount($where);
        $dataArr = self::_modelSelect($where, 'sort DESC', "id,username,phone,last_time,email,last_ip,login_num", $list['limit']);
        $this->assign('dataArr',$dataArr);
        $this->display();
    }
}