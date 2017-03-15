<?php
// +----------------------------------------------------------------------
// | 后台管理员,系统分组,权限分配
// +----------------------------------------------------------------------
// | Copyright (c) www.php63.cc All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 普罗米修斯 <996674366@qq.com>
// +----------------------------------------------------------------------
namespace Admin\Controller;
class AdminController extends PrivateController
{
    /**
     * uesr 后台管理员列表
     * @author 刘中胜
     * @time 2015-12-05
     **/
    public function user()
    {
        $this->model = D('Admin');
        //分配按钮
        $but = array(
            array(
                'url'   => 'useredit',
                'name'  => '添 加',
                'title' => '添加管理员',
                'type'  => 1 
            ),
        );
        self::isBut($but);
        $where = array(
            'status' => 1,
            'type'   => 0
        );
        $list = self::_modelCount($where);
        $dataArr = self::_modelSelect($where, 'sort DESC', "id,username,phone,last_time,email,addtime", $list['limit']);
        foreach ($dataArr as $key => &$value) {
            if($value['last_time'] == 0){
                $value['last_time'] = '从未登录';
            }else{
                $value['last_time'] = formatTime($value['last_time']);
            }
            $value['add_time'] = formatTime($value['addtime']);
        }
        unset($value);
        $toolsOptionArr = array(
            array('编辑',1,'编辑管理员','useredit', U('useredit', array('id' => '___id___'))),
            array('删除',2,'删除管理员','adminuserdel', U('adminuserdel', array('id' => '___id___')),'你确认要删除吗？'),
        );
        $toolsOption = self::_listBut($toolsOptionArr);
        $thead = array(
            array(
                'name'  => '用户名',
                'field' => 'username',
                'width' => '120',
                'order' => 'desc'
            ),
            array(
                'name'  => '联系电话',
                'width' => '90',
                'field' => 'phone',
                'order' => 'desc'
            ),
            array(
                'name'  => '联系邮箱',
                'width' => '150',
                'field' => 'email',
                'order' => 'desc'
            ),
            array(
                'name'  => '添加时间',
                'width' => '120',
                'field' => 'add_time',
                'order' => 'desc'
            ),
            array(
                'name'  => '最后登录时间',
                'width' => '120',
                'field' => 'last_time',
                'order' => 'desc'
            ),
            array(
                'name'  => '操作',
                'width' => '80',
                'field' => '__TOOLS',
                'type'  => 'TOOLS'
            ),
        );
        foreach ($thead as $key => $value) {
            if(empty($toolsOption) && $value['field'] == '__TOOLS'){
                unset($thead[$key]);
            }
        }
        $list['toolsOption'] = $toolsOption;
        $list['thead'] = $thead;
        $list['tbody'] = $dataArr;
        if(IS_POST){
            $list['statusCode'] = 200;
            $list['message'] = '操作成功';
            die(json_encode($list));
        }
        $this->assign('list', json_encode($list));
        $this->display('Public/list');
    }

    /**
     * group 后台用户分组
     * @author 刘中胜
     * @time 2015-12-05
     **/

