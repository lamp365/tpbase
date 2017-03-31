<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 17-3-15
 * Time: 下午11:20
 */
namespace Home\Controller;
use Think\Controller;
use QL\QueryList;
class IndexController extends Controller{

    public function index(){


        $hj = QueryList::Query('http://dayblog.cn',array('tt'=>array('h2 a','text'),'ll'=>array('h2 a','href')));
//输出结果：二维关联数组
        ppd($hj->data);

        ppd("welcome about you");
    }
}