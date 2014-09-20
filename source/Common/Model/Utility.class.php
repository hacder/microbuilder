<?php
namespace Common\Model;
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
        $condition = rtrim($condition, ',');
        $condition .= ')';
        $settings = $m->table('__CORE_SETTINGS__')->where($condition)->select();
        $s = array();
        foreach($keys as $key) {
            $origKey = "{$moduleName}:{$key}";
            $s[$key] = unserialize($settings[$origKey]);
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
        return $m->table('__CORE_SETTINGS__')->addAll($ds);
    }
}
