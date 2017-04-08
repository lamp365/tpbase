<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 17-3-15
 * Time: 下午11:20
 */
namespace Home\Controller;
use Think\Controller;

class LoginController extends Controller
{

    /**
     * 初始化方法
     * @auth kevin.liu www.dayblog.cn
     **/
    public function _initialize()
    {
        //防止QCC跨站攻击
        checkXssQcc();
        // 表单验证，防止重复提交
        if(!formCheckToken()){
            $this->error("禁止重复提交表单！");
        }
    }


    /**
     * success 执行成功返回json格式
     * @param $message 提示字符串
     * @param $url 跳转地址
     * @param $time 等待时间
     * @author kevin.liu(791845283@qq.com)
     * 操作  success('操作成功')  直接跳转 返回上一页
     * 操作  success('操作成功',3000)  等待3秒 返回上一页
     * 操作  success('操作成功','url')  直接跳转到url
     * 操作  success('操作成功','url',3000)  等待3秒 直接跳转到url
     * @time 2015-15-05
     **/
    public function success($message = '操作成功！', $url = 2000,$time = 2000)
    {
        if(is_numeric($url)){
            $time = $url;
            $url  = '';
        }
        $this->assign('code',200);
        $this->assign('message',$message);
        $this->assign('url',$url);
        $this->assign('time',$time);
        $this->display('Public/status');
        die();
    }

    /**
     * error 执行成功返回json格式
     * @param string $message 提示字符串
     * @param string $url 跳转地址
     * @param $time 等待时间
     * @author kevin.liu(791845283@qq.com)
     * 操作  error('操作失败')  直接跳转 返回上一页
     * 操作  error('操作失败',2000)  等待2秒 返回上一页
     * 操作  error('操作失败','url')  直接跳转到url
     * 操作  error('操作失败','url',2000)  等待2秒 直接跳转到url
     **/
    public function error($message = '操作失败！',$url = 2000,$time = 2000)
    {
        if(is_numeric($url)){
            $time = $url;
            $url  = '';
        }
        $this->assign('code',1002);
        $this->assign('message',$message);
        $this->assign('url',$url);
        $this->assign('time',$time);
        $this->display('Public/status');
        die();
    }



    public function index()
    {
        if(IS_POST){

            $userModel  = D('User');
            $res = $userModel->login();
            if($res){
                $this->success("登陆成功！",getU('center',true));
            }else{
                $this->error($userModel->getError());
            }

        }

        $this->display();
    }

    public function dologin()
    {

    }

    public function loginout()
    {

    }

    public function register()
    {
        if(IS_POST){
            $user = D('User');
            if($user->register()){
                $this->success("注册成功！",getU('center',true));
            }else{
                $this->error($user->getError());
            }
        }

        $this->display();
    }

    /**
     * 发送验证码
     */
    public function sendcode()
    {
        $mobile = I('post.mobile');
        if(empty($mobile) || !is_numeric($mobile) || strlen($mobile) != 11){
            $data = array('code'=>1002,'message'=>"手机号码有误！");
            $this->ajaxReturn($data,'json');
        }

        //该手机号是否已经存在
        $user = M('user')->where("mobile={$mobile}")->find();
        if($user){
            $data = array('code'=>1002,'message'=>"该手机号码已经注册过！");
            $this->ajaxReturn($data,'json');
        }

        $sms = D('Sms',"Service");
        if($sms->register($mobile)){
            $ali_open = C('ali_open_start');
            if(!$ali_open){
                //如果短信开关已经关闭状态 不用验证码直接可以注册
                $data = array('code'=>202,'message'=>"666666");
                $this->ajaxReturn($data,'json');
            }else{
                $data = array('code'=>200,'message'=>"发送成功！");
                $this->ajaxReturn($data,'json');
            }
        }else{
            $data = array('code'=>1002,'message'=>$sms->getError());
            $this->ajaxReturn($data,'json');
        }
    }

    public function findpassword()
    {
        $this->display();
    }
}