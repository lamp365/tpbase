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
        foreach($parentCate as $cate){

        }
        $this->display();
    }
}