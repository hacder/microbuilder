<?php
/**
 * 工作台欢迎页
 */
namespace Bench\Controller;
use Think\Controller;
class WelcomeController extends Controller {
    public function _empty($name) {
        $name = preg_replace('/' . C('ACTION_SUFFIX') . '$/', '', $name);
        C('FRAME_ACTIVE', $name);
        $this->display('Welcome/bench');
    }

    public function themeAction() {
        if(IS_AJAX) {
            $theme = I('post.theme');
            if(!empty($theme)) {
                cookie('template_theme', $theme, 31536000);
            }
        }
    }
}