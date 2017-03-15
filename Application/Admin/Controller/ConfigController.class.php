<?php
namespace Admin\Controller;
class ConfigController extends PrivateController
{
    //基础配置
    public function config()
    {
        if(IS_POST){
            $data = I('post.');
            foreach ($data as $key => &$value) {
                $value = trim($value);
            }
            if(isset($post['webOption'])){
                unset($post['webOption']);
            }
            A('Common/Base')->setOption('webOption', $data, 1, "网站配置");
            $this->success("操作成功", U('config'));
        }
        $info = A('Common/Base')->getOption('webOption');
        $this->assign('info', $info);
        $this->display();
    }
}