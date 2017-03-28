<?php
    /**
     * @author kevin.liu www.dayblog.cn
     * @param int $width 宽度
     * @param int $height 高度
     * @param int $font_size 字体大小
     * @param int $code_len 验证码长度
     * @param int $line_num 线条长度
     * @param string $font 字体名称
     * @param int $interference 雪花数量
     */
    function code($width = 100, $height = 32, $font_size = 13, $code_len = 4, $line_num = 5, $font = './Public/ttf/5.ttf', $interference = 10,$verifyName = 'code'){
        $image = imagecreatetruecolor($width, $height);
        $image_color = imagecolorallocate($image,mt_rand(157,255), mt_rand(157,255), mt_rand(157,255));
        imagefilledrectangle($image,0,$height,$width,0,$image_color);
        $x = $width/$code_len;
        $codeStrs = '';
        for($i = 0; $i<$code_len;$i++){
            $str = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
            $font_color = imagecolorallocate($image,mt_rand(0,156),mt_rand(0,156),mt_rand(0,156));
            $codeStrs .= $codeStr = utf8_encode($str[mt_rand(0,strlen($str)-1)]);

            imagettftext($image,$font_size,mt_rand(-30,30),$x*$i+mt_rand(1,3),$height / 1.4,$font_color,$font,$codeStr);
        }
        session($verifyName,md5(strtolower($codeStrs)));
        //生成线条
        for($i = 0;$i<$line_num;$i++) {
            $line_color = imagecolorallocate($image, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
            imageline($image, mt_rand(0,$width),mt_rand(0,$height),mt_rand(0,$width),mt_rand(0,$height), $line_color);
        }
        for($i=0;$i<$interference;$i++){
            $color = imagecolorallocate($image, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
            imagestring($image,mt_rand(1,5),mt_rand(0,$width),mt_rand(0,$height),'*',$color);
        }
        header("Content-type: image/png");
        imagepng($image);
        imagedestroy($image);
    }

/**
 * checkcode 检测验证码方法
 * @param string $code 传入的验证码
 * @param string $verifyName session里面的key值
 * @author kevin.liu
 **/
function checkcode($code,$verifyName='code'){
    $str = strtolower($code);
    return session($verifyName) == MD5($str);
}



/**
 * 跳转到登陆
 */
function skip_login(){
    session(C('ADMIN_UID'), null);
    session('login_user', null);
    $url = getU("Admin/Public/login");
    header("location:{$url}");
}

/**
 * 坚持规则是否可以被选中
 * @param $id
 * @param $rule_idArr
 * @return string
 */
function ruleIsCheck($id,$rule_idArr){
    if(in_array($id,$rule_idArr)){
        return 'checked';
    }else{
        return '';
    }
}

/**
 * 获取管理员所属组
 * @param $uid
 * @return string
 */
function getAdminGroup($uid){
    if(empty($uid)){
        return '';
    }
    $group = D("GroupAccess")->where("uid={$uid}")->getField('group_id',true);
    if(!empty($group)){
        $group_id = implode(',',$group);
        $where['id'] = array("in",$group_id);
        $info_title  = D("Group")->where($where)->getField('title',true);
        $html = '';
        foreach($info_title as $title){
            $html .= "<span class='button button-little bg-yellow'>{$title}</span> ";
        }
        return $html;
    }else{
        return '';
    }
}

/**
 * 获取数据库大小
 * @return string
 */
function _mysql_db_size()
{
    $sql = "SHOW TABLE STATUS FROM ".C('DB_NAME');
    $tblPrefix = C('DB_PREFIX');
    if($tblPrefix != null) {
        $sql .= " LIKE '{$tblPrefix}%'";
    }
    $row = M()->query($sql);
    $size = 0;
    foreach($row as $value) {
        $size += $value["data_length"] + $value["index_length"];
    }
    return tosize($size);
}

/**
 * 大小写转换
 * @param string $str 要转换的字符串
 * @param int    $type转换模式 1是首字母转为大写 2是换为小写
 **/
function letterChange($str,$type=1)
{
    if($type == 1){
        return ucfirst(trim($str));
    }else{
        return strtolower(trim($str));
    }
}

/**
 * 创建model
 * @param string $module model所属模块
 * @param string $model model名字
 * @author kevin.liu
 **/
function createModel($module,$model)
{
    $model = ucfirst($model);
    $model_arr = explode('_',$model);
    if(count($model_arr) != 1){
        $model = '';
        foreach($model_arr as $word){
            $model .= ucfirst($word);
        }
    }

    $filename   = APP_PATH.$module.'/Model/'.$model."Model.class.php";
    if(file_exists($filename)){
        return array('code'=>1002,'msg'=>'文件已经存在！');
    }
    $str    = "<?php ".PHP_EOL;
    $str   .= 'namespace '.$module."\\Model;".PHP_EOL;
    $str   .= 'class '.$model."Model extends PrivateModel { ".PHP_EOL.PHP_EOL;
    $str   .= '}';

    if (!$head = fopen($filename, "w+")) {//以读写的方式打开文件，将文件指针指向文件头并将文件大小截为零，如果文件不存在就自动创建
        return array('code'=>1002,'msg'=>"尝试打开文件[{$filename}]失败!");
    }
    if (fwrite($head,$str)==true) {//执行写入文件
        fclose($head);
    }else{
        return array('code'=>1002,'msg'=>'写入失败！');
    }
    fclose($head);
    return array('code'=>200,'msg'=>'操作成功！');
}


