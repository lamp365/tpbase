<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 17-4-7
 * Time: 下午8:34
 */
/**
 * 操作用户日志记录
 * @param $uid
 * @param $type
 * @param $mark 备注信息 1登陆 2非法攻击 3创建规则
 */
function set_history($uid,$type,$mark ='')
{
    $ip  = get_client_ip();
    $arr = get_ip_address($ip);
    $data["uid"]      = intval($uid);
    $data["ip"]       = $arr['ip'] ?: '';
    $data["country"]  = $arr['country'] ?: '';
    $data["province"] = $arr['province'] ?: '';
    $data["city"]     = $arr['city'] ?: '';
    $data["isp"]      = $arr['isp'] ?: '';
    $data["createtime"]    = NOW_TIME;
    $data["type"]     = $type;
    $data["mark"]     = $mark;
    /* 设备方式 */
    $data["mobile_type"]   = get_mobile_type();

    $userinfo = getUserCache();
    $data['mobile']        = $userinfo['mobile'] ?: '';
    $data['name']          = $userinfo['name'] ?: '';

    $history               = M("userlog");
    $history->add($data);

}


/**
 * 根据ip获取所在位置
 * @param $queryIP
 * @return mixed|stdClass
 */
function get_ip_address($queryIP){
    $url = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip='.$queryIP;

    $client = new \Curl\Curl();
    $client->get($url);
    if ($client->error) {
    //0 空 或者1  1则是有问题的
//        echo $client->error_code.$client->error_message;
        $location = array();
    }else {
//        pp($client->request_headers);
//        pp($client->response_headers);
        $location = json_decode($client->response,true);
        if(!is_array($location)){
            $location = array();
        }
    }
    $location['ip']       = $queryIP;
    return $location;
}

/**
 * 获取访问设备的类型
 * 最好有涉及到数据存储是 1代表安卓 2代表ios 3代表平板 4代表PC
 * @param string $show_str  $show_str如果入库用 不给值，如果需要页面展示具体文字信息 则给值
 * @return string
 * 更多方法查看 http://demo.mobiledetect.net/
 */
function get_mobile_type($show_str = ''){
    if($show_str){
        $typeArr = array('安卓','IOS','平板','PC');
    }else{
        $typeArr = array(1,2,3,4);
    }
    $mobile = new \Detection\MobileDetect();
    if($mobile->isAndroidOS()){
        return $typeArr[0];
    }else if($mobile->isiOS()){
        return $typeArr[1];
    }else if($mobile->isTablet()){
        return $typeArr[2];
    }else{
        return $typeArr[3];
    }
}

/**
 * 获取用户缓存
 * @return bool|mixed
 */
function getUserCache(){
    $token = cookie('USER_INFO_TICKET');
    if(empty($token))
    {
        return array();
    }else{
        return S($token);
    }
}
