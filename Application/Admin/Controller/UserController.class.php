<?php
/**
 * Created by PhpStorm.
 * User: kevin www.dayblog.cn  791845283@qq.com
 */
namespace Admin\Controller;

class UserController extends PrivateController
{
    ///////////////////普通会员操作/////////////////
    public function userlists()
    {
        ppd('还没有用户哦！');
    }


    ///////////////////////管理员操作///////////////////////
    public function rootlists()
    {
        $adminModel = D('Admin');
        $where = array(
            'status' => 1,
        );
        $list    = $adminModel->_modelCount($where);
        $dataArr = $adminModel->_modelSelect($where, "id,username,name,phone,last_time,email,last_ip,login_num",'sort DESC', $list['limit']);
        $this->assign('dataArr',$dataArr);
        $this->display();
    }

    public function addroot(){
        if(IS_POST){
            //修改密码
            if(I('post.old_password')){
                if(session(C('ADMIN_UID')) != C('ADMINISTRATOR')){
                    $this->error("只有管理员才可以操作！");
                }
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
                $adminModel = D("Admin");
                //分配按钮
                $res = $adminModel->_modelAdd();
                if($res){
                    if(empty(I('post.id'))){
                        //新添加的
                        $passwd = md5Encrypt(I('post.password'),$res);
                        $adminModel->where("id={$res}")->save(array('password'=>$passwd));
                    }
                    $this->success("操作成功！",getU('rootlists'));
                }else{
                    $this->error($adminModel->getError());
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


    //删除管理员
    public function delroot()
    {
        $model = D('Admin');
        $id = I('get.id',0,'intval');
        if($id == C('ADMINISTRATOR')){
            $this -> error('系统账号无法删除');
        }
        if($id == UID){
            $this -> error('自己无法删除自己');
        }

        $res = $model->_modelDelete(array("id"=>$id));
        if($res){
            $this->success("删除成功！");
        }else{
            $this->error($model->getError());
        }
    }

    //分配属组
    public function groupchoose()
    {
        if(IS_POST){
            $uid = I('post.uid');
            $model = D('GroupAccess');
            $res   = $model->_modelDelete("uid={$uid}");
            $group_ids = I('post.group_ids');
            if(!empty($group_ids)){
                foreach($group_ids as $id){
                    $data = array('uid'=>$uid,'group_id'=>$id);
                    $model->add($data);
                }
            }
            $this->success('操作成功！');
        }

        //该用户所属于的组
        $id    = I('get.id',0);  //uid
        $admin = D('Admin')->where("id={$id}")->getField('username');

        $group_id_arr = D('GroupAccess')->where("uid={$id}")->getField('group_id',true);
        $group_id_str = implode(',',$group_id_arr);
        //总的组
        $group_arr    = D('Group')->_modelSelect('status=1',"id,title",'sort desc');
        $this->assign('group_id_str',$group_id_str);
        $this->assign('group_arr',$group_arr);
        $this->assign('admin',$admin);
        $this->display();
    }

    public function level()
    {
        ppd('会员等级！');
    }
}