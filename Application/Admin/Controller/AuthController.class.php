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
            $this->model = D("AuthCate");
            $name = I('post.module').'/'.I('post.controller').'/'.I('post.method');
            $_POST['name']  = rtrim($name,'/');
            $_POST['level'] = 1;
            $res = $this->_modelAdd();
            if($res){
                $this->success("操作成功",getU("navlists"));
            }else{
                $this->error($this->model_error);
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
            $this->model = D("AuthCate");
            $name = I('post.module').'/'.I('post.controller').'/'.I('post.method');
            $_POST['name']  = rtrim($name,'/');
            $_POST['level'] = 2;
            $res = $this->_modelAdd();
            if($res){
                $this->success("操作成功",getU("navlists"));
            }else{
                $this->error($this->model_error);
            }
        }
        $id   = I("get.id");
        $cate = M('auth_cate')->where("id={$id}")->find();
        $this->assign('cate',$cate);
        $this->display();
    }

    //编辑一级菜单
    public function editMenu()
    {
        if(IS_POST){
            $this->model = D("AuthCate");
            $name = I('post.module').'/'.I('post.controller').'/'.I('post.method');
            $_POST['name']  = rtrim($name,'/');
            $_POST['level'] = 2;
            $res = $this->_modelAdd();
            if($res){
                $this->success("操作成功",getU("navlists"));
            }else{
                $this->error($this->model_error);
            }
        }
        $auth = M('auth_cate');
        $id   = I("get.id");
        $cate = $auth->where("id={$id}")->find();
        //查找分分类
        $parent = $auth->field('title')->where("id={$cate['pid']}")->find();
        $this->assign('parent',$parent);
        $this->assign('cate',$cate);
        $this->display();
    }
}