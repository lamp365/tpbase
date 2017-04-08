<?php
/**
 * Created by PhpStorm.
 * User: kevin
 */
return array(

    'LOAD_EXT_CONFIG'                   => 'route',           //路由配置文件


    /* 数据缓存设置 */
    'DATA_CACHE_PREFIX'                 => 'Kevin_', // 缓存前缀
    'DATA_CACHE_TYPE'                   => 'File',      // 数据缓存类型 OR File
    'REDIS_HOST'                        => '127.0.0.1',  //连接类型 1:普通连接 2:长连接
    'REDIS_PORT'                        => 6379,
    'DATA_CACHE_TIME'                   => 3600,        //超时时间
    'REDIS_AUTH'                        => 'a7918I',     //AUTH认证密码


    //阿里大于
    'ali_open_start'                    => 0,  //是否启用短信验证
    'ali_open_key'                      => '23499623',
    'ali_open_secret'                   => 'e2a5c71e4eca9cc7e4d6141ce5c5f0b4',
);
