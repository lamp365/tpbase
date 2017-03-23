<?php
/**
 * randNum 生成随机数
 * @author kevin.liu
 * @time 2015-08-14
 **/
function randNum(){
    return mt_rand(1000,99999999);
}

/**
 * md5Encrypt 加密函数
 * @param string $str 要加密的字符串
 * @return string $chars 加密后的字符串
 * @author kevin.liu
 * @time 2015-04-13
 **/
function md5Encrypt($str='',$rand=''){
    $hash = $str.$rand;
    $chars =  MD5(hash('sha256', $hash));
    return $chars;
}
/**
 * 删除缓存文件
 * @param string $dir 默认temp目录
 * @author kevin.liu
 **/
function delTemp($dir = TEMP_PATH){
    $dh = opendir($dir);
    while ($file = readdir($dh)) {
       if ($file != "." && $file != "..") {
           $fullpath = $dir . "/" . $file;
           if (!is_dir($fullpath)) {
                unlink($fullpath);
           } else {
                delTemp($fullpath);
           }
       }
    }
    closedir($dh);
    if (rmdir($dir))
    {
        return true;
    }
    return false;
}

/**
 * 将key相同的数组合并为新的数组
 * @param array $arr 接收要组装的二维数组
 * @author kevin.liu
 **/
function arrAssembly($arr)
{
    $arr_new = array();
    foreach($arr as $item){
        foreach($item as $key=>$val){
            $arr_new[$key][] = $val;
        }
    }
    return $arr_new;
}

/**
 * 格式化时间
 * @param mixed $date 传入要格式化的时间
 * @param int $type 1：年月日时分秒如：2015-12-07 0:22:12
 * @return mixed 处理好的时间
 * @author kevin.liu
 * @time 2015-12-07
 **/
function formatTime($date, $type=1)
{
    switch($type){
        case 1:
            $date = date('Y-m-d H:i:s',$date);
            break;
        case 2:
            $date = date('Y-m-d H:i',$date);
            break;
        case 3:
            $date = date('Y-m-d',$date);
            break;
    }
  return $date;
}


/**
 * 切割图片
 * @author kevin.liu
 * @trim 2015-05-22
 **/
function getThumb($url='', $width=null, $height=null){
    if(empty($url)){
        return '';
    }
    if(is_null($width)){
        $width = 100;
    }
    if(is_null($height)){
        $height = $width;
    }
    $tmpArr = explode('/', $url);
    $name = array_pop($tmpArr);
    $allname = implode('/', $tmpArr) ."/thumb/{$width}x{$height}/" . $name;
    return $allname;
}
/**
 * cut_str 字符串截取
 * @param string $sourcestr 要截取的内容
 * @param string $cutlength 指定长度
 * @author kevin.liu
 * @time 2015-5-19
 **/
function cut_str($sourcestr,$cutlength){
    $returnstr='';
    $i=0;
    $n=0;
    $str_length=strlen($sourcestr);//字符串的字节数
    while (($n<$cutlength) and ($i<=$str_length)){
        $temp_str=substr($sourcestr,$i,1);
        $ascnum=Ord($temp_str);//得到字符串中第$i位字符的ascii码
        if ($ascnum>=224){ //如果ASCII位高与224，
            $returnstr=$returnstr.substr($sourcestr,$i,3); //根据UTF-8编码规范，将3个连续的字符计为单个字符
            $i=$i+3; //实际Byte计为3
            $n++; //字串长度计1
        }elseif ($ascnum>=192){ //如果ASCII位高与192，

            $returnstr=$returnstr.substr($sourcestr,$i,2); //根据UTF-8编码规范，将2个连续的字符计为单个字符
            $i=$i+2; //实际Byte计为2
            $n++; //字串长度计1
        }elseif ($ascnum>=65 && $ascnum<=90){ //如果是大写字母，
            $returnstr=$returnstr.substr($sourcestr,$i,1);
            $i=$i+1; //实际的Byte数仍计1个
            $n++; //但考虑整体美观，大写字母计成一个高位字符
        }else{ //其他情况下，包括小写字母和半角标点符号，
            $returnstr=$returnstr.substr($sourcestr,$i,1);
            $i=$i+1; //实际的Byte数计1个
            $n=$n+0.5; //小写字母和半角标点等与半个高位字符宽...
        }
    }
    if ($str_length>$cutlength){
        $returnstr = $returnstr."...";//超过长度时在尾处加上省略号
    }
    return $returnstr;
}
/**
 * 生成随机字符串
 * @author kevin.liu
 **/
