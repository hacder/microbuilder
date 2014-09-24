<?php
/**
 * 用户管理
 */
namespace Control\Controller;
use Core\Model\Acl;
use Core\Model\Utility;
use Think\Controller;

class UserController extends Controller {
    /**
     * @var Acl
     */
    private $acl;
    private $roles;

    public function _initialize() {
        C('FRAME_ACTIVE', 'access');
        C('FRAME_CURRENT', U('control/user/list'));

        $this->acl = new Acl();
        $this->roles = $this->acl->getRoles(true);
        $this->assign('roles', $this->roles);
    }

    public function listAction() {
        $condition = '';
        $pars = array();

        $users = $this->acl->table('__USR_USERS__')->where($condition)->bind($pars)->select();
        if(!empty($users)) {
            $roles = coll_key($this->roles, 'id');
            foreach($users as &$user) {
                $user['role'] = $roles[$user['role']];
            }
        }

        $this->assign('users', $users);
        $this->display();
    }

    public function createAction() {
        if(IS_POST) {
            $input = $this->validateForm();
            $user = $this->acl->getUser($input['username'], true);
            if(!empty($user)) {
                $this->error('用户名已经存在, 请返回修改');
            }
            $input['salt'] = util_random(8);
            $input['password'] = Utility::encodePassword($input['password'], $input['salt']);
            $ret = $this->acl->table('__USR_USERS__')->data($input)->add();
            if(empty($ret)) {
                $this->error('保存新增用户失败, 请稍后重试');
            } else {
                $this->success('成功新增管理用户');
                exit;
            }
        }

        $this->display('form');
    }

    public function modifyAction($uid) {
        $uid = intval($uid);
        $user = $this->acl->getUser($uid, true);
        if(empty($user)) {
            $this->error('访问错误');
        }
        if(IS_POST) {
            $input = $this->validateForm(true);
            $input = coll_elements(array('password', 'role', 'status'), $input);
            $input['password'] = Utility::encodePassword($input['password'], $user['salt']);
            $ret = $this->acl->table('__USR_USERS__')->data($input)->where("`uid`={$uid}")->save();
            if(empty($ret)) {
                $this->error('保存用户信息失败, 请稍后重试');
            } else {
                $this->success('保存成功');
                exit;
            }
        }
        $this->assign('user', $user);
        $this->display('form');
    }

    public function deleteAction($uid) {
        $uid = intval($uid);
        if($uid == '1') {
            $this->error('创建用户不能删除');
        }
        $user = $this->acl->getUser($uid, true);
        if(empty($user)) {
            $this->error('访问错误');
        }
        $ret = $this->acl->table('__USR_USERS__')->where("`uid`={$uid}")->delete();
        if(empty($ret)) {
            $this->error('删除用户信息失败, 请稍后重试');
        } else {
            $this->success('删除成功');
        }
    }

    private function validateForm($modify = false) {
        $input = coll_elements(array('username', 'password', 'role', 'status'), I('post.'));
        $input['username'] = trim($input['username']);
        if(empty($modify)) {
            if(empty($input['username']) || empty($input['password'])) {
                $this->error('请输入用户名及登陆密码');
            }
        }
        if($input['role'] === false) {
            $this->error('必须指定用户组');
        }
        $roles = coll_key($this->roles, 'id');
        if(empty($roles[$input['role']])) {
            $input['role'] = '0';
        }

        $input['status'] = $input['status'] == '-1' ? '-1' : '0';
        return $input;
    }
}