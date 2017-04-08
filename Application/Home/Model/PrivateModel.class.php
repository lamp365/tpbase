<?php
namespace Home\Model;

use Think\Model;

class PrivateModel extends Model
{
    /**
     * 添加编辑操作
     * @param int $returnData   0 为直接返回(新添加返回最后id 更新返回结果)  1为返回数据数组
     * @param null $tableName
     * @return boolean
     * @author kevin.liu<www.dayblog.cn>  <791845283@qq.com>
     */
    public function _modelAdd($returnData = 0,$tableName = null)
    {
        if(is_null($tableName)){
            $tableName = $this;
        }else{
            $tableName = D($tableName);
        }
        $data = $tableName->create();

        if (empty($data)) {
            return false;
        }
        if (empty($data['id'])) {
            $id = $tableName->add();
            if (!$id) {
                $this->error = "操作失败!";
                return false;
            }
            $data['last_id'] = $id;
            $res = $id;
        } else {
            $res = $tableName->save();
            if (!$res) {
                $this->error = "更新操作失败!";
                return false;
            }
        }

        if ($returnData == 1) {
            return $data;
        }
        return $res;
    }

    /**
     * 查询总条数
     * @param array $where 查询的条件
     * @param int $type 类型 :type =1 分页用 type=2普通查询
     * @return mixed
     * @author kevin.liu<www.dayblog.cn>  <791845283@qq.com>
     */
    public function _modelCount($where = array(), $type = 1, $num = '')
    {
        $count = $this->where($where)->count();
        if ($type == 1) {
            if ($num == '') {
                $num = C('PAGENUM');
            }
            $Page = $this->_page($count, $num);
            return $Page;
        }
        return $count;
    }

    /**
     * 查询多条数据
     * @param array $where 查询的条件
     * @param string $field 要显示的字段
     * @param string $order 排序方式
     * @param string $limit 分页
     * @return array
     * @author kevin.liu<www.dayblog.cn>  <791845283@qq.com>
     */
    public function _modelSelect($where=array(), $field = "*", $order ='',  $limit = '')
    {
        $list = $this->where($where)->limit($limit)->order($order)->field($field)->select();
        return $list;
    }

    /**
     * 删除一条数据 或者多条数据
     * @param array $where
     * @param null $tableName
     * @return bool
     * @author kevin.liu<www.dayblog.cn>  <791845283@qq.com>
     */
    public function _modelDelete($where = array(), $tableName = null)
    {
        //条件
        if(empty($where)){
            $this->error = '条件不能为空！';
            return false;
        }
        if(is_null($tableName)){
            $tableName = $this;
        }else{
            $tableName = D($tableName);
        }

        $res = $tableName -> where($where)->delete();
        if(!$res){
            $this->error = '删除失败';
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
    public function _modelFind($where, $field = '*')
    {
        $this->create();
        $info = $this->where($where)->field($field)->find();
        return $info;
    }

    /**
     * page 分页
     * @param int $count 总条数
     * @param int $num 展示条数
     * @return array 返回组装好的结果
     * @author kevin.liu<www.dayblog.cn>
     **/
    public function _page($count, $num)
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

}