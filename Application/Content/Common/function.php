<?php


/**
 * code 验证码函数
 * @param int $length 验证码长度默认为4
 * @param int $width 验证码图片宽度默认为100
 * @param int $height 验证码图片高度 默认为30
 * @param string $code 验证码session里面的key 默认code
 * @author 刘中胜
 * @time 2014-12-5
 **/
function code($size='24',$length='4',$width='100',$height='40',$verifyName = 'code'){
    $w = $width-1;
    $h = $height-1;
    $image = imagecreate($width,$height);
    $imagecolor = imagecolorallocate($image,255,255,255);
    $bordercolor = imagecolorallocate($image,0,0,0);
    $rectangle  = imagerectangle($image,0,0,$w,$h,$bordercolor);
    for($i=0;$i<$length;$i++){
        $fontsize = $size;
        $fontcolor = imagecolorallocate($image,rand(1,120),rand(1,120),rand(1,120));
        $data = 'zxcvbnmasdfghjkqwertyup23456789';
        $str .= $fontcontent = substr($data,rand(0,strlen($data)-1),1);
        $x = ($i*$width/$length) + rand(3,10);
        $y = rand(2,12);
        imagestring($image,$fontsize,$x,$y,$fontcontent,$fontcolor);
    }

    session($verifyName,md5($str));
    for($i=0;$i<200;$i++){
        $xelcolor = imagecolorallocate($image,rand(80,220),rand(80,220),rand(80,220));
        imagesetpixel($image,rand(1,$w),rand(1,$h),$xelcolor);
    }
    for($i=0;$i<4;$i++){
        $imageline = imagecolorallocate($image,rand(80,220),rand(80,220),rand(80,220));
        imageline($image,rand(1,$w),rand(1,$h),rand(1,$w),rand(1,$h),$imageline);
    }
    ob_clean();
    header('content-type:image/png');
    imagepng($image);
    imagedestroy($image);
}

/**
 * checkcode 检测验证码方法
 * @param string $code 传入的验证码
 * @param string $verifyName session里面的key值
 * @author 刘中胜
 * @time 2014-12-5
 **/
function checkcode($code,$verifyName='code'){
    $str =strtolower($code);
    return session($verifyName) == MD5($str);
}

/**
 * 字符串大小写转换
 * @author 刘中胜
 **/
function letterChange($str,$type=1)
{
    if($type == 1){
        return ucfirst(trim($str));
    }else{
        return strtolower(trim($str));
    }
}
