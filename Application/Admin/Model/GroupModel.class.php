<?php
namespace Admin\Model;

class GroupModel extends PrivateModel
{
    protected $_validate = array(
        array('title', 'require', '分组名称不能为空'),
        array('sort', 'number', '排序只能是数字'),
        array('id', 'number', 'id参数有误！',3),
    );


    protected $_auto = array(
        array('createtime', 'time', self::MODEL_INSERT, 'function')
    );
}