    public function group()
    {
        $this->model = D('Group');
        $but = array(
            array(
                'url'   => 'groupedit',
                'name'  => '添 加',
                'title' => '添加管理分组',
                'type'  => 1
            ),
        );
        self::isBut($but);
        $where = array(
            'status' => 1
        );
        self::_modelCount($where);
        $list = S('group_list');
        $list = self::_modelSelect($where, 'sort DESC', 'title,id');
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 权限规则列表
     * @author 刘中胜
     * @time 2015-12-09
     */
    public function auth()
    {
        self::_cateList('AuthCate', '权限管理', $sort='sort DESC',$cache='auth_cate_list');
        $this->display();
    }
    /******************以下为操作方法**************/
    /**
     * group 后台用户分组添加编辑
     * @author 刘中胜
     * @time 2015-12-05
     **/
    public function groupedit()
    {
        $this->model = D('Group');
        if(IS_POST){
            self::_modelAdd('group');
        }
        $id = I('get.id', 0, 'intval');
        if($id != 0){
            $where = array(
                'id'     => $id,
                'status' => 1
            );
            self::_oneInquire($where);
        }
        $this->display();
    }

    /**
     * authinfo 显示权限分类信息
     * @author 刘中胜
     * @time 2015-12-8
     **/
    public function authinfo()
    {
        $this->model = D('AuthCate');
        $id = I('get.id', 0, 'intval');
        if($id != 0){
            $where = array(
                'id'    => $id,
                'status' => 1
            );
            $info = self::_oneInquire($where, 2);
        }else{
            $info['id'] = 0;
        }

        if(self::_is_check_url('authedit') && $info['level'] != 2){
             $info['butadd'] = self::_catebut('authedit', '添加权限', $info['id']);
        }
        if(self::_is_check_url('authdel')){
            $info['butdel'] = self::_catebut('authdel', '删除权限', $info['id'], '您确认要删除该权限吗?', 2);
        }
        $this->assign('info', $info);
        $this->display();
    }

    /**
     * group 后台用户分组添加编辑
     * @author 刘中胜
     * @time 2015-12-05
     **/
    public function useredit()
    {
        $this->model = D('Admin');
        if(IS_POST){
            self::_modelAdd('user');
        }
        $id = I('get.id', 0, 'intval');
        if($id != 0){
            $where = array(
                'id'     => $id,
                'status' => 1
            );
            self::_oneInquire($where);
            $where = array(
                'uid' => $id
            );
            $group_id = M('group_access')->where($where)->getField('group_id', true);
            $this->assign('group_id', $group_id);
        }
        $where = array(
            'status' => 1
        );
        $list = D('Group')->dataSet($where, 'sort DESC', 'id,title');
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * group 后台用户分组删除操作
     * @author 刘中胜
     * @time 2015-12-05
     **/
    public function groupdel()
    {
        $this->model = D('Group');
        self::_del('group');
    }

    /**
     * 权限添加编辑
     * @author 刘中胜
     * @time 2015-12-09
     */
    public function authedit()
    {
        $model = D('AuthCate');
        if(IS_POST){
            $data = $model->authedit();
            if($data){
                delTemp();
                $this->success($data['id'] ? '更新成功' : '添加成功', U('auth'));
            }else{
                $this->error($model->getError());
            }
        }
        $id = I('get.id', 0, 'intval');
        if($id == 0){
            $info['level'] = '';
        }else{
            $where = array(
                'status' => 1,
                'id'     => $id
            );
            $info = M('auth_cate')->where($where)->find();
        }
        $this->assign('info', $info);
        $this->display();
    }


    /**
     * groupauth 显示权限分组的权限
     * @author 刘中胜
     * @time 2015-08-11
     **/
    public function groupauth()
    {
        $id = I('get.id', 0, 'intval');
        $where = array(
            'id'     => $id,
            'status' => 1
        );
        $rules = M('group')->where($where)->getField('rules');
        $rulesArr = explode(',', $rules);
        $where = array(
            'status' => 1,
        );
        $list['res'] = M('auth_cate')->where($where)->field('id,pid,title name')->select();
        if(!$list){
            $list['res'] = array();
        }
        $arr = array('id' => 0, 'pid' => null, 'name' => '权限管理', 'isParent' => true);
        array_unshift($list['res'], $arr);
        foreach ($list['res'] as $key => &$value) {
            foreach ($rulesArr as $k => $v) {
                if($value['id'] == $v){
                    $value['checked'] = true;
                }
            }
        }
        unset($value);
        $list['statusCode'] = 200;
        die(json_encode($list));
    }


    /**
     * savegroupauth 分配权限
     * @author 刘中胜
     * @time 2015-08-11
     **/
    public function savegroupauth()
    {
        $groupId = I('get.groupId');
        $rules = I('get.idStr');
        $where = array(
            'status' => 1,
            'id'     => $groupId
        );
        $res = M('group')->where($where)->setField('rules', $rules);
      	if($res){
            delTemp();
      	    $this -> success('分配成功',U('group'));
      	}else{
      			$this -> error('分配失败');
      	}
    }


    /**
     * 删除权限
     * @author 刘中胜
     * @time 2015-12-08
     */
    public function authdel()
    {
        $this->model = D('AuthCate');
        self::_delcate('auth');
    }

    /*
     * 删除管理员
     * @author 普修米洛斯
     * @time 2015-12-08
     */
    public function adminuserdel()
    {
        $this->model = D('Admin');
        $id = I('get.id',0,'intval');
        if($id == C('ADMINISTRATOR')){
            $this -> error('系统账号无法删除');
        }
        if($id == UID){
            $this -> error('自己无法删除自己');
        }
        self::_del('user');
    }
}
