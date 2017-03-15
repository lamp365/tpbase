<?php
// +----------------------------------------------------------------------
// | 该控制器用于文章的管理，包括分类，文章等功能的管理
// +----------------------------------------------------------------------
// | Copyright (c) www.php63.cc All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 普罗米修斯 <996674366@qq.com>
// +----------------------------------------------------------------------
namespace Content\Controller;
class ArticleController extends PublicController{
	public function cate(){
		self::_cateList('ArticleCate','文章分类','sort ASC', 'article_cate');
        $this -> display();
	}
	
	/**
     * 分类操作按钮控制
     * @author 刘中胜
     * @time 2016-01-21
     **/
    public function cateinfo()
    {
        $this->model = D('ArticleCate');
        $id = I('get.id', 0, 'intval');
        if($id != 0){
            $where = array(
                'id'    => $id,
                'statu' => 1
            );
            $info = self::_oneInquire($where,2);
        }else{
            $info['id'] = 0;
        }

        if(self::_is_check_url('edit',1)){//临时
            $info['butadd'] = self::_catebut('edit', '添加分类', $info['id']);
        }
        if(self::_is_check_url('catedel',1)){//临时
            $info['butdel'] = self::_catebut('catedel', '删除分类', $info['id'], '您确认要删除该分类吗?', 2);
        }
        $this->assign('info', $info);
        $this->display();
     }
	    /**
     * 分类添加编辑
     * @author 刘中胜
     * @time 2015-12-09
     */
    public function edit()
    {
        $model = D('ArticleCate');
        if(IS_POST){
            $data = $model->editCate();
            if($data){
                delTemp();
                $this->success($data['id'] ? '更新成功' : '添加成功', U('cate'));
            }else{
                $this->error($model->getError());
            }
        }
        $wherer = array(
            'status' => 1
        );
        $info['maxsort'] = M('article_cate')->where($where)->Max('sort');
        $this -> assign('info',$info);
        $this->display();
    }

   /**
     * upcate 更新分类
     * @author 刘中胜
     * @time 2016-01-29
     **/
    public function upcate(){
        $data  = D('ArticleCate') -> updateCate();
        if($data){
            $this -> success('操作成功',U('cate'));
        }
        $this -> error('操作失败');
    }

    /**
     * 删除分类
     * @author 刘中胜
     * @time 2016-01-29
     **/
    public function catedel()
    {
        $model = D('ArticleCate');
        $res = $model ->delcate();
        if($res){
            $this -> success('操作成功',U('cate'));
        }
		$this -> error($model->getError());
    }
}