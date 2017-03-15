<?php
namespace Admin\Model;
class AuthCateModel extends \Common\Model\PublicModel
{
    protected $_validate = array(
        array('title', 'require', '标题必须填写'), //默认情况下用正则进行验证
        array('module', 'require', '模块名称必须填写'), //默认情况下用正则进行验证
        array('controller', 'require', '控制器名称必须填写'), //默认情况下用正则进行验证
        array('method', 'require', '方法名称必须填写'), //默认情况下用正则进行验证
        array('sort', 'require', '排序必须填写'), //默认情况下用正则进行验证
        array('sort', 'number', '排序只能为数字'), //默认情况下用正则进行验证
    );

    /**
     * 添加编辑权限方法
     * @return mixed 成功返回数组,失败返回错误消息
     * @author 刘中胜
     */
    public function authedit()
    {
        $data = $this->create();
        if(empty($data)){
            return false;
        }
        if(!empty($data['module'])){
            $data['module'] = letterChange($data['module']);
        }
        if(!empty($data['controller'])){
            $data['controller'] = letterChange($data['controller']);
        }
        if(!empty($data['method'])){
            $data['method'] = letterChange($data['method'],2);
        }
        if(empty($data['id'])){
            $res = $this->add($data);
            $where = array(
                'id'     => $res,
                'status' => 1
            );
            $info = $this->where($where)->find();
            switch ($info['level']) {
                case 0:
                    $dataArr = $info['module'];
                    break;
                case 1:
                    $dataArr = $info['module'] . '/' . $info['controller'];
                    break;
                case 2:
                    $dataArr = $info['module'] . '/' . $info['controller'] . '/' . $info['method'];
                    break;
            }
            $res = $this->where($where)->setField('name', $dataArr);
            if(!$res){
                $this->error = '操作失败';
                return false;
            }
        }else{
            $where = array(
                'id'     => $data['id'],
                'status' => 1
            );
            $info = $this->where($where)->find();
            switch ($info['level']) {
                case 0:
                    $dataArr = array(
                        'module' => $data['module'],
                        'name'   => $data['module']
                    );
                    break;
                case 1:
                    $dataArr = array(
                        'controller' => $data['controller'],
                        'name'       => $info['module'] . '/' . $data['controller']
                    );
                    break;
                case 2:
                    $dataArr = array(
                        'method' => $data['method'],
                        'name'   => $info['module'] . '/' . $info['controller'].'/'.$data['method']
                    );
                    break;
            }
            $dataArr['title'] = $data['title'];
            $dataArr['sort'] = $data['sort'];
            $dataArr['is_menu'] = $data['is_menu'] ? $data['is_menu'] : 0;
            $res = $this->where($where)->save($dataArr);
            if(!$res){
                $this->error = '没有数据被更新';
                return false;
            }
            if($info['level'] == 0){
                $this->allModule($info['id'], $data['module']);
            }else if($info['level'] == 1){
                $this->editAction($info['id'], $data['controller']);
            }
        }

        return $data;
    }

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
     * @author 刘中胜
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