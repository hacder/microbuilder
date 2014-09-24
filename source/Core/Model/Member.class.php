<?php
namespace Core\Model;
use Think\Model;

class Member extends Model {
    protected $autoCheckFields = false;

    const STATUS_DISABLED = '-1';
    const STATUS_ENABLED = '0';
     /**
     * 选项: 会员策略
     */
    const OPT_POLICY = 'POLICY';
    const OPT_POLICY_UNION = 'union';
    const OPT_POLICY_CLASSICAL = 'classical';

    /**
     * 选项: 会员积分选项
     */
    const OPT_CREDITS = 'CREDITS';

    /**
     * 选项: 会员积分策略
     */
    const OPT_CREDITPOLICY = 'CREDITPOLICY';
    const OPT_CREDITPOLICY_ACTIVITY = 'activity';
    const OPT_CREDITPOLICY_CURRENCY = 'currency';

    public static function loadSettings($flush = false) {
        $s = C('MS');
        if(empty($s) || $flush) {
            $keys = array();
            $keys[] = self::OPT_POLICY;
            $keys[] = self::OPT_CREDITS;
            $keys[] = self::OPT_CREDITPOLICY;
            $s = Utility::loadSettings('Member', $keys);
            if(empty($s[self::OPT_POLICY])) {
                $s[self::OPT_POLICY] = self::OPT_POLICY_UNION;
            }
            if(empty($s[self::OPT_CREDITS]) || !is_array($s[self::OPT_CREDITS])) {
                $s[self::OPT_CREDITS] = array(
                    array(
                        'name'      => 'credit1',
                        'title'     => '积分',
                        'enabled'   => true,
                        'issystem'  => true,
                    ),
                    array(
                        'name'      => 'credit2',
                        'title'     => '余额',
                        'enabled'   => true,
                        'issystem'  => true,
                    ),
                    array(
                        'name'      => 'credit3',
                        'title'     => '',
                        'enabled'   => false,
                        'issystem'  => false,
                    ),
                    array(
                        'name'      => 'credit4',
                        'title'     => '',
                        'enabled'   => false,
                        'issystem'  => false,
                    ),
                    array(
                        'name'      => 'credit5',
                        'title'     => '',
                        'enabled'   => false,
                        'issystem'  => false,
                    ),
                );
            }
            if(empty($s[self::OPT_CREDITPOLICY]) || !is_array($s[self::OPT_CREDITPOLICY])) {
                $s[self::OPT_CREDITPOLICY] = array(
                    self::OPT_CREDITPOLICY_ACTIVITY => 'credit1',
                    self::OPT_CREDITPOLICY_CURRENCY => 'credit2',
                );
            }
            C('MS', $s);
        }
    }

    public static function saveSettings($settings) {
        $keys = array();
        $keys[] = self::OPT_POLICY;
        $keys[] = self::OPT_CREDITS;
        $keys[] = self::OPT_CREDITPOLICY;
        $settings = coll_elements($keys, $settings);
        return Utility::saveSettings('Member', $settings);
    }

    public function getGroups() {
        $ret = array();
        $condition = '';
        $pars = array();
        $roles = $this->table('__MMB_GROUPS__')->where($condition)->bind($pars)->order('`orderlist`')->select();
        if(!empty($roles)) {
            $ret = array_merge($ret, $roles);
        }
        return $ret;
    }

    public function deleteGroup($id) {
        $id = intval($id);
        $ret = $this->table('__MMB_GROUPS__')->where("`id`={$id}")->delete();
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
