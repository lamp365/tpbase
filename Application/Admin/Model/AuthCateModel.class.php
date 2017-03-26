<?php
namespace Admin\Model;

class AuthCateModel extends PrivateModel
{
    protected $_validate = array(
        array('title', 'require', '标题必须填写'), //默认情况下用正则进行验证
        array('module', 'require', '模块名称必须填写'), //默认情况下用正则进行验证
        array('controller', 'require', '控制器名称必须填写'), //默认情况下用正则进行验证
        array('method', 'require', '方法名称必须填写'), //默认情况下用正则进行验证
        array('sort', 'require', '排序必须填写'), //默认情况下用正则进行验证
        array('sort', 'number', '排序只能为数字'), //默认情况下用正则进行验证
        array('id', 'number', 'id参数有误！'), //默认情况下用正则进行验证
    );


    /**
     * 修改模块
     * @param $id 要修改的id
     * @param $url 要修改的模块名称
     * @return bool 成功返回true 失败返回false
     */
    public function allModule($id, $url)
    {
        $where = array(
            'status' => 1,
            'pid'    => array('in', $id)
        );
        $pid = $this->where($where)->getField('id', true);

        if(!empty($pid)){
            foreach ($pid as $key => $value) {
                $where = array(
                    'status' => 1,
                    'id'     => $value
                );
                $info = $this->where($where)->find();
                switch ($info['level']) {
                    case 1:
                        $dataArr = array(
                            'module' => $url,
                            'name'   => $url . '/' . $info['controller']
                        );
                        break;
                    case 2:
                        $dataArr = array(
                            'module' => $url,
                            'name'   => $url . '/' . $info['controller'] . '/' . $info['method']
                        );
                        break;
                }
                $res = $this->where($where)->save($dataArr);
                $info = $this->where($where)->find();
                if($res){
                    $this->allModule($info['id'], $info['module']);
                }else{
                    $this->error = '没有数据被更新';
                    return false;
                }
            }
        }

    }

    /**
     * 更新控制器路径
     * @param int $id 权限id
     * @param $Action 权限控制器路径
     * @return bool
     * @author kevin.liu
     */
    public function editAction($id, $Action)
    {
        $where = array(
            'status' => 1,
            'pid'    => array('in', $id)
        );
        $pid = $this->where($where)->getField('id', true);
        if(!empty($pid)){
            foreach ($pid as $key => $value) {
                $where = array(
                    'status' => 1,
                    'id'     => $value
                );
                $info = $this->where($where)->find();
                switch ($info['level']) {
                    case 1:
                        $dataArr = array(
                            'name' => $info['module'] . '/' . $Action
                        );
                        break;
                    case 2:
                        $dataArr = array(
                            'controller' => $Action,
                            'name'       => $info['module'] . '/' . $Action . '/' . $info['method']
                        );
                        break;
                }
                $res = $this->where($where)->save($dataArr);
                $info = $this->where($where)->find();
                if($res){
                    $this->editAction($info['id'], $info['controller']);
                }else{
                    $this->error = '没有数据被更新';
                    return false;
                }
            }
        }
    }
}