<?php
namespace Common\Model;

use Think\Model;

class PublicModel extends Model
{
	/**
	 * 获取某些指定用户的指定字段
	 * @param string $file 字段名
	 * @param array where 要查询的条件
	 * @param  $type 默认true
	 * @author kevin.liu dayblog.cn
	 **/
	public function getOneField($file, $where=array(), $type = false,$order=''){
		$result = $this->where($where)->order($order)->getField($file, $type);
		return $result;
	}
    /**
	
     * 查询一条记录
     * @param string $where 查询条件
     * @param string $field 要查询的字段,默认全部
     * @return array 返回查询到的结果
     * @author kevin.liu
     **/
    public function oneInquire($where, $field = '*')
    {
        $info = $this->where($where)->field($field)->find();
        if (!$info) {
            $this->error = '暂无数据';
            return false;
        }
        return $info;
    }

    /**
     * 查询总数
     * @param string $where 查询条件
     * @param string $field 要查询的字段,默认全部
     * @author kevin.liu
     * @return int 总数
     **/
    public function total($where)
    {
        $count = $this -> where($where)->count();
        return $count;
    }

    /**
     * 查询总数
     * @param string $where 查询条件
     * @param string $limit 分页参数
     * @param string $order 排序方式
     * @param string $field 要查询的字段
     * @author kevin.liu
     * @return array 查询到的结果集
     **/
    public function dataSet($where,$order,$field,$limit='')
    {
        $list = $this -> where($where)->limit($limit)->order($order)->field($field)->select();
        return $list;
    }

    /**
     * 更改一条数据
     * @param string $where 查询条件
     * @param string $limit 分页参数
     * @param string $value 修改的值
     * @author kevin.liu
     * @return bool 
     **/
    public function onesave($where,$field,$value)
    {
        $res = $this->where($where)->setField($field,$value);
        return $res;
    }

	/**
     * 删除数据(软删除)
     * @param int $id 要删除的数据id默认0
     * @param string $tableName 表名默认为空
	 * @param string $key 查询的key,默认id
     * @author kevin.liu
     * @return bool 
     **/
	public function del($id = 0,$key = 'id', $type = 0, $tableName = null)
	{
		if(empty($id)){
			$this -> error = '参数错误';
			return false;
		}
		if(is_null($tableName)){
			$tableName = $this;
		}else{
			$tableName = M($tableName);
		}
		
		$where['status'] =1;
		if($type == 1){
			$where[$key] = array('in',$id);
		}else{
			$where[$key] = $id;
		}
		$res = $tableName -> where($where)->setField('status',0);
		if(!$res){
            $this->error = "删除失败";
            return false;
        }
		return $res;
	}
	
	/**
	 * edit 添加编辑页面
	 * @author kevin.liu
	 **/
    public function edit()
    {
        $data = $this->create();
        if (empty($data)) {
            return false;
        }
        if (empty($data['id'])) {
            $id = $this->add();
            if (!$id) {
                $this->error = '添加操作失败';
                return false;
            }
        } else {
            $res = $this->save();
            if ($res === false) {
                $this->error = '更新操作失败';
                return false;
            }
        }
        return $data;
    }

    /**
     * 分类添加编辑页面
     * @author kevin.liu
     **/
    public function editCate(){
        $data = $this -> create();
        if(empty($data)){
            return false;
        }
        $id = $this -> add($data);
        if(!$id){
            $this -> error = '添加操作失败';
            return false;
        }
         return $data;
    }

    /**
     * 删除分类
     * @author kevin.liu
     **/
    public function delcate()
    {
        $id = I('get.id',0,'intval');
        if(empty($id)){
            $this -> error = '参数错误';
            return false;
        }
        $where = array(
            'pid' => $id,
            'status' => 1
        );
        $res = $this -> where($where)->getField('id');
        if($res){
            $this -> error = '该分类下拥有子分类无法删除';
            return  false;
        }
        $where = array(
            'status' => 1,
            'id' => $id
        );
        $res  = $this -> where($where)->setField('status',0);
        if($res){
            delTemp();
            return true;
        } else{
            $this -> errro = '参数错误';
            return false;
        }

    }

    /**
     * 更新分类操作
     * @author kevin.liu
     **/
    public function updateCate()
    {
        $data = $this -> create();
        if(empty($data)){
            return false;
        }

        $res   = $this ->  save();
        if($res){
            delTemp();
            return true;
        }else{
            return false;
        }
    }
}