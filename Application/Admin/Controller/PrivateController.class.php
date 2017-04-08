<?php
// +----------------------------------------------------------------------
// | 基于Thinkphp3.2.3开发的一款权限管理系统
// +----------------------------------------------------------------------
// | Copyright (c) www.dayblog.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: kevin.liu <791845283@qq.com>
// +----------------------------------------------------------------------
namespace Admin\Controller;

use Think\Controller;
use Think\Auth;

class PrivateController extends Controller
{
    private $auth         = null;
    private $group_id     = array();

    /**
     * 初始化方法
     * @auth kevin.liu www.dayblog.cn
     **/
    public function _initialize()
    {
        // 表单验证，防止重复提交
        if(!formCheckToken()){
            $this->error("禁止重复提交表单！");
        }
        //获取到当前用户所属所有分组拥有的权限id  数组形式
        $this->group_id = $this->_rules();

        //分配网站菜单
        $this->_admin_menu();

        //读取缓存名为check_iskey+uid的缓存
        $key = MODULE_NAME.'/'. CONTROLLER_NAME . '/' . ACTION_NAME;
        $where = array(
            'name'   => $key,
            'status' => 1
        );
        $auth_cate = M('auth_cate')->where($where)->field('id,title')->find();
        $this->assign('top_nav',$auth_cate['title']);

        //检测是否为超级管理员
        if (UID == C('ADMINISTRATOR')) {
            return true;
        }

		//检测该规则id是否存在于分组拥有的权限里
		if(!empty($auth_cate) && !in_array($auth_cate['id'],$this -> group_id)){
			$this->auth = new Auth();
			if(!$this->auth->check($key, UID)){
				$this->error("您没有权限访问！");
			}
		}
    }

    /**
     * 分组权限查询 获取用户所拥有的权限规则id数组
     * @author kevin.liu www.dayblog.cn
     * @return array $str 返回查询到的权限
     **/
    protected function _rules()
    {
        $uid = session(C('ADMIN_UID'));
        if (empty($uid)) {
            skip_login();
        }
        //将uid定义为常量方便后期统一使用
        defined("UID") or define("UID", $uid);
        $str = S('group_rules' . $uid);
        $str = '';
        //定义用户-用户组model
        $userGroupId = D('GroupAccess');
        if ($str == false) {
            //调用getOneField方法传参格式getOneField('字段','条件（数组）','指定条数或者true如果只查询一条就为空')
            $where = array(
                'status' => 1,
                'rules'  => array('neq','')
            );
            if ($uid != C('ADMINISTRATOR')) {
                //如果为普通管理员查看当前用户的数据
                $map = array(
                    'uid' => $uid
                );
                $group = $userGroupId->where($map)->getField('group_id', true);
                if (empty($group)) {
                    //清除登录信息
                    $this->error('您无权访问后台！',getU('Public/logout'));
                }
                $group_str = implode(",",$group);
                //可以属于多个组
                $where['id'] = array('in', $group_str);
            }

            $list = M('group')->where($where)->getField('rules', true);

            if (empty($list)) {
                //清除登录信息
                $this->error('您无权访问后台！',getU('Public/logout'));
            }
            $str     = implode(',', $list);
            $strArr  = explode(',', $str);
            $str_arr = array_unique($strArr);
            S('group_rules' . $uid, $str_arr,3600*24*15);
        }
        return $str_arr;
    }



