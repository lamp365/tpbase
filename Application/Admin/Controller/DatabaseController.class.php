<?php
// +----------------------------------------------------------------------
// | 后台管理员,系统分组,权限分配
// +----------------------------------------------------------------------
// | Copyright (c) www.dayblog.cn All rights reserved.
// | Author: kevin.liu <791845283@qq.com>
// +----------------------------------------------------------------------
namespace Admin\Controller;
class DatabaseController extends PrivateController
{
    public function lists()
    {
        $tablesList = M()->query('show tables');
        $dataArr    = array();
        foreach ($tablesList as $key => $value) {
            foreach ($value as $k => $table) {
                $tableInfo       = M()->query("show table status like '{$table}' ");
                $db_size         = tosize($tableInfo[0]['index_length'] + $tableInfo[0]['data_length']);
                $dataArr[$key]   = $tableInfo[0];
                $dataArr[$key]['db_size'] = $db_size;
            }
        }
        $this->assign('dataArr',$dataArr);
        $this->display();
    }

    public function backup_db()
    {
        $begin_time = microtime(true);

        $dirname = './Database';
        if(!is_dir($dirname)){
            $dir = @mkdir($dirname, 0777, true);
            if(!$dir){
                $this->showAjax('创建文件失败！',1002);
            }
        }

        $sqlfile = $dirname.'/'.date('YmdHis',time()).'.sql';

        $db_service = D('Database','Service');
        //获取创建表语句
        $create_sql = $db_service->createTable();
        //写入文件
        $res = file_put_contents($sqlfile, $create_sql);
        if(!$res) {
            @unlink($sqlfile);
            $this->showAjax('备份失败,无法写入', 1002);
        }

        $insert_sql = $db_service->insertSql();
        $res = file_put_contents($sqlfile, $insert_sql, FILE_APPEND);
        if($res){
            $end_time  = microtime(true);
            $diff_time = round($end_time-$begin_time,3);
            $this->showAjax("备份成功！共耗时：{$diff_time} ms");
        }else{
            @unlink($sqlfile);
            $this->showAjax('备份失败,无法写入',1002);
        }

    }

    public function huanyuan()
    {

    }

}
