<?php

namespace Gini\Module {

    class Haic
    {
        public static function setup()
        {
            date_default_timezone_set(\Gini\Config::get('system.timezone') ?: 'Asia/Shanghai');

            class_exists('\Gini\Those');

            // 获得当前的用户名, 设置全局变量ME
            $username = \Gini\Auth::userName();
 
            $me = a('user', $username ? ['username' => $username] : null);
            _G('ME', $me);
 
            setlocale(LC_MONETARY, \Gini\Config::get('system.locale') ?: 'zh_CN');
            \Gini\I18N::setup();
        }

        public static function shutdown()
        {
        }
    }
}

namespace {
    /*
     * Shortcut for Hub ORM variables in Gini
     *
     **/
    if (function_exists('Hub')) {
        die('Hub() was declared by other libraries, which may cause problems!');
    } else {
        function Hub($key, $value = null)
        {
            $hub = a('hub', ['key' => $key]);
            if (is_null($value)) {
                return $hub->val ? @unserialize($hub->val) : null;
            } else {
                $hub->key = $key;
                $hub->val = @serialize($value);
                return $hub->save();
            }
        }
    }
}
