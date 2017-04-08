<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 17-4-7
 * Time: 下午11:28
 */
namespace Home\Controller;

class UcenterController extends PrivateController
{
    public function index()
    {
        ppd(getUserCache());
    }
}