<?php
/**
 * 拦截器配置，分为global,针对某个controller,default
 * @package /
 * @author  XiaodongPan
 * @version $Id: interceptor.php 2016-12-12 $
 */
return [
    'global' => [
        'AuthInterceptor',
    ],
    'default' => [
    ],
    'App\\Controller\\Mobile\\V1\\HelloController' => [
        'LoggerInterceptor',
    ]
];