<?php
/**
 * Created by PhpStorm.
 * User: kevin www.dayblog.cn  791845283@qq.com
 */
namespace Admin\Controller;

class RootController extends PrivateController
{
    public function lists()
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

//ALTER TABLE `a_admin` ADD COLUMN `login_num` INT(10) NOT NULL DEFAULT 0 COMMENT '登录次数';