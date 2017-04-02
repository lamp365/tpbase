<?php
/**
 * Created by PhpStorm.
 * User: kevin
 */
return array(

    /* 数据缓存设置 */
    'DATA_CACHE_PREFIX'                 => 'Kevin_', // 缓存前缀
    'DATA_CACHE_TYPE'                   => 'Redis',      // 数据缓存类型 OR File
    'REDIS_HOST'                        => '127.0.0.1',  //连接类型 1:普通连接 2:长连接
    'REDIS_PORT'                        => 6379,
    'DATA_CACHE_TIME'                   => 3600,        //超时时间
    'REDIS_AUTH'                        => 'a7918I',     //AUTH认证密码
);
