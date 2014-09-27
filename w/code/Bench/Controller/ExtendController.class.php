<?php
/**
 * 扩展功能访问入口
 */
namespace Bench\Controller;
use Core\Model\Addon;
use Think\Controller;

class ExtendController extends Controller {
    public function _empty($name) {
        $name = preg_replace('/' . C('ACTION_SUFFIX') . '$/', '', $name);
        $a = new Addon($name);
        $entries = $a->getEntries(Addon::ENTRY_BENCH);
        
        $this->assign('entity', $a->getCurrentAddon());
        $this->assign('entries', $entries);
        C('FRAME_ACTIVE', 'extend');
        C('FRAME_CURRENT', U('bench/extend/' . $name));
        $this->display('Extend/bench');
    }
    
    public function listAction() {
        
    }
}