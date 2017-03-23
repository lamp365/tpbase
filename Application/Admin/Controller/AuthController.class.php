<?php
/**
 * Created by PhpStorm.
 * User: kevin www.dayblog.cn  791845283@qq.com
 */
namespace Admin\Controller;

class AuthController extends PrivateController
{
    public function navlists()
    {
        $parentCate = M("auth_cate")->where("pid=0")->order('sort desc')->select();
        foreach($parentCate as &$cate){
            $son_cate = M("auth_cate")->where("pid={$cate['id']}")->order("sort desc")->select();
            $cate['son_cate'] = $son_cate;
        }
        $this->assign('parentCate',$parentCate);
        $this->display();
    }

    public function addMenu()
    {
        $this->display();
    }
}