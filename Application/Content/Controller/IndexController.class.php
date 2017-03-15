<?php
namespace Content\Controller;
class IndexController extends PublicController {
    public function index(){
		//调用首页跳转处理方法
        self::urlRedirect();
	}
}
