<?php
namespace Api\Controller;

class EmptyController extends PublicController
{
    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 检测接口是否存在
     * @author 普修米洛斯 www.php63.cc
     */
    public function _empty()
    {
        $apiFile = MODULE_PATH.$this->apiversion.'/'.CONTROLLER_NAME.'.'.ACTION_NAME.'.api.php';
        if(is_file($apiFile)){
            include ($apiFile);
        }else{
            $this->error(3004);
        }
    }
}
