<?php
namespace Admin\Model;
class GroupModel extends \Common\Model\PublicModel
{
    protected $_validate = array(
        array('title', 'require', '分组名称不能为空'),
        array('sort', 'require', '排序方式必须填写'),
        array('sort', 'number', '排序只能是数字'),
    );


}