<?php
/**
 * randNum 生成随机数
 * @author kevin.liu
 **/
function randNum(){
    return mt_rand(1000,99999999);
}

/**
 * md5Encrypt 加密函数
 * @param string $str 要加密的字符串
 * @return string $chars 加密后的字符串
 * @author kevin.liu
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
 * 表单验证，防止重复提交
 * @return bool
 * @author kevin.liu
 */
function formCheckToken(){
    if(C('TOKEN_ON')){
        $name   = C('TOKEN_NAME', null, '__hash__');
        if(isset($_REQUEST[$name])){
            if(!isset($_SESSION[$name])){   // 令牌数据无效
                return false;
            }

            // 令牌验证
            list($key,$value)  =  explode('_',$_REQUEST[$name]);
            if(isset($_SESSION[$name][$key]) && $value && $_SESSION[$name][$key] == $value) { // 防止重复提交
                unset($_SESSION[$name][$key]); // 验证完成销毁session
                return true;
            }

            // 开启TOKEN重置
            if(C('TOKEN_RESET'))
                unset($_SESSION[$name][$key]);

            return false;

        }

    }
    return true;
}

/**
 * 无限分类tree
 * @param $list 分类数据
 * @param int $pid
 * @param int $level
 * @param string $html
 * @return array
 * @author kevin.liu
 */
function catTree(&$list,$pid=0,$level=1,$html='--'){
    static $tree = array();
    foreach($list as $v){
        if($v['pid'] == $pid){
            $v['sort'] = $level;
            $v['html'] = str_repeat($html,$level);
            $tree[] = $v;
            catTree($list,$v['id'],$level+1,$html);
        }
    }
    return $tree;
}

/**
 * @param $list 空数组
 * @param $data  分类数据
 * @param int $pid
 * @param int $level
 * @author kevin.liu
 */
function catTree2(&$list,$data, $pid = 0, $level = 1)
{
    if (!is_null($pid)) {
        foreach ($data as $tmp) {
            if ($tmp['pid'] == $pid) {
                $list[$tmp['id']]['main']  = $tmp;
                $list[$tmp['id']]['level'] = $level;
                catTree2($list[$tmp['id']]['child'], $data,$tmp['id'], $level + 1);
            }
        }
    }
}

/**
 * 防止快速刷新 恶意行为 我们认为当3秒内，刷页面超过8次，认为是恶意行为
 * @param string $seconds  时间段[秒]
 * @param int $refresh     刷新次数
 */
function checkXssQcc($seconds = '3',$refresh=10){
    $cur_time = time();
    if($data = session('xssQcc')){
        $ip  = get_client_ip();
        $key = 'forbiden_vist_'.$ip;
        if(S($key) == $ip){   //静止三分钟访问
            //跳转至攻击者服务器地址
            header(sprintf('Location:%s', 'http://127.0.0.1'));
            exit('Sorry,Access Denied');
        }

        $data = unserialize($data);
        $data['refresh_times'] = $data['refresh_times'] + 1;

        //处理监控结果
        if($cur_time - $data['last_time'] < $seconds){
            //3秒内 超过10次
            if($data['refresh_times'] > $refresh){
                //记录攻击者日志
                set_history(0,2,'非法攻击！');
                //并缓存三分钟不能访问
                S($key,$ip,180);
                //跳转至攻击者服务器地址
                header(sprintf('Location:%s', 'http://127.0.0.1'));
                exit('Sorry,Access Denied');
            }
        }else{
            //超过3秒 重至
            $data = array(
                'refresh_times' => 1,
                'last_time'     => $cur_time,
            );
        }

        $new_data = serialize($data);
        session('xssQcc',$new_data);
    }else{
        $data = array(
            'refresh_times' => 1,
            'last_time'     => $cur_time,
        );

        $new_data = serialize($data);
        session('xssQcc',$new_data);
    }
}
/**
 * 获取大小单位换算
 * @param $size
 * @return string
 * @author kevin.liu
 */
function tosize($size) {
    $kb = 1024; // 1KB（Kibibyte，千字节）=1024B，
    $mb = 1024 * $kb; //1MB（Mebibyte，兆字节，简称“兆”）=1024KB，
    $gb = 1024 * $mb; // 1GB（Gigabyte，吉字节，又称“千兆”）=1024MB，
    $tb = 1024 * $gb; // 1TB（Terabyte，万亿字节，太字节）=1024GB，

    if ($size < $kb) {
        return $size . " B";
    } else if ($size < $mb) {
        return round($size / $kb, 2) . " KB";
    } else if ($size < $gb) {
        return round($size / $mb, 2) . " MB";
    } else if ($size < $tb) {
        return round($size / $gb, 2) . " GB";
    } else {
        return round($size / $tb, 2) . " TB";
    }

}

/**
 * 保存上次访问页面
 */
function tosavepagefrom()
{
    $key = MODULE_NAME.'_page_fromurl';
    $url = rtrim(C('WEB_DOMAIN'),'/').'/'.$_SERVER['REQUEST_URI'];
    session($key,$url);
}
function cleanpagefrom()
{
    $key = MODULE_NAME.'_page_fromurl';
    session($key,null);
}

/**
 * 二维数组排序 按照某一个key
 * @param $arr
 * @param $field
 * @param string $sort
 * @return array
 */
function mulity_array_sort($arr,$field,$sort='desc')
{
    $keysvalue = $new_array = array();
    foreach ($arr as $k=>$v){
        $keysvalue[$k] = $v[$field];
    }
    if($sort == 'asc'){
        asort($keysvalue);
    }else{
        arsort($keysvalue);
    }
    reset($keysvalue);
    foreach ($keysvalue as $k=>$v){
        $new_array[] = $arr[$k];
    }
    return $new_array;
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
 * @$route 是否是一个路由地址，是的话，则直接放回路由地址
 * @return string 返回一个绝对地址
 * getU('edit')   当前控制器下的edit
 * getU('User/edit')   当前model下 User控制器下的edit
 * getU('Home/User/edit')   当前Home模块下 User控制器下的edit
 */
function getU($url,$route=false){
    if($route){
        return C('WEB_DOMAIN').'/'.$url.'.'.C('URL_HTML_SUFFIX');
    }else{
        $url_arr = explode('/',$url);
        if(count($url_arr) == 2){
            $url = MODULE_NAME."/".$url;
        }
        return C('WEB_DOMAIN').U($url);
    }
}
