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

    //分类菜单  添加或者修改
    public function addCate()
    {
        if(IS_POST){
            $id = I('post.id');
            if($id){
                //modify old
            }else{
                //add new

            }
        }
        $cate = array();
        if($id = I("get.id")){
            $cate = M('auth_cate')->where("id={$id}")->find();
        }

        $this->assign('cate',$cate);
        $this->display();
    }

    //添加一级菜单
    public function addMenu()
    {
        if(IS_POST){

        }
        $id   = I("get.id");
        $cate = M('auth_cate')->where("id={$id}")->find();
        $this->assign('cate',$cate);
        $this->display();
    }

    public function editMenu()
    {
        if(IS_POST){

        }
        $id   = I("get.id");
        $cate = M('auth_cate')->where("id={$id}")->find();
        $this->assign('cate',$cate);
        $this->display();
    }
}