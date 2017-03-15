<?php
namespace  Admin\Controller;
class ModelController extends PrivateController
{
    /**
     * 数据表列表
     * @author 刘中胜
     * @time 2016-01-21
     **/
    public function mysqllist()
    {
        $tablesList = M()->query('show tables');
        $dataArr = array();
        foreach ($tablesList as $key => $value) {
            foreach ($value as $k => $v) {
                $dataInfo = M()->query("show table status like '{$v}' ");
                // $count = M()->query("show columns from {$v}");
                foreach ($dataInfo as $kk =>$vv) {
                    $dataArr[$key]=$vv;
                }

            }
        }

        $list = self::_page(count($dataArr),15);
        $limit = explode(',',$list['limit']);
        $but = array(
            array(
                'url'   => 'found',
                'name'  => '新 增',
                'title' => '新增数据表',
                'type'  => 1
            ),
            array(
                'url'   => 'backup',
                'name'  => '备 份',
                'title' => '备份数据库',
                'type'  => 1
            ),
            array(
                'url'   => 'remove',
                'name'  => '清 空',
                'title' => '清空数据库',
                'type'  => 1
            ),
        );
        self::isBut($but);
        $dataArrs = array_slice($dataArr,intval($limit[0]),intval($limit[1]));
        $toolsOptionArr = array(
            array('生成model',1,'生成model','addmodel', U('addmodel', array('id' => '___id___'))),
        );
        $toolsOption = self::_listBut($toolsOptionArr);
            $thead = array(
                array(
                    'name'  => '表名',
                    'width' => '130',
                    'field' => 'name',
                    'align' => 'left',
                    'order' => 'desc'
                ),
                array(
                    'name'  => '类型',
                    'width' => '50',
                    'field' => 'engine',
                    'order' => 'desc'
                ),
                array(
                    'name'  => '编码',
                    'width' => '90',
                    'field' => 'collation',
                    'order' => 'desc'
                ),
                array(
                    'name'  => '备注',
                    'width' => '120',
                    'field' => 'comment',
                    'align' => 'left',
                    'order' => 'desc'
                ),
                array(
                    'name'  => '创建时间',
                    'width' => '120',
                    'field' => 'create_time',
                    'order' => 'desc'
                ),
                array(
                    'name'  => '操作',
                    'width' => '60',
                    'field' => '__TOOLS',
                    'type'  => 'TOOLS'
                ),
            );
        foreach ($thead as $key => $value) {
            if(empty($toolsOption) && $value['field'] == '__TOOLS'){
                unset($thead[$key]);
            }
        }
        $list['toolsOption'] = $toolsOption;
        $list['thead'] = $thead;
        $list['tbody'] = $dataArrs;
        if(IS_POST){
            $list['statusCode'] = 200;
            $list['message'] = '操作成功';
            die(json_encode($list));
        }
        $this->assign('list', json_encode($list));
        $this->display('Public/list');
    }

    /**
     * 备份数据库
     * @author 刘中胜
     * @time 2016-01-22
     **/
    public function backup()
    {
        if(IS_POST){
            $password = trim(I('post.password',''));
            if($password == ''){
                $this -> error('请输入操作密码');
            }

            if($password != C('OPERATING_PASSWORD')){
                $this -> error('操作密码错误');
            }
            $dirname = C('BACKUP_URL');
            $dir = file_exists($dirname);
            if($dir == false){
                mkdir(dirname($dirname), 0755, true);
            }
            $time = time();
            $url = $dirname.'/'.date('YmdHis',$time.'.sql');
            $res =$this -> sqlBackup($url);
            if($res){
                $data = array(
                    'addtime'   =>  $time,
                    'url'       =>  $url,
                    'username'  =>  'admin'
                );
                $res = M('backup')->add($data);
                if(!$res){
                    $this -> error('备份失败');
                }
                $this -> success('备份成功',U('mysqllist'));
            }else{
                $this -> error('备份失败');
            }
        }
        $this->display();
    }


    /**
     * 截断数据库
     * @author 刘中胜
     * @time 2016-01-23
     **/
    public function remove()
    {
        if(IS_POST){
            $password = trim(I('post.password',''));
            if($password == ''){
                $this -> error('请输入操作密码');
            }
            if($password != C('OPERATING_PASSWORD')){
                $this -> error('操作密码错误');
            }
            $system_mysql_table_list = C('SYSTEM_MYSQL_TABLE');
            $db_prefix = C('DB_PREFIX');
            $system_mysql_table_lists = array();
            foreach ($system_mysql_table_list as $key => $value) {
                $system_mysql_table_lists[] = $db_prefix.$value;
            }
            $list = $this -> tableArr();
            foreach ($list as $key => $value) {
                if(!in_array($value, $system_mysql_table_lists)){
                    M()->execute("truncate table `$value`");
                }
            }
            $this -> success('操作完成',U('mysqllist'));
        }
        $this -> display();
    }

/**
 * 获取数据库信息
 * @author 刘中胜
 * @time 2016-01-23
 **/
private function tableArr()
{
    //读取数据库信息
    $tablesList = M()->query('SHOW TABLES');
    $list = array();
    foreach ($tablesList as $key => $value) {
        foreach ($value as $k=>$v) {
            $list[$key]=$v;
        }
    }
    return $list;
}

/**
 * 获取表的信息并组装创建表的命令
 * @author 刘中胜
 * @time 2016-01-23
 **/
private function createTable()
{
    $list = $this -> tableArr();
    //读取表信息
    $sql = '';
    foreach ($list as $v) {
        $table=M()->query("SHOW CREATE TABLE $v");
        foreach ($table as $key => $value) {
            $sql.= $value['create table'];
            $sql.=";-- <xjx> --\r\n\r\n";
        }
    }
    return $sql;
}

//组装插入语句
private  function insertSql()
{
    $list = $this -> tableArr();
    //插入数据库
    $insql='';    
    foreach ($list as $v) {
        $rs=M()->query("SELECT * FROM $v");
        if(!$rs){
            continue;
        }   
        $insql.="INSERT INTO `$v` VALUES\r\n";  
        foreach ($rs as $key => $value) {
            $insql.='(';
                foreach ($value as $ke => $val) {
                    if ($val===null) {
                        $insql.="NULL,";
                    }else {
                        //转换特殊字符
                        // $val=mysql_real_escape_string($val);
                        $insql.="'$val',";
                    } 
                }
            $insql.="),\r\n";
        }
        $insql=mb_substr($insql, 0, -3);
        $insql.=";";    
    }

    return $insql;
}

