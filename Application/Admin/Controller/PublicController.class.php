<?php
// +----------------------------------------------------------------------
// | 基于Thinkphp3.2.3开发的一款权限管理系统
// +----------------------------------------------------------------------
// | Copyright (c) www.dayblog.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: kevin.liu <791845283@qq.com>
// +----------------------------------------------------------------------
namespace Admin\Controller;

use Think\Controller;

class PublicController extends Controller
{
	/**
	 * ship 没有条件时跳转地址
	 * @author kevin.liu(791845283@qq.com)
	 **/
	public function skip(){
		session(C('ADMIN_UID'), null);
		$this->redirect('Public/login');
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


    /**
     * login 登录页面
     * @author kevin.liu
     **/
    public function login()
    {
        if(session(C('ADMIN_UID'))) $this->redirect('Admin/index');
        $this->display();
    }

    /**
     * islogin 检测登录
     * @author kevin.liu
     **/
    public function islogin()
    {
        $model = D('Admin');
        $data = $model->login();
        if ($data) {
            $this->success('登录成功', getU('Admin/index'));
        }
        $this->error($model->getError());
    }

    /**
     * code 检测验证码
     * @author kevin.liu
     **/
    public function code()
    {
        code();
    }
    /**
     * logout 退出登录
     * @author kevin.liu
     **/
    public function logout()
    {
        session(C('ADMIN_UID'), null);
        $this->redirect('Public/login');
    }


    /**
     * flashupload 上传方法
     * @author kevin.liu
     **/
    public function flashupload()
    {
        $upload = new \Think\Upload();
        $upload->maxSize = 31457280000;
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');
        $rootPath = $upload->rootPath = './Upload/';
        $upload->autoSub = false;
        $upload->savePath = date('Y/md/');
        $info = $upload->upload();
        if (!$info) {
            header("HTTP/1.1 500 Internal Server Error");
            echo $upload->getError();
            exit(0);
        }
        //接受上传类型
        $upload_type = I('get.type');
        //如果上传类型不是文件则执行如下代码
        if ($upload_type != 'file') {
            $imgSrc = $rootPath . $info['Filedata']['savepath'] . $info['Filedata']['savename'];
            $widthArr = explode(',', I('get.width', '', 'trim'));
            $heightArr = explode(',', I('get.height', '', 'trim'));
            $resArr = array();

            if (!empty($widthArr)) {
                $image = new \Think\Image();
                //图片裁剪
                foreach ($widthArr as $key => $w) {
                    $w = trim($w);
                    $h = trim($heightArr[$key]);
                    $image->open($imgSrc);
                    $thumbName = $rootPath . $info['Filedata']['savepath'] . "thumb/{$w}x{$h}/" . $info['Filedata']['savename'];
                    if (!is_dir(dirname($thumbName))) {
                        mkdir(dirname($thumbName), 0755, true);
                    }
                    $image->thumb($w, $h, 3)
                        ->save($thumbName, $info['ext'], 100);
                    $watermark = I('get.watermark', 0, 'intval');
                    //检测是否打水印如果等于1则代表需要打水印，
                    if ($watermark == 1) {
                        $waterMarkImg = C('WATER_MARK_IMG');
                        $warerMarkPos = C('WATER_MARK_POS');
                        if (!is_array($warerMarkPos)) {
                            $warerMarkPos = array(9);
                        }
                        foreach ($warerMarkPos as $value) {
                            $image->open($thumbName)->water($waterMarkImg, $value)->save($thumbName);
                        }
                    }
                    if (!isset($resArr['thumb'])) {
                        $resArr['thumb'] = ltrim($thumbName, '.');
                    }
                }
            }
            if (!isset($resArr['thumb'])) {
                $resArr = ltrim($imgSrc, '.');
            }
        }
        $resArr['img'] = ltrim($imgSrc, '.');
        die(json_encode($resArr));
    }

    /**
     * editUpload 编辑器上传图片
     * @author kevin.liu
     **/
    public function editUpload()
    {
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $rootPath = $upload->rootPath = './Upload/'; // 设置附件上传根目录
        $upload->savePath = ''; // 设置附件上传（子）目录
        $upload->autoSub = true;
        $upload->subName = array('date', 'Y/m/d');
        // 上传文件
        $info = $upload->upload();
        $arr = array();
        if (!$info) {
            $arr['state'] = 'ERROR';
        } else {
            $infos = $info['upfile'];
            $savePath = ltrim($infos['savepath'], '.');
            $filePathName = '/Upload/' . $savePath . $infos['savename'];
            $arr['originalName'] = $infos['name'];
            $arr['name'] = $infos['savename'];
            $arr['url'] = $filePathName;
            $arr['size'] = $infos['size'];
            $arr['type'] = $infos['ext'];
            $arr['state'] = 'SUCCESS';
        }
        die(json_encode($arr));
    }

}
