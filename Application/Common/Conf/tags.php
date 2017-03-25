<?php
/**
 * Created by PhpStorm.
 * User: kevin dayblog.cn
 */
return array(

//    'view_filter' => array('Behavior\TokenBuild'),
    // 如果是3.2.1以上版本 需要改成
     'view_filter' => array('Behavior\TokenBuildBehavior'),
);