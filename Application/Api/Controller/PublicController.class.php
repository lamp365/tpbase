<?php
namespace Api\Controller;
use Think\Controller;
class PublicController extends Controller
{
    public $apiversion  = '';
    public $safecode    = '';
    public function _initialize()
    {
        //parent::_initialize();
        $this->checkVersion();
        $this->checkSafeCode();
    }

    /**
     * 检测版本号是否一致
     * @author 普修米洛斯 www.php63.cc
     */
    public function checkVersion()
    {
        $this->apiversion = I('post.apiversion');
        if(in_array($this->apiversion, C('API_VERSION_LIST'))){
            return;
        }else{
            $this->error(3001);
        }
    }

    /**
     * 检测安全码是否一致
     * @author 普修米洛斯 www.php63.cc
     */
    public function checkSafeCode()
    {
        $this->safecode = I('post.safecode', '');
        if($this->safecode == C('API_SAFE_CODE')){
            return;
        }else{
            $this->error(3002);
        }
    }

    /**
     * @author 普修米洛斯 www.php63.cc
     * @param array $data 返回json
     */
    public function success($res=array(),$field_desc = array())
    {
        if(empty($res)){
            $res = '';
        }
        header('content-type:application:json;charset=utf8');
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
        $resArr = array(
            'data_res'  =>array(
                'code' => 200,
                'res' => $res
            ),
            'field_desc'=>$field_desc
        );
        die(json_encode($resArr));
    }

    /**
     * @author 普修米洛斯 www.php63.cc
     * @param int $code 返回失败后的提示消息
     */
    public function error($code=300)
    {
        header('content-type:application:json;charset=utf8');
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
        $errorMsg = C('API_ERROR_MESSAGE');
        if(isset($errorMsg[$code])){
            $msg = $errorMsg[$code];
        }else{
            $msg = "操作失败";
        }
        $resArr = array(
            'data_res'  =>array(
                'code' => $code,
                'msg' => $msg
            ),
        );
        die(json_encode($resArr));
    }

    public function checkLogin($uid=0,$uuid='')
    {
        $uid = I('post.uid',0,'intval');
        $uuid = I('post.uuid','','trim');
        if(!$uid){
            $this->error(3005);
        }
        if($uuid==''){
            $this->error(3006);
        }
        if(!$this->checkUuid($uid,$uuid)){
            $this->error(3007);
        }
        return true;
    }

    public function makeUuid($id=0)
    {
        $str = $this->apiversion.$this->safecode.$id;
        return md5($str);
    }

    public function checkUuid($uid=0,$uuid='')
    {
        $str = $this->apiversion.$this->safecode.$uid;
        return (md5($str)==trim($uuid)) ;
    }
}