    /**
     * 网站菜单
     * @author kevin.liu<www.dayblog.cn>
     **/
    public function _admin_menu()
    {
        $adminMenu = D('AdminMenu','Service');
        $admin_menu_url = S('admin_menu_url' . UID);
        $admin_menu_url = '';
        //检测缓存是否存在,如果不存在则生成缓存
        if ($admin_menu_url == false) {

            //更据用户所具有的group_id找出auth_cate对应的name（url）地址
            $cate_arr = array();
            foreach($this->group_id as $cate_id){
                $cate = M('auth_cate')->where("id={$cate_id}")->getField('name');
                if(!empty($cate)){
                    $cate_arr[$cate_id] = $cate;
                }
            }
            $cate_arr_flip = array_flip($cate_arr);


            //获取最顶部的菜单
            $admin_menu_url = $adminMenu->adminMenuEnum();

            //获取左侧一级菜单
            foreach($admin_menu_url as $key => &$admin_val){
                $leftMenu  = $adminMenu->adminLeftMenu($key);
                $_leftMenu = array();
                //如果权限中没有该左侧地址菜单  则进行去掉该顶部菜单
                if(empty($leftMenu)){
                    unset($admin_menu_url[$key]);
                }else{
                    //否则有再权限中的 左侧菜单要提取出来  并找出它的下一级子菜单
                    foreach($leftMenu as  $one_menu){
                        if(in_array($one_menu['url'],$cate_arr)){
                            $cate_id = $cate_arr_flip[$one_menu['url']];
                            //查找它的子分类
                            $this_son = M('auth_cate')->where("pid={$cate_id}")->field('name,title,id')->order('sort desc')->select();

                            foreach($this_son as $this_key =>  $val_arr){
                                //如果查出的子类，不在权限中则去除
                                if(!in_array($val_arr['name'],$cate_arr)){
                                    unset($this_son[$this_key]);
                                }
                            }

                            $one_menu['son']     = $this_son;

                            $_leftMenu[$cate_id] = $one_menu;
                        }
                    }
                    if(empty($_leftMenu)){
                        //如果全部都移除掉了 说明顶部菜单中没有一个左侧菜单，去掉该顶部菜单
                        unset($admin_menu_url[$key]);
                    }else{
                        $admin_val['son'] = $_leftMenu;
                    }
                }
            }

            S('admin_menu_url' . UID,$admin_menu_url);
        }
        $this->assign('admin_menu_url', $admin_menu_url);
    }


    /**
     * @param $url 要检测的权限
     * @param bool $type 是否退出 0是 1否
     * @return bool 成功返回true 否则跳转到登录页面
     */
    protected function _is_check_url($action)
    {
        if (UID == C('ADMINISTRATOR')) {
            return true;
        }
        $url = MODULE_NAME . '/' . CONTROLLER_NAME . '/' . $action;
        $where = array(
            'name'   => $url,
            'status' => 1
        );
        $id = M('auth_cate')->where($where)->getField('id');
        if ($id) {
            $this->auth = new Auth();
            if ($this->auth->check($url, UID)) {
                return true;
            }
            return false;
        }
        return true;

    }

    /**
     * success 执行成功返回json格式
     * @param $message 提示字符串
     * @param $url 跳转地址
     * @param $time 等待时间
     * @author kevin.liu(791845283@qq.com)
     * 操作  success('操作成功')  直接跳转 返回上一页
     * 操作  success('操作成功',3000)  等待3秒 返回上一页
     * 操作  success('操作成功','url')  直接跳转到url
     * 操作  success('操作成功','url',3000)  等待3秒 直接跳转到url
     **/
    public function success($message = '操作成功！', $url = 2000,$time = 2000)
    {
        if(is_numeric($url)){
            $time = $url;
            $url  = '';
        }
        $this->assign('code',200);
        $this->assign('message',$message);
        $this->assign('url',$url);
        $this->assign('time',$time);
        $this->display('Public/status');
        die();
    }

    /**
     * error 执行成功返回json格式
     * @param string $message 提示字符串
     * @param string $url 跳转地址
     * @param $time 等待时间
     * @author kevin.liu(791845283@qq.com)
     * 操作  error('操作失败')  直接跳转 返回上一页
     * 操作  error('操作失败',2000)  等待2秒 返回上一页
     * 操作  error('操作失败','url')  直接跳转到url
     * 操作  error('操作失败','url',2000)  等待2秒 直接跳转到url
     **/
    public function error($message = '操作失败！',$url = 2000,$time = 2000)
    {
        if(is_numeric($url)){
            $time = $url;
            $url  = '';
        }
        $this->assign('code',1002);
        $this->assign('message',$message);
        $this->assign('url',$url);
        $this->assign('time',$time);
        $this->display('Public/status');
        die();
    }

    /**
     * 用于统一数据格式  返回ajax请求
     * @param string $msg
     * @param int $code
     */
    public function showAjax($msg='',$code=200)
    {
        $data = array('code'=>$code,'message'=>$msg);
        $this->ajaxReturn($data,'json');
    }

}
