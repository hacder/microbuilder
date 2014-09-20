<?php
/**
 * 管理中心欢迎页
 */
namespace Control\Controller;
use Think\Controller;
class WelcomeController extends Controller {
    public function _empty($name){
        $name = preg_replace('/' . C('ACTION_SUFFIX') . '$/', '', $name);
        C('FRAME_ACTIVE', $name);
        $this->display('Welcome/control');
    }
}