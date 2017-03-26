<?php
namespace Admin\Model;

class AuthCateModel extends PrivateModel
{
    protected $_validate = array(
        array('title', 'require', '标题必须填写'), //默认情况下用正则进行验证
        array('module', 'require', '模块名称必须填写'), //默认情况下用正则进行验证
        array('controller', 'require', '控制器名称必须填写'), //默认情况下用正则进行验证
        array('method', 'require', '方法名称必须填写'), //默认情况下用正则进行验证
        array('sort', 'require', '排序必须填写'), //默认情况下用正则进行验证
        array('sort', 'number', '排序只能为数字'), //默认情况下用正则进行验证
        array('id', 'number', 'id参数有误！'), //默认情况下用正则进行验证
    );

}