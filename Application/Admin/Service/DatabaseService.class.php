<?php
/**
 * Created by PhpStorm.
 * User: kevin.liu dayblog.cn
 */
namespace Admin\Service;

use Think\Model;

class DatabaseService extends Model{
    protected $autoCheckFields = False;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取表的信息并组装创建表的命令
     * array $list 表数组
     * @author kevin.liu
     **/
    public function createTable($list = array())
    {
        if(empty($list)){
            //获得所有表
            $list = $this -> tableArr();
        }

        $top_info = "/**".PHP_EOL;
        $top_info .= "* MySQL Database Backup Tool".PHP_EOL.PHP_EOL;
        $top_info .= "* 开发作者：kevin.liu dayblog.cn".PHP_EOL.PHP_EOL;
        $top_info .= "* 生成日期：" . date('Y-m-d H:i:s', time()) .PHP_EOL;
        $top_info .= "**/".PHP_EOL.PHP_EOL;
        $top_info .= "SET FOREIGN_KEY_CHECKS=0;".PHP_EOL;  //取消外键关联

        //读取创建表信息
        $create_sql = $top_info;

        $db_name     = C('DB_NAME');
        $create_sql .= "CREATE DATABASE IF NOT EXISTS `{$db_name}`;".PHP_EOL;
        $create_sql .= "use `{$db_name}`;".PHP_EOL.PHP_EOL;

        foreach ($list as $table) {
            $table_info = M()->query("SHOW CREATE TABLE $table");
            $create_table_sql = "Drop table IF EXISTS `{$table}`;".PHP_EOL;
            $create_table_sql .= $table_info[0]['create table'];

            $create_sql .= $create_table_sql;
            $create_sql .=";".PHP_EOL.PHP_EOL;
        }
        return $create_sql;
    }

    /**
     * 组装插入语句
     * @param array $list  表数组
     * @return string
     */
    public  function insertSql($list = array())
    {
        if(empty($list)){
            //获得所有表
            $list = $this -> tableArr();
        }
        //插入数据库
        $insert_sql = '';
        foreach ($list as $table) {
            $res_data = M()->query("SELECT * FROM {$table}");
            if(!$res_data){
                continue;
            }
            $insert_sql .= "INSERT INTO `{$table}` VALUES ".PHP_EOL;
            foreach ($res_data as $key => $row_arr) {
                $insert_sql .= '(';
                foreach ($row_arr as $row_key => $val) {
                    if ($val === null) {
                        $insert_sql .= "NULL,";
                    }else {
                        //转换特殊字符
                        // $val = mysql_real_escape_string($val);
                        $insert_sql .= "'$val',";
                    }
                }

                $insert_sql = rtrim($insert_sql,',');
                $insert_sql .= "),".PHP_EOL;
            }

            $insert_sql = mb_substr($insert_sql, 0, -2);
            $insert_sql .= ";".PHP_EOL.PHP_EOL;
        }
        return $insert_sql;
    }

    /**
     * 数据库还原
     * @param $tableArr
     * @param $filename
     * @return array
     */
    public function huanyuan($tableArr,$filename)
    {
        //先删除数据表
        $tb = '';
        foreach ($tableArr as $table) {
            $tb .= "`$table`,";
        }
        $tb  = rtrim($tb,',');
        $res = M()->execute("DROP TABLE `{$tb}` IF EXISTS");
        if (!$res) {
            $this->error = "删除{$tb}表失败了!";
            return false;
        }

        //执行SQL
        $str = file_get_contents($filename);
        //去除以上顶部的无用信息
        $preg   = '/(\/\*.*\*\/)/sU';
        $newstr = preg_replace($preg,'',$str);

        $sql_arr = explode(';', $newstr);
        foreach ($sql_arr as $one_sql) {
            $res = M()->query($one_sql);
            if (!$res) {
                $this->error = "还原失败：{$one_sql}";
                return false;
            }
        }
        return true;
    }

    /**
     * 获取数据库所有表
     * @author kevin.liu
     **/
    public function tableArr()
    {
        //读取数据库信息
        $tablesList = M()->query('SHOW TABLES');
        $list = array();
        foreach ($tablesList as $key => $value) {
            foreach ($value as $k=>$table) {
                $list[$key] = $table;
            }
        }
        return $list;
    }

    /**
     * 检查备份目录 并得到当前要备份的sql名字
     * @param $table_arr
     * @return bool|string
     */
    public function checkDataDir($table_arr){
        $dirname = './Database';
        if(!is_dir($dirname)){
            $dir = @mkdir($dirname, 0777, true);
            if(!$dir){
                $this->error = '创建文件失败！';
                return false;
            }
        }

        if(is_array($table_arr)){
            $sqlfile = $dirname.'/'.date('YmdHis',time()).'.sql';
        }else{
            //如果是单个表的话 用表名命名
            $sqlfile = $dirname.'/'.date('YmdHis',time())."_{$table_arr}.sql";
        }

        return $sqlfile;
    }


}