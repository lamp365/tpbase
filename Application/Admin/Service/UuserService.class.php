<?php
/**
 * Created by PhpStorm.
 * User: kevin.liu www.dayblog.cn
 */
namespace Admin\Service;

use Think\Model;

class UuserService extends Model{

    protected $autoCheckFields = False;

    public function __construct()
    {
        parent::__construct();
    }

    public function adminMenuArr(){
        echo 'sss';
    }
}