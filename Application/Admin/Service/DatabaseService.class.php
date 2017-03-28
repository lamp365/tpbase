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
     * @author kevin.liu
     **/
    public function createTable()
    {
        //获得所有表
        $list = $this -> tableArr();

        $top_info =  "--| MySQL Database Backup Tool".PHP_EOL.PHP_EOL;
        $top_info .= "--| 开发作者：kevin.liu dayblog.cn".PHP_EOL.PHP_EOL;
        $top_info .= "--| 生成日期：" . date('Y-m-d H:i:s', time()) .PHP_EOL.PHP_EOL;
        $top_info .= "--|".PHP_EOL.PHP_EOL;
        $top_info .= "SET FOREIGN_KEY_CHECKS=0;".PHP_EOL;  //取消外键关联

        //读取创建表信息
        $create_sql = $top_info;

        $db_name     = C('DB_NAME');
        $create_sql .= "CREATE DATABASE IF NOT EXISTS `{$db_name}`;".PHP_EOL;
        $create_sql .= "use `{$db_name}`;".PHP_EOL.PHP_EOL;

        foreach ($list as $table) {
            $table_info = M()->query("SHOW CREATE TABLE $table");
            $create_table_sql = $table_info[0]['create table'];

            $create_sql .= $create_table_sql;
            $create_sql .=";".PHP_EOL.PHP_EOL;
        }
        return $create_sql;
    }

    //组装插入语句
    public  function insertSql()
    {
        $list = $this -> tableArr();
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

            $insert_sql = mb_substr($insert_sql, 0, -3);
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
        $res = M()->execute("DROP TABLE $tb");
        if (!$res) {
            return array('code'=>'1002','msg'=>'删除表失败了!');
        }

        //执行SQL
        $str = file_get_contents($filename);
        //去除以上顶部的无用信息


        $sql_arr = explode(';', $str);
        foreach ($sql_arr as $one_sql) {
            $res = M()->query($one_sql);
            if (!$res) {
                return array('code'=>'1002','msg'=>"还原失败：{$one_sql}");
            }
        }

        return array('code'=>'200','msg'=>"还原成功！");
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
}