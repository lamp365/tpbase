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
    public $model = null;
    private $auth = null;
    private $group_id = array();

    /**
     * 初始化方法
     * @auth kevin.liu www.dayblog.cn
     **/
    public function _initialize()
    {
        //获取到当前用户所属所有分组拥有的权限id
        $this->group_id = self::_rules();
        $UserName = session(C('USERNAME'));
        //检测后台管理员昵称是否存在，如果不等于空或者0则获取配置文件里定义的name名字并分配给首页
        if (!empty($UserName)) {
            $this->assign('UserName', session(C('USERNAME')));
        }
        //分配左边菜单
        $this->_left_menu();
        //分配列表上方菜单
        $this->_top_menu();
        //分配网站顶部菜单
        $this->_web_top_menu();
        //检测是否为超级管理员
        if (UID == C('ADMINISTRATOR')) {
            return true;
        }
        //读取缓存名为check_iskey+uid的缓存
        $key = MODULE_NAME.'/'. CONTROLLER_NAME . '/' . ACTION_NAME;
        $where = array(
            'name'   => $key,
            'status' => 1
        );
        $iskey = M('auth_cate')->where($where)->getField('id');
		//检测该规则id是否存在于分组拥有的权限里
		if(!empty($iskey) && !in_array($iskey,$this -> group_id)){
			$this->auth = new Auth();
			if(!$this->auth->check($key, UID)){
				$url = getU('Public/login');
				//如果为ajax请求，则返回301，并且跳转到指定页面
				if(IS_AJAX){
					session('[destroy]');
					$data = array(
						'statusCode' => 301,
						'url'        => $url
					);
					die(json_encode($data));
				}
				session('[destroy]');
				$this->redirect($url);
			}
		}
    }

    /**
     * 添加编辑操作
     * @param string $model 要操作的表
     * @param string $url 要跳转的地址
     * @param int $typeid 0 为直接跳转 1为返回数组
     * @return boolean
     * @author kevin.liu<www.dayblog.cn>  <791845283@qq.com>
     */
    protected function _modelAdd($url = '', $typeid = 0)
    {
        if (!$this->model) {
            $this->error('请传入操作表名');
        }
        $data = $this->model->edit();
        if ($typeid == 1) {
            return $data;
        }
        $data ? $this->success($data['id'] ? '更新成功' : '添加成功', U($url)) : $this->error($this->model->getError());
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
        $count = $this->model->total($where);
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
    protected function _modelSelect($where, $order, $field = "*", $limit = '')
    {
        if (!$this->model) {
            $this->error("表名未定义");
        }
        $list = $this->model->dataSet($where, $order, $field, $limit);
        return $list;
    }

    /**
     * 删除一条数据
     * @param string $url 跳转地址
     * @param int $type 如果为1则表示删除后还有其他操作
     * @return string 返回执行结果
     * @author kevin.liu<www.dayblog.cn>  <791845283@qq.com>
     */
    protected function _del($url)
    {
        if (!$this->model) {
            $this->error("表名未定义");
        }
        $id = I('get.id', 0, 'intval');
        $res = $this->model->del($id);
        if (!$res) {
            $this->error($this->model->getError());
        }
        delTemp();
        $this->success('删除成功', U($url));
    }

    /**
     * 查询一条数据
     * @param array $where 条件
     * @param $max 是否查询最大的排序字段
     * @param int $type 默认为1：分配到模板 ，其他返回
     * @return mixed
     * @author kevin.liu<www.dayblog.cn>  <791845283@qq.com>
     */
    protected function _oneInquire($where, $type = 1)
    {
        if (!$this->model) {
            $this->error("表名未定义");
        }
        $info = $this->model->oneInquire($where);
        if (!$info) {
            $this->error($this->model->getError());
        }
        if ($type == 1) {
            return $this->assign('info', $info);
        }
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
     * 左边菜单
     * @author kevin.liu<www.dayblog.cn>
     **/
    public function _left_menu()
    {
        $url = S('left_menu');
        if ($url == false) {
            $where = array(
                'status' => 1,
                'level'  => 1,
                'module' => MODULE_NAME
            );
            if (UID != C('ADMINISTRATOR')) {
                $where['id'] = array('in', $this->group_id);
            }
            $model = M('auth_cate');
            $url = $model->where($where)->order('sort DESC')->select();
            foreach ($url as $key => &$value) {
                $where = array(
                    'pid' => $value['id'],
                    'status' => 1,
                    'is_menu' => array('neq',0)
                );
                $info = $model->where($where)->count();
                if($info){
                    array_splice($url, $key,1);
                }else{
                    $urls = $value['name'] . '/index';
                    $value['name'] = U($urls); 
                }
            }
            unset($value);
            S('left_menu' . UID, $url);
        }
        $this->assign('menu_url', $url);
    }

    /**
     * 列表上方菜单
     * @author kevin.liu<www.dayblog.cn>
     **/
    public function _top_menu()
    {
        $module_name = MODULE_NAME;
        $controller  = CONTROLLER_NAME;
        $where = array(
            'status'     => 1,
            'level'      => 2,
            'is_menu'    => 0,
            'module'     => $module_name,
            'controller' => $controller
        );
        if (UID != C('ADMINISTRATOR')) {
            $where['id'] = array('in', $this->group_id);
        }
        $url = M('auth_cate')->where($where)->field('module,controller,method,title,name')->order('sort DESC')->select();
        //检测控制器是不是等于Index
        if ($controller == 'Index') {
            $arr = array(
                'module'     => $module_name,
                'controller' => 'Index',
                'method'     => 'index',
                'title'      => '站点信息',
                'name'       => $module_name . '/Index/index'
            );
            array_unshift($url, $arr);
        }
        $this->assign('top_menu_url', $url);
    }


    /**
     * 网站顶部菜单
     * @author kevin.liu<www.dayblog.cn>
     **/
    public function _web_top_menu()
    {
        $model = M('auth_cate');
        $url = S('web_top_menu' . UID);
        //检测缓存是否存在,如果不存在则生成缓存
        if ($url == false) {
            $where = array(
                'status' => 1,
                'level'  => 0,
            );
            if (UID != C('ADMINISTRATOR')) {
                $where['id'] = array('in', $this->group_id);
            }
            $dataArr = $model->where($where)->select();
            $module = array();
            foreach ($dataArr as $key => $value) {
                $where = array(
                    'pid'    => $value['id'],
                    'status' => 1
                );
                $res = $model->where($where)->getField('id');
                if ($res) {
                    $module[] = $value['id'];
                }
            }
            if (!empty($module)) {
                $where = array(
                    'id'     => array('in', $module),
                    'status' => 1
                );
                $url = $model->where($where)->field('id,title,module')->order('sort DESC')->select();
                foreach ($url as $key => &$value) {
                    $where = array(
                        'pid'    => $value['id'],
                        'status' => 1
                    );
                    $str = $model->where($where)->getField('module');
                    $value['url'] = U($str . '/Index/index', array('module' => MODULE_NAME));
                }
                unset($value);
                //生成缓存
                S('web_top_menu' . UID, $url);
            }
        }
        if (count($url) > 1) {
            $this->assign('web_top_menu_url', $url);
        }
    }


    /**
     * 权限判断 所有一级菜单点击都进入这个方法
     * @author kevin.liu<www.dayblog.cn>
     * @time 2016-06-15
     **/
    public function index()
    {
        $url = MODULE_NAME . '/' . CONTROLLER_NAME;
        $where = array(
            'a.name' => $url,
            'a.level'=> 1,
            'b.status' => 1
        );
        if(UID != C('ADMINISTRATOR')){
            $where['b.id']   = array('in',$this->group_id);
        }
        $info = M()
            -> table('__AUTH_CATE__ a')
            -> join('LEFT JOIN __AUTH_CATE__ b ON a.id=b.pid')
            -> where($where)
            -> order('b.sort DESC')
            -> getField('b.name');
        $this->redirect($info);
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
