<?php
// +----------------------------------------------------------------------
// | 后台管理员,系统分组,权限分配
// +----------------------------------------------------------------------
// | Copyright (c) www.dayblog.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: kevin.liu <791845283@qq.com>
// +----------------------------------------------------------------------
namespace Admin\Controller;
class AdminController extends PrivateController
{
    public function index(){
        $this->display();
    }




    /**
     * 权限添加编辑
     * @author kevin.liu
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
     * @author kevin.liu
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
     * @author kevin.liu
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


}
