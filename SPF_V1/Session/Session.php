<?php
/**
 * Session类
 * @package /SPF/Session
 * @author XiaodongPan
 * @version $Id Session.php 2016-10-25$
 */
class SPF_Session
{
    //可配置的参数
    private static $validOptions = array(
        'save_path', 'name', 'save_handler', 'gc_probability', 'gc_divisor', 'gc_maxlifetime', 'serialize_handler',
        'cookie_lifetime', 'cookie_path', 'cookie_domain', 'cookie_secure', 'cookie_httponly', 'use_cookies',
        'use_only_cookies', 'referer_check', 'entropy_file', 'entropy_length', 'cache_limiter', 'cache_expire', 'use_trans_sid'
    );

    //是否已启动
    protected static $started = false;

    /**
     * 开启
     * @param array $options
     */
    public static function start(array $options = array(), $handler = '', $instance = null)
    {
        if (self::$started) {
            return ;
        }
        if (!empty($options)) {
            self::setOptions($options);
        }
        if ($handler) {
            self::setSaveHandler($handler, $instance);
        }
        self::$started = session_start();
    }

    /**
     * 配置设置
     * @param array $options
     */
    public static function setOptions(array $options)
    {
        foreach ($options as $key=>$value) {
            if (!in_array($key, self::$validOptions)) {
                throw new SPF_Session_Exception('未知的Session配置参数：'. $key);
            }
            ini_set("session.$key", $value);
        }
    }

    /**
     * 设置session save handler
     * @param Session_SaveHandlerInterface|string $handler
     */
    public static function setSaveHandler($handler, $instance = null)
    {
        if (!$handler instanceof SPF_Session_HandlerInterface) {
            if (is_string($handler)) {
                $className = 'SPF_Session_Handler'. ucfirst(strtolower($handler));
                $handler = new $className($instance);
            } else {
                throw new SPF_Session_Exception('Save Handler Is Invalid!');
            }
        }
        session_set_save_handler(
            array(&$handler, 'open'),
            array(&$handler, 'close'),
            array(&$handler, 'read'),
            array(&$handler, 'write'),
            array(&$handler, 'destroy'),
            array(&$handler, 'gc')
        );
    }

    /**
     * 获取session id
     * @return string
     */
    public static function getId()
    {
        return session_id();
    }

    /**
     * 关闭Session
     */
    public static function close()
    {
        session_write_close();
    }

    /**
     * Session销毁
     */
    public static function destroy()
    {
        session_unset();
        session_destroy();
        //cookie设为过期
        if (isset($_COOKIE[session_name()])) {
            $cp = session_get_cookie_params();
            setcookie(session_name(), md5(microtime() . mt_rand(0, 999999)), 1, $cp['path'], $cp['domain'], $cp['secure']);
        }
    }
}
