<?php
/**
 * Created by PhpStorm.
 * User: kevin www.dayblog.cn  791845283@qq.com
 */
namespace Admin\Controller;

class UserController extends PrivateController
{
    public function userlists()
    {
        ppd('还没有哟过户');
    }

    public function rootlists()
    {
        $this->model = D('Admin');
        //分配按钮
        $where = array(
            'status' => 1,
        );
        $list    = self::_modelCount($where);
        $dataArr = self::_modelSelect($where, "id,username,name,phone,last_time,email,last_ip,login_num",'sort DESC', $list['limit']);
        $this->assign('dataArr',$dataArr);
        $this->display();
    }

    public function addroot(){
        if(IS_POST){
            //修改密码
            if(I('post.old_password')){
                $adminModel = M('admin');
                $admin = $adminModel->field('id,password')->find(I('post.id'));
                if(empty($admin)){
                    $this->error("查无此用户");
                }
                $checkPassword = md5Encrypt(I('post.old_password'),$admin['id']);
                $newPassword   = md5Encrypt(I('post.password'),$admin['id']);
                if($checkPassword != $admin['password']){
                    $this->error('密码与用户不匹配！');
                }
                $res = $adminModel->where("id={$admin['id']}")->save(array('password'=>$newPassword));
                if($res){
                    $this->success("操作成功！",getU('rootlists'));
                }else{
                    $this->error("操作失败！");
                }
            }else{
                $this->model = D("Admin");
                $res = $this->_modelAdd();
                if($res){
                    if(empty(I('post.id'))){
                        //新添加的
                        $passwd = md5Encrypt(I('post.password'),$res);
                        $this->model->where("id={$res}")->save(array('password'=>$passwd));
                    }
                    $this->success("操作成功！",getU('rootlists'));
                }else{
                    $this->error($this->model_error);
                }
            }
        }

        $id = I("get.id");
        $admin = array();
        if(!empty($id)){
            $admin = M('admin')->find($id);
        }
        $this->assign('admin',$admin);
        $this->display();
    }
}