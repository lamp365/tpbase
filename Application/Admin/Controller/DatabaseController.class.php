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

    /**
     * 备份数据库 可能是当张表  也可以是整个数据库 或者多张表
     * 单张表会以表名字命名，多张表则不会带有名字
     */
    public function backup_db()
    {
        $begin_time = microtime(true);
        if(empty(I('post.table'))){
            $this->showAjax('请选择要备份的数据！',1002);
        }
        $tables_arr = I('post.table');

        $db_service = D('Database','Service');

        //创建备份目录 并得到sql文件名
        $sqlfile = $db_service->checkDataDir($tables_arr);
        if(!$sqlfile){
            $this->showAjax($db_service->getError(),1002);
        }

        if(!is_array($tables_arr)){
            $tables_arr   = array();
            $tables_arr[] = I("post.table");
        }
        //获取创建表语句
        $create_sql = $db_service->createTable($tables_arr);
        //写入文件
        $res = file_put_contents($sqlfile, $create_sql);
        if(!$res) {
            @unlink($sqlfile);
            $this->showAjax('备份失败,无法写入', 1002);
        }

        $insert_sql = $db_service->insertSql($tables_arr);
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

    /**
     * 生成模型
     */
    public function build_model()
    {
        $model = I('post.table');
        if(empty($model)){
            $this->showAjax('参数有误！',1002);
        }

        $module = MODULE_NAME;
        $model  = str_replace(C('DB_PREFIX'),'',$model);
        $model  = ucfirst($model);

        $model_arr = explode('_',$model);
        if(count($model_arr) != 1){
            $model = '';
            foreach($model_arr as $word){
                $model .= ucfirst($word);
            }
        }

        $filename   = APP_PATH.$module.'/Model/'.$model."Model.class.php";

        if(file_exists($filename)){
            $this->showAjax('文件已经存在！',1002);
        }
        $str    = "<?php ".PHP_EOL;
        $str   .= 'namespace '.$module."\\Model;".PHP_EOL;
        $str   .= 'class '.$model."Model extends PrivateModel { ".PHP_EOL.PHP_EOL;
        $str   .= '}';

        if (!$head = fopen($filename, "w+")) {//以读写的方式打开文件，将文件指针指向文件头并将文件大小截为零，如果文件不存在就自动创建
            $this->showAjax("尝试打开文件失败!",1002);
        }
        if (fwrite($head,$str)==true) {//执行写入文件
            fclose($head);
        }else{
            $this->showAjax('写入失败！',1002);
        }
        fclose($head);
        $this->showAjax('操作成功！');
    }
}
