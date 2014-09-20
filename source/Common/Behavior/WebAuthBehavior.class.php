<?php
/**
 * 会话处理
 */
namespace Common\Behavior;
use Common\Model\Acl;

class WebAuthBehavior {
    public function run(&$params) {
        $module = MODULE_NAME;
        $controller = CONTROLLER_NAME;
        $action = ACTION_NAME;

        $user = session('user');
        if(!empty($user)) {
            $session = array();
            $session['USER'] = array_change_key_case($user, CASE_UPPER);

            $acl = new Acl();
            $roles = $acl->getRoles();
            $roles = coll_key($roles, 'id');
            $role = $roles[$user['role']];
            if(empty($role)) {
                $role = $roles[0];
            }
            $role = coll_elements(array('id', 'title'), $role);
            $session['ROLE'] = array_change_key_case($role, CASE_UPPER);

            $session['ACL'] = array();
            C('SESSION', $session);
        }

        //无用户身份, 只能访问Wander
        if((empty($session) || empty($session['USER'])) && !in_array($module, array('Wander'))) {
            redirect(U('/wander'));
            exit;
        }
    }
}