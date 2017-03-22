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
        $dataArr = self::_modelSelect($where, 'sort DESC', "id,username,phone,last_time,email,addtime", $list['limit']);
        foreach ($dataArr as $key => &$value) {
            if($value['last_time'] == 0){
                $value['last_time'] = '从未登录';
            }else{
                $value['last_time'] = formatTime($value['last_time']);
            }
            $value['add_time'] = formatTime($value['addtime']);
        }

        $this->display();
    }
}