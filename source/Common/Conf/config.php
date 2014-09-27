<?php
$config = require MB_ROOT . 'source/config.inc.php';
$db = $config['db'];

$cfg = array(
    'VIEW_PATH'             =>  APP_ROOT . 'skins/',
    'CHECK_APP_DIR'         =>  false,
    'SHOW_PAGE_TRACE'       =>  true,
    'ACTION_SUFFIX'         =>  'Action',
    'SESSION_AUTO_START'    =>  false,

    'DEFAULT_MODULE'        =>  'Home',
    'DEFAULT_CONTROLLER'    =>  'Home',
    'DEFAULT_ACTION'        =>  'index',
    'DEFAULT_THEME'         =>  'default',
    'URL_HTML_SUFFIX'       =>  '',

    'TAGLIB_BUILD_IN'       =>  'cx,Core\Util\Tags',

    'AUTOLOAD_NAMESPACE'    =>  array(
        'Common'            =>  COMMON_PATH,
        'Core'              =>  MB_ROOT . 'source/Core/',
        'Addon'             =>  MB_ROOT . 'addons/'
    ),

    'DB_TYPE'   =>  'PDO',
    'DB_USER'   =>  $db['default']['username'],
    'DB_PWD'    =>  $db['default']['password'],
    'DB_DSN'    =>  "mysql:host={$db['default']['host']};port={$db['default']['port']};dbname={$db['default']['database']}",
    'DB_CHARSET'=>  $db['default']['charset'],
    'DB_PREFIX' =>  $db['default']['tablepre'],
);
$cfg['COMMON'] = array_change_key_case($config['common'], CASE_UPPER);

if(defined('IN_APP') && IN_APP === true) {
    $c = require MB_ROOT . 'source/Common/Conf/config-app.php';
    $cfg = array_merge($cfg, $c);
} else {
    $c = require MB_ROOT . 'source/Common/Conf/config-web.php';
    $cfg = array_merge($cfg, $c);
}

return $cfg;