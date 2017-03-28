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
            @chmod($sqlfile,0777);
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
        $path = './Database';
        $datalist = '';
        if (!is_dir($path)) {
            $this->error("{$path}不是目录！");
        }

//      $listSqls = glob($path.'/*.sql');

        $flag = \FilesystemIterator::KEY_AS_FILENAME;
        $glob = new \FilesystemIterator($path, $flag);
        //20161112145639.sql
        //20161112145639_squsian_main.sql
        if (!empty($glob)) {
            foreach ($glob as $name => $file) {
//              ppd($name,$file->getBaseName(), $file->getSize());
                if (strpos($name, '_')) {
                    $arr = explode('_', $name);
                } else {
                    $arr = explode('.', $name);
                }
                $timecode = $arr['0'];
                $timeArr = sscanf($timecode, '%4s%2s%2s%2s%2s%2s');

                $date = "{$timeArr[0]}-{$timeArr[1]}-{$timeArr[2]}";
                $time = "{$timeArr[3]}:{$timeArr[4]}:{$timeArr[5]}";
                $datalist[] = array(
                    'name' => $name,
                    'size' => $file->getSize(),
                    'date' => $date,
                    'time' => $time
                );
            }
        }else{
            $datalist = array();
        }
        //按照大小排序
        $datalist = mulity_array_sort($datalist,'size','desc');
        $this->assign('datalist',$datalist);
        $this->display();
    }


    public function dopost()
    {
        $begin_time = microtime(true);
        $sqlName = I('post.name');
        if(empty($sqlName)){
            $this->showAjax('参数有误！',1002);
        }
        //解析是恢复单张表还是整站表
        $sql_arr = explode('_', $sqlName);
        if(count($sql_arr) == 1){
            //整站表
            $db_service = D('Database','Service');
            $tableArr  = $db_service->tableArr();
        }else{
            $table = '';
            foreach($sql_arr as $one){
                $table .= $one.'_';
            }
            $tableArr   = array();
            $tableArr[] = rtrim($table,'_');
        }
        $filename = "./Database/".$sqlName;

        $result = $db_service->huanyuan($tableArr,$filename);
        if($result['code'] == 200){
            $end_time = microtime(true);
            $diff_time = round($end_time-$begin_time,3);
            $this->showAjax($result['msg']."共耗时:{$diff_time} ms");
        }else{
            $this->showAjax($result['msg'],1002);
        }
    }

    public function delsql()
    {
        $sqlName = I('post.name');
        if(empty($sqlName)){
            $this->showAjax('参数有误！',1002);
        }
        $filename = "./Database/".$sqlName;
        if(!file_exists($filename)){
            $this->showAjax('sql文件已不存在！',1002);
        }
        @unlink($filename);
        $this->showAjax('sql文件删除成功！');
    }
}
