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

    //分类菜单  添加或者修改  一级菜单
    public function addcate()
    {
        if(IS_POST){
            $authMode = D("AuthCate");
            $name = I('post.module').'/'.I('post.controller').'/'.I('post.method');
            $_POST['name']  = rtrim($name,'/');
            $_POST['level'] = 1;
            $res = $authMode->_modelAdd();
            if($res){
                $this->success("操作成功",getU("navlists"));
            }else{
                $this->error($authMode->getError());
            }
        }

        $cate = array();
        if($id = I("get.id")){
            $cate = M('auth_cate')->where("id={$id}")->find();
        }

        $this->assign('cate',$cate);
        $this->display();
    }

    //添加二级菜单
    public function addmenu()
    {
        if(IS_POST){
            $authMode = D("AuthCate");
            $name = I('post.module').'/'.I('post.controller').'/'.I('post.method');
            $_POST['name']  = rtrim($name,'/');
            $_POST['level'] = 2;
            $res = $authMode->_modelAdd();
            if($res){
                $this->success("操作成功",getU("navlists"));
            }else{
                $this->error($authMode->getError());
            }
        }
        $id   = I("get.id");
        $cate = M('auth_cate')->where("id={$id}")->find();
        $this->assign('cate',$cate);
        $this->display();
    }

    //编辑二级菜单
    public function editmenu()
    {
        if(IS_POST){
            $authMode = D("AuthCate");
            $name = I('post.module').'/'.I('post.controller').'/'.I('post.method');
            $_POST['name']  = rtrim($name,'/');
            $_POST['level'] = 2;
            $res = $authMode->_modelAdd();
            if($res){
                $this->success("操作成功",getU("navlists"));
            }else{
                $this->error($authMode->getError());
            }
        }
        $auth = M('auth_cate');
        $id   = I("get.id");
        $cate = $auth->where("id={$id}")->find();
        //查找父分类
        $parent = $auth->field('title')->where("id={$cate['pid']}")->find();
        $this->assign('parent',$parent);
        $this->assign('cate',$cate);
        $this->display();
    }

    public function menusort()
    {
        $group = D('AuthCate');
        $res = $group->_modelAdd();
        if($res){
            $this->showAjax('已经排序成功！');
        }else{
            $this->showAjax($group->getError(),1002);
        }
    }

    //进入子菜单 三级菜单查看
    public function showson()
    {
        if(empty(I('get.id'))){
            $this->error('对不起，参数有误');
        }
        $model    = D('AuthCate');
        $son_cate = $model->_modelSelect(array("pid"=>I('get.id')),'',"sort desc");
        $parent   = $model->_modelfind(array("id"=>I('get.id')));
        $this->assign('son_cate',$son_cate);
        $this->assign('parent',$parent);
        $this->display();
    }

    //添加三级菜单
    public function addsonmenu()
    {
        if(IS_POST){
            $authMode = D("AuthCate");
            $name = I('post.module').'/'.I('post.controller').'/'.I('post.method');
            $_POST['name']  = rtrim($name,'/');
            $_POST['level'] = 3;
            $res = $authMode->_modelAdd();
            if($res){
                $this->success("操作成功",getU("showson"));
            }else{
                $this->error($authMode->getError());
            }
        }
        $id   = I("get.id");
        $cate = M('auth_cate')->where("id={$id}")->find();
        $this->assign('cate',$cate);
        $this->display();
    }

    //编辑三级菜单
    public function editsonmenu()
    {
        if(IS_POST){
            $authMode = D("AuthCate");
            $name = I('post.module').'/'.I('post.controller').'/'.I('post.method');
            $_POST['name']  = rtrim($name,'/');
            $_POST['level'] = 3;
            $res = $authMode->_modelAdd();
            if($res){
                $this->success("操作成功",getU("showson"));
            }else{
                $this->error($authMode->getError());
            }
        }
        $auth = M('auth_cate');
        $id   = I("get.id");
        $cate = $auth->where("id={$id}")->find();
        //查找父分类
        $parent = $auth->field('title')->where("id={$cate['pid']}")->find();
        $this->assign('parent',$parent);
        $this->assign('cate',$cate);
        $this->display();
    }

    //////////////分组列表//////////////////
    public function group()
    {
        $groupModel = D('Group');
        $where = array(
            'status' => 1
        );
        $list = $groupModel->_modelSelect($where, 'title,id,sort,desc,createtime','sort DESC');
        $this->assign('list', $list);
        $this->display();
    }

    public function addgroup()
    {
        if(IS_POST){
            $groupModel = D('Group');
            $res = $groupModel->_modelAdd();
            if($res){
                $this->success("操作成功",getU("group"));
            }else{
                $this->error($groupModel->getError());
            }
        }
        $id = I("get.id");
        $group = array();
        if(!empty($id)){
            $group = M('group')->find($id);
        }
        $this->assign('group',$group);
        $this->display();

    }

    public function groupsort()
    {
        $group = D('Group');
        $res = $group->_modelAdd();
        if($res){
            $this->showAjax('已经排序成功！');
        }else{
            $this->showAjax($group->getError(),1002);
        }
    }

    public function groupaccess()
    {
        if(IS_POST){
            if(empty(I("post.ids"))){
                $_POST['rules'] = '';
            }else{
                $_POST['rules'] = implode(',',I("post.ids"));
            }

            $group = D('Group');
            $res   = $group->_modelAdd();
            if($res){
                $this->success('分配权限成功！');
            }else{
                $this->error($group->getError());
            }
        }

        $id = I('get.id',0);
        $rules_arr = D("Group")->_modelFind("id={$id}","id,title,rules");

        $all_rules = D("AuthCate")->_modelSelect(array('status'=>1),"id,pid,title","sort desc");
        $all_rules_arr = array();
        //无限分类
        catTree2($all_rules_arr,$all_rules);

        $check_rule = empty($rules_arr['rules'])? array() : explode(",",$rules_arr['rules']);


        $this->assign('rules_arr',$rules_arr);
        $this->assign('all_rules_arr',$all_rules_arr);
        $this->assign('check_rule',$check_rule);
        $this->display();
    }
}