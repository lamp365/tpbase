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
        //获取到当前用户所属所有分组拥有的权限id  数组形式
        $this->group_id = $this->_rules();
        $UserName = session(C('USERNAME'));
        //检测后台管理员昵称是否存在，如果不等于空或者0则获取配置文件里定义的name名字并分配给首页
        if (!empty($UserName)) {
            $this->assign('UserName', session(C('USERNAME')));
        }
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
                'status' => 1
            );
            if ($uid != C('ADMINISTRATOR')) {
                //如果为普通管理员查看当前用户的数据
                $map = array(
                    'uid' => $uid
                );
                $group = $userGroupId->where($map)->getField('group_id', true);
                if (empty($group)) {
                    $this->error('访问权限不足');
                }
                //可以属于多个组
                $where['id'] = array('in', $group);
            }

            $list = M('group')->where($where)->getField('rules', true);
            if (empty($list[0])) {
                $this->error('访问权限不足');
            }
            $str     = implode(',', $list);
            $strArr  = explode(',', $str);
            $str_arr = array_unique($strArr);
            S('group_rules' . $uid, $str_arr);
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
                            $one_menu['son'] = M('auth_cate')->where("pid={$cate_id}")->field('name,title,id')->order('sort desc')->select();

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
    protected function _is_check_url($url)
    {
        if (UID == C('ADMINISTRATOR')) {
            return true;
        }
        $url = strtolower($url);
        $url = MODULE_NAME . '/' . CONTROLLER_NAME . '/' . $url;
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
     * @time 2015-15-05
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
     * isBbutton 控制页面添加按钮是否显示
     * @param string $title 弹出框标题
     * @param string $url 跳转地址
     * @param int $type 跳转类型: 1为弹出层 2为新窗口打开
     * @author kevin.liu<www.dayblog.cn>
     **/
    protected function isBut($but = array())
    {
        $dataArr = array();
        foreach ($but as $Key => $value) {
            if (self::_is_check_url($value['url'])) {
                if (!empty($value['parameter'])) {
                    $url = U($value['url'], $value['parameter']);
                } else {
                    $url = U($value['url']);
                }
                $title = $value['title'];
                if ($value['type'] == 1) {
                    $href = 'JavaScript:;';
                    $target = 'popDialog';
                    $dataOpt = "{title:'" . "$title',url:'" . "$url'" . '}';
                } else {
                    $href = $url;
                    $target = '';
                    $dataOpt = '';
                }
                $dataArr[] = array(
                    'href'    => $href,
                    'target'  => $target,
                    'dataopt' => array(
                        'data-opt' => $dataOpt,
                        'content'  => $value['name']
                    )
                );
            }
        }
        $this->assign('editTag', $dataArr);
    }

    /**
     * isBbutton 控制分组页面按钮类型
     * @param string $title 弹出框标题
     * @param string $url 跳转地址
     * @param int $type 跳转类型: 1为添加 2为其他
     * @author kevin.liu<www.dayblog.cn>
     **/
    protected function _catebut($url, $title, $id = 0, $msg = '', $type = 1)
    {
        $res = self::_is_check_url($url, 1);
        if ($res) {
            if ($id != 0) {
                $where = array(
                    'id' => $id
                );
                $url = U($url, $where);
            } else {
                $url = U($url);
            }
            if ($type == 1) {
                $butArr = array(
                    'data-opt' => "{title:'" . "$title',url:'" . "$url'" . '}',
                    'title'    => '添 加',
                );
            } else {
                $butArr = array(
                    'data-opt' => "{title:'" . "$title',url:'" . "$url',msg:'" . "$msg'" . '}',
                    'title'    => '删 除',
                );
            }
        }
        return $butArr;
    }



    /**
     * 分类列表
     * @param string $model 要操作的表
     * @param string $cache 缓存名称
     * @author kevin.liu<www.dayblog.cn>
     * @time 2016-01-21
     **/
    public function _cateList($model, $title, $sort = '', $cache = '')
    {
        $list = S($cache . UID);
        if ($list == false) {
            $this->model = D($model);
            $where = array(
                'status' => 1
            );
            $list = self::_modelSelect($where, $sort);
            if (!$list) {
                $list = array();
            }
            $arr = array(
                'id'       => 0,
                'pid'      => null,
                'title'    => $title,
                'isParent' => true,
                'open'     => true,
            );
            array_unshift($list, $arr);
            $list = json_encode($list);
            S($cache . UID, $list);
        }
        $this->assign('list', $list);
    }

    /**
     * 列表右边操作按钮
     * 数组里第二个参数为跳转类型参数
     * type 1弹出层 2删除 3审核 4直接打开
     * @author kevin.liu<www.dayblog.cn>
     **/
    protected function _listBut($data)
    {
        $dataArr = array();
        foreach ($data as $key => $value) {
            if (self::_is_check_url($value[3])) {
                $dataArr[$key]['name'] = $value[0];
                $dataArr[$key]['opt']['title'] = $value[2];
                $dataArr[$key]['opt']['url'] = $value[4];
                switch ($value[1]) {
                    case 1://弹出层
                        $dataArr[$key]['target'] = 'popDialog';
                        break;
                    case 2:
                        $dataArr[$key]['opt']['msg'] = $value[5];
                        $dataArr[$key]['target'] = 'ajaxDel';
                        break;
                    case 3:
                        $dataArr[$key]['opt']['msg'] = $value[5];
                        $dataArr[$key]['target'] = 'ajaxTodo';
                        $dataArr[$key]['opt']['value'] = $value[7];
                        $dataArr[$key]['opt']['type'] = $value[6];
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
        return $dataArr;
    }

    /**
     * 删除分类
     * @author kevin.liu<www.dayblog.cn>
     **/
    protected function _delcate($url)
    {
        if (!$this->model) {
            $this->error("表名未定义");
        }
        $res = $this->model->delcate();
        if ($res) {
            $this->success('操作成功', U($url));
        }
        $this->error($this->model->getError());
    }

}