function getRandStr($length=8) {
    $str = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $randString = ''; 
    $len = strlen($str)-1; 
    for($i = 0;$i < $length;$i ++){ 
        $num = mt_rand(0, $len); 
        $randString .= $str[$num]; 
    } 
    return $randString ; 
}

/**
 * 逗号中文转英文
 **/
function bianma($str)
{
   return str_replace('，',',',$str);
}
/**
 * 邮件发送函数
 */
function sendMail($to, $title, $content) {
    require APP_PATH . 'Common/Lib/PHPMailer/class.smtp.php';
    require APP_PATH . 'Common/Lib/PHPMailer/class.phpmailer.php';

    $mail = new \PHPMailer(); //实例化
    $mail->IsSMTP(); // 启用SMTP
    $mail->Host=C('MAIL_HOST'); //smtp服务器的名称（这里以QQ邮箱为例）
    $mail->SMTPAuth = C('MAIL_SMTPAUTH'); //启用smtp认证
    $mail->Username = C('MAIL_USERNAME'); //你的邮箱名
    $mail->Password = C('MAIL_PASSWORD') ; //邮箱密码
    $mail->From = C('MAIL_FROM'); //发件人地址（也就是你的邮箱地址）
    $mail->FromName = C('MAIL_FROMNAME'); //发件人姓名
    $mail->AddAddress($to,"尊敬的客户");
    $mail->WordWrap = 50; //设置每行字符长度
    $mail->IsHTML(C('MAIL_ISHTML')); // 是否HTML格式邮件
    $mail->CharSet=C('MAIL_CHARSET'); //设置邮件编码
    $mail->Subject =$title; //邮件主题
    $mail->Body = $content; //邮件内容
    $mail->AltBody = "这是一个纯文本的身体在非营利的HTML电子邮件客户端"; //邮件正文不支持HTML的备用显示
    return($mail->Send());
}

/**
 * @param $str 要加密的字符串
 * @return string 加密后的字符串
 */
function homeUserPwd($str)
{
    $hash = Md5($str);
    $str = MD5(hash('sha256', $hash));
    return $str;
}

/**
 * @author kevin.liu www.dayblog.cn
 * @param $file 缓存文件名
 * @param int $time 缓存时间
 */
function check_cache($file, $time = 0){
    if($time == 0){
        $time = C('CACHE_TIME');
    }
    if (is_file($file) && (time() - filemtime($file)) < $time) {
        require_once $file;
        exit;
    }
}

/**
 * @author kevin.liu www.dayblog.cn
 * @param $file 生成静态页面
 */
function create_cache($file){
    file_put_contents($file,ob_get_contents());
}


/**
 * 打印数组 程序还会往下执行
 * 可打印任何数据 可支持连写
 * pp($data,$obj,$aa);
 */
function pp(){
    echo "<meta charset='utf8' />";
    $arr = func_get_args();
    echo "<pre>";
    foreach($arr as $one){
        print_r($one);
        echo '<br/>';
    }
    echo "</pre>";
}
/**
 * 打印数组 程序会终止，不往下执行
 * 可打印任何数据 可支持连写
 * pp($data,$obj,$aa);
 */
function ppd(){
    echo "<meta charset='utf8' />";
    $arr = func_get_args();
    echo "<pre>";
    foreach($arr as $one){
        print_r($one);
        echo '<br/>';
    }
    echo "</pre>";
    die();
}

/**
 * @param $url
 * @return string 返回一个绝对地址
 * getU('edit')   当前控制器下的edit
 * getU('User/edit')   当前model下 User控制器下的edit
 * getU('Home/User/edit')   当前Home模块下 User控制器下的edit
 */
function getU($url){
    $url_arr = explode('/',$url);
    if(count($url_arr) == 2){
        $url = MODULE_NAME."/".$url;
    }
    return C('WEB_DOMAIN').U($url);
}