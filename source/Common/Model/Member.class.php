<?php
namespace Common\Model;
use Think\Model;

class Member extends Model {
    protected $autoCheckFields = false;

    const STATUS_DISABLED = '-1';
    const STATUS_ENABLED = '0';
     /**
     * 选项: 会员策略
     */
    const OPT_POLICY = 'POLICY';

    public static function loadSettings($flush = false) {
        $s = C('MS');
        if(empty($s) || $flush) {
            $keys = array();
            $keys[] = self::OPT_POLICY;
            $s = Utility::loadSettings('Member', $keys);
            C('MS', $s);
        }
    }

    public static function saveSettings($settings) {
        $keys = array();
        $keys[] = self::OPT_POLICY;
        $settings = coll_elements($keys, $settings);
        return Utility::saveSettings('Member', $settings);
    }

    public function getRoles($withDisabled = false) {
        $ret = array();
        $ret[] = array(
            'id'        => '-1',
            'title'     => '[系统]超级管理员',
            'parent'    => '0',
            'status'    => '0',
            'issystem'  => true,
            'remark'    => '系统默认用户组, 拥有系统所有权限. 只有超级管理员才能访问管理中心, 其他用户只能访问工作台.'
        );
        $ret[] = array(
            'id'        => '0',
            'title'     => '[系统]基本用户',
            'parent'    => '0',
            'status'    => '0',
            'issystem'  => true,
            'remark'    => '系统默认用户组, 拥有系统基本权限. 没有设置访问权限的所有页面'
        );
        $condition = '';
        $pars = array();
        if(!$withDisabled) {
            $condition = '`status`=:status';
            $pars[':status'] = self::STATUS_DISABLED;
        }
        $roles = $this->table('__USR_ROLES__')->where($condition)->bind($pars)->select();
        if(!empty($roles)) {
            $ret = array_merge($ret, $roles);
        }
        return $ret;
    }

    public function deleteRole($id) {
        $id = intval($id);
        $ret = $this->table('__USR_ROLES__')->where("`id`={$id}")->delete();
        return !!$ret;
    }

    public function getUser($username, $withDisabled = false) {
        $pars = array();

        if(is_int($username)) {
            $condition = '`uid`=:uid';
            $pars[':uid'] = $username;
        } else {
            $condition = '`username`=:username';
            $pars[':username'] = $username;
        }

        if(!$withDisabled) {
            $condition = '`status`=:status';
            $pars[':status'] = self::STATUS_DISABLED;
        }
        $user = $this->table('__USR_USERS__')->where($condition)->bind($pars)->find();
        return $user;
    }
}