    /**
     * 备份 ...
     * @param $filename 文件路径
     */
    private  function sqlBackup($filename) {
        $sql=$this->createTable();
        $sql2=$this->insertSql();        
        $data=$sql.$sql2;
        $res = file_put_contents($filename, $data);
        if($res){
            return true;
        }
        return false;
    }

    /**
     * 还原 ...
     * @param $filename 文件路径
     */
    function huanyuan($filename='20160123.sql') {
        //删除数据表
        $list=$this->tableArr();
        $tb='';
        foreach ($list as $v) {
            $tb.="`$v`,";
        }

        $tb=mb_substr($tb, 0, -1);
        if ($tb) {
            $rs=M()->execute("DROP TABLE $tb");
            if ($rs===false) {
                return false;
            }
        }

        //执行SQL
        $str=file_get_contents($filename);
        $arr=explode('-- <xjx> --', $str);
        array_pop($arr);

        foreach ($arr as $v) {
            $rs=M()->query($v);
            if ($rs===false) {
                return false;
            }
        }

        return true;
    }


    /**
     * 添加表操作
     * @author 刘中胜
     **/
    public function found()
    {
        if(IS_POST){
            //取得表名
            $tablename = I('post.tablename','');
            if($tablename == ''){
                $this -> error('表名不允许为空');
            }
            $tablename = C('DB_PREFIX').letterChange($tablename,2);
            //取得表类型
            $tabletype = I('post.tabletype','');
            if(empty($tablename)){
                $this -> error('表类型错误');
            }
            //取得编码格式
            $coding = I('post.coding','');
            if(empty($coding)){
                $this -> error('请选择正确的编码格式');
            }
            //取得表备注
            $tabledesc = I('post.tabledesc','');
            //字段名称
            $field = I('post.field');
            if(empty($field)){
                $this -> error('至少填写一个表字段');
            }
            //字段类型
            $data_type = I('post.data_type');
            if(empty($data_type)){
                $this -> error('请选择字段类型');
            }
            //字段长度
            $field_length = I('post.field_length');
            if(empty($field_length)){
                $this -> error('请填写正确的字段长度');
            }
            //字段默认值
            $field_default = I('post.field_default');
            //是否是主键
            $primary_Key = I('post.primary_Key');
            //是否允许为空
            $non_empty = I('post.non_empty');
            //零填充
            $unsigned = I('post.unsigned');
            //自增
            $Increment = I('post.Increment');
            //注释
            $note = I('post.note');
            //组装数组
            $dataArr = arrAssembly(array($field,$data_type,$field_length,$field_default,$primary_Key,$non_empty,$unsigned,$Increment,$note));
            //执行添加操作
            $add_table = $this -> createTableSql($dataArr,$tablename,$tabledesc,$tabletype,$coding,$charset);

            if($add_table == 0){
                $this -> success('添加成功',U('mysqllist'));
            }
        }
        $this->display();
    }

    /*测试代码*/
    public function test()
    {
        createMysqlTable('tablename');
        //$this->display();
    }


    private function createTableSql($data=array(), $tableName='aaa',$comment='22',$engine='InnoDB',$charset='utf8')
    {
        $auth_add = '';
        $table  = array();
        $tableKey = '';
        $field_type = array('int','tinyint','smallint','mediumint','bigint','float','double','decimal');
        foreach ($data as $key => $value) {
            $str = '`'.$value[0].'`'.' '.$value[1].'('.$value[2].')';
            if(in_array($value[1], $field_type)){
                if($value[6] == 0){
                    $str .= ' unsigned';
                }
            }
            if($value[5] == 1){
                $str .= ' NOT NULL';
            }
            if($value[7] == 1){
                if($auth_add == 'AUTO_INCREMENT'){
                    return false;
                }else{
                    $str .= ' AUTO_INCREMENT';
                    $auth_add = ' AUTO_INCREMENT';
                    $tableKey = $value[0];
                }
            }
            if($value[7] != 1){
                if($value[3] != ''){
                    $str .= " DEFAULT '{$value[3]}'";
                }else{
                    $str .= " DEFAULT ''";
                }
            }
            if ($value[8]!='') {
               $str .= " COMMENT '{$value[8]}'";
            }
            $table[$key] = $str;
        }
        $tableKeyStr = "PRIMARY KEY (`{$tableKey}`)";
        array_push($table, $tableKeyStr);
        $tab=implode(',', $table);    
        $sql = M()->execute("CREATE TABLE `{$tableName}` ({$tab}) ENGINE={$engine}  AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='{$comment}'");
        return $sql;
    }
}