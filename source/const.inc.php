<?php
/**
 * 启动位置
 */

define('MB_ROOT', str_replace("\\", '/', dirname(dirname(__FILE__))) . '/');

define('APP_DEBUG', true);
define('APP_MODE', 'common');
define('THINK_PATH', MB_ROOT . 'source/ThinkPHP/');
define('COMMON_PATH', MB_ROOT . 'source/Common/');
define('STORAGE_TYPE', 'File');

define('LOG_PATH', MB_ROOT . 'source/Data/Logs/');

if(defined('IN_APP') && IN_APP === true) {
    define('APP_ROOT', MB_ROOT . 'm/');
    define('APP_PATH', APP_ROOT . 'code/');
    define('RUNTIME_PATH', MB_ROOT . 'source/Data/Runtime/App/');
} else {
    define('APP_ROOT', MB_ROOT . 'w/');
    define('APP_PATH', APP_ROOT . 'code/');
    define('RUNTIME_PATH', MB_ROOT . 'source/Data/Runtime/Web/');
}

define('MSG_TYPE_SUCCESS', 1);
define('MSG_TYPE_INFO', 2);
define('MSG_TYPE_WARNING', 3);
define('MSG_TYPE_DANGER', 4);
define('TIMESTAMP', time());
define('__HOST__', 'http://www.microb.cn');