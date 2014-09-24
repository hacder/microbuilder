<?php
/**
 * 用户会话
 */
namespace Wander\Controller;
use Core\Model\Acl;
use Core\Model\Utility;
use Think\Controller;
class AccountController extends Controller {

    public function registerAction() {
        $this->success('注册页面');
    }

    public function loginAction() {
        if(IS_POST) {
            $username = I('post.username');
            $password = I('post.password');
            if(empty($username) || empty($password)) {
                $this->error('请输入用户名及密码');
            }
            $acl = new Acl();
            $user = $acl->getUser($username, true);
            if(!empty($user)) {
                $pwd = Utility::encodePassword($password, $user['salt']);
                if($pwd != $user['password']) {
                    $this->error('您输入的密码错误');
                }
                if($user['status'] == Acl::STATUS_DISABLED) {
                    $this->error('您的账号已经被禁用, 请联系系统管理员');
                }
                $user = coll_elements(array('uid', 'username', 'role'), $user);
                session('user', $user);
                $this->success('成功登陆', U('bench/welcome/index'));
            }
            exit;
        }
        $this->display('Wander/login');
    }

    public function logoutAction() {
        session(null);
        $this->success('成功退出系统');
    }
}