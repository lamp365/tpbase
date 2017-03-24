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

use Think\Auth;

class PrivateController extends PublicController
{
    public  $model        = null;
    private $auth         = null;
    public  $model_error  = null;
    private $group_id     = array();

    /**
     * 初始化方法
     * @auth kevin.liu www.dayblog.cn
     **/
    public function _initialize()
    {
        //获取到当前用户所属所有分组拥有的权限id  数组形式
        $this->group_id = self::_rules();
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
     * 添加编辑操作
     * @param string $model 要操作的表
     * @param string $url 要跳转的地址
     * @param int $typeid 0 为直接返回 1为返回数组
     * @return boolean
     * @author kevin.liu<www.dayblog.cn>  <791845283@qq.com>
     */
    protected function _modelAdd($typeid = 0)
    {
        if (!$this->model) {
            $this->error('请传入操作表名');
        }

        $data = $this->model->create();

        if (empty($data)) {
            $this->model_error = "数据初始化失败";
            return false;
        }
        if (empty($data['id'])) {
            $id = $this->model->add();
            if (!$id) {
                $this->model_error = "添加操作失败";
                return false;
            }
            $res = $id;
        } else {
            $res = $this->model->save();
            if (!$res) {
                $this->model_error = "更新操作失败";
                return false;
            }
        }

        if ($typeid == 1) {
            return $data;
        }
       return $res;
    }

    /**
     * 查询总条数
     * @param string $model 要操作的表
     * @param array $where 查询的条件
     * @param int $type 类型 :type =1 分页用 type=2普通查询
     * @return mixed
     * @author kevin.liu<www.dayblog.cn>  <791845283@qq.com>
     */
    protected function _modelCount($where = array(), $type = 1, $num = '')
    {
        $count = $this->model-> where($where)->count();
        if ($type == 1) {
            if ($num == '') {
                $num = C('PAGENUM');
            }
            $Page = self::_page($count, $num);
            return $Page;
        }
        return $count;
    }

    /**
     * 查询多条数据
     * @param string $model 要操作的表
     * @param array $where 查询的条件
     * @param string $limit 分页
     * @param string $order 排序方式
     * @param string $field 要显示的字段
     * @return array
     * @author kevin.liu<www.dayblog.cn>  <791845283@qq.com>
     */
    protected function _modelSelect($where, $field = "*", $order,  $limit = '')
    {
        if (!$this->model) {
            $this->error("表名未定义");
        }
        $list = $this->model->where($where)->limit($limit)->order($order)->field($field)->select();
        return $list;
    }

    /**
     * 删除一条数据 或者多条数据
     * @param int $type 如果为1则表示 删除多条 where in   0则表示单条
     * @return string 返回执行结果
     * @author kevin.liu<www.dayblog.cn>  <791845283@qq.com>
     */
    protected function _modelDelete($key = 'id', $type = 0, $tableName = null)
    {
        if (!$this->model) {
            $this->error("表名未定义");
        }
        $id = I('get.id', 0, 'intval');
        if(empty($id)){
            $this->model_error = '参数错误';
            return false;
        }
        if(is_null($tableName)){
            $tableName = $this;
        }else{
            $tableName = M($tableName);
        }

        $where['status'] =1;
        if($type){
            $where[$key] = array('in',$id);
        }else{
            $where[$key] = $id;
        }
        $res = $tableName -> where($where)->delete();
        if(!$res){
            $this->model_error = '删除失败';
            return false;
        }else{
           return true;
        }
    }

    /**
     * 查询一条数据
     * @param array $where 条件
     * @return mixed
     * @author kevin.liu<www.dayblog.cn>  <791845283@qq.com>
     */
    protected function _modelFind($where, $field = '*')
    {
        if (!$this->model) {
            $this->error("表名未定义");
        }
        $info = $this->model->where($where)->field($field)->find();
        return $info;
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
     * page 分页
     * @param int $count 总条数
     * @param int $num 展示条数
     * @return array 返回组装好的结果
     * @author kevin.liu<www.dayblog.cn>
     **/
    protected function _page($count, $num)
    {
        $showPageNum = 15;
        $totalPage = ceil($count / $num);
        $currentPage = I('post.currentPage', 1, 'intval');
        $searchValue = I('post.searchValue', '');
        if ($currentPage > $totalPage) {
            $currentPage = $totalPage;
        }
        if ($currentPage < 1) {
            $currentPage = 1;
        }
        $list = array(
            'pageNum'     => $num,
            'showPageNum' => $showPageNum,
            'currentPage' => $currentPage,
            'totalPage'   => $totalPage,
            'limit'       => ($currentPage - 1) * $num . "," . $num,
            'searchValue' => $searchValue,
            'pageUrl'     => ''
        );
        return $list;
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
	
	/**
	 *  param 跳转地址
	 * author kevin.liu<www.dayblog.cn>
	 **/
	protected function urlRedirect($url = '/info'){
		$modules = I('get.module');
        if(!empty($modules)){
            delTemp();
        }
        $this -> redirect(MODULE_NAME.'/'.CONTROLLER_NAME.$url);
	}
}
