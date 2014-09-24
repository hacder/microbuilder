<?php
namespace Core\Model;
use Think\Model;

class Utility extends Model {
    protected $autoCheckFields = false;

    public static function encodePassword($input, $salt) {
        $str = "{$input}{$salt}" . C('COMMON.AUTHKEY');
        return sha1($str);
    }

    /**
     * 加载数据库配置项, 一般预留给模块封装调用, 控制器中不应该直接调用
     * @param $moduleName string 模块名称
     * @param $keys array 配置项名称
     * @return array
     */
    public static function loadSettings($moduleName, $keys) {
        $moduleName = strtoupper($moduleName);
        $m = new Model();
        $condition = '`key` IN (';
        foreach($keys as &$key) {
            $key = strtoupper($key);
            $condition .= "'{$moduleName}:{$key}',";
        }
        unset($key);
        $condition = rtrim($condition, ',');
        $condition .= ')';
        $settings = $m->table('__CORE_SETTINGS__')->where($condition)->select();
        $settings = coll_key($settings, 'key');
        $s = array();
        foreach($keys as $key) {
            $origKey = "{$moduleName}:{$key}";
            $s[$key] = unserialize($settings[$origKey]['value']);
        }
        return $s;
    }

    /**
     * 将配置项写入数据库, 一般预留给模块封装调用, 控制其中不应该直接调用
     * @param $moduleName string 模块名称
     * @param $settings array 配置项名称
     * @return bool
     */
    public static function saveSettings($moduleName, $settings) {
        $moduleName = strtoupper($moduleName);
        $ds = array();
        foreach($settings as $key => $value) {
            if(!empty($key)) {
                $key = strtoupper($key);
                $origKey = "{$moduleName}:{$key}";
                $ds[$origKey] = serialize($value);
            }
        }
        if(empty($ds)) {
            return false;
        }
        $m = new Model();
        foreach($ds as $key => $value) {
            $rec = array();
            $rec['key'] = $key;
            $rec['value'] = $value;
            $m->table('__CORE_SETTINGS__')->add($rec, array(), true);
        }
        return true;
    }

    public static function sslGenKey() {
        $config = array(
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
            'private_key_bits' => 1024,
            'config' => MB_ROOT . 'source/Data/openssl.cnf'
        );
        $res = openssl_pkey_new($config);
        if(empty($res)) {
            return error(-1, openssl_error_string());
        }
        $public = openssl_pkey_get_details($res);
        if(empty($public)) {
            return error(-2, openssl_error_string());
        }
        $r = openssl_pkey_export($res, $private, null, $config);
        if(empty($r)) {
            return error(-3, openssl_error_string());
        }
        openssl_free_key($res);
        $ret = array();
        $ret['public'] = $public['key'];
        $ret['private'] = $private;
        return $ret;
    }

    public static function sslTrimKey($key) {
        $pub = str_replace('-----BEGIN PUBLIC KEY-----', '', $key);
        $pub = str_replace('-----END PUBLIC KEY-----', '', $pub);
        $pub = trim($pub);
        $pub = str_replace("\r", '', $pub);
        $pub = str_replace("\n", '', $pub);
        return $pub;
    }
}
