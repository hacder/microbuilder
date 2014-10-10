<?php
/**
 * 扩展功能访问入口
 */
namespace Bench\Controller;
use Core\Model\Addon;
use Think\Controller;

class ExtendController extends Controller {
    public function _empty($name) {
        $pieces = explode('/', __INFO__, 6);
        if(count($pieces) >= 5 && $pieces[0] == 'bench' && $pieces[1] == 'extend') {
            $params = array();
            list($params['Entry'], $action, $params['Addon'], $params['Controller'], $params['Action'], $params['Stuff']) = $pieces;
            unset($_GET[$params['Controller']]);
            $this->proc($params);
            exit;
        }
        
        $name = preg_replace('/' . C('ACTION_SUFFIX') . '$/', '', $name);
        $a = new Addon($name);
        $entries = $a->getEntries(Addon::ENTRY_BENCH);
        
        $this->assign('entity', $a->getCurrentAddon());
        $this->assign('entries', $entries);
        C('FRAME_ACTIVE', 'extend');
        C('FRAME_CURRENT', U('bench/extend/' . $name));
        $this->display('Extend/addon');
    }
    
    private function proc($params) {
        C('FRAME_ACTIVE', 'addons');
        $a = new Addon($params['Addon']);
        define('ADDON_NAME', $params['Addon']);
        C('ADDON_INSTANCE', $a);
        Addon::autoload();
        $class = "Addon\\{$params['Addon']}\\Bench\\Controller\\{$params['Controller']}Controller";
        if(class_exists($class)) {
            $instance = new $class($params, $a);
            $method = $params['Action'] . C('ACTION_SUFFIX');
            if(method_exists($instance, $method)) {
                call_user_func(array($instance, $method));
            } else {
                $this->error("访问的操作 {$params['Action']} 不存在.");
            }
        } else {
            $this->error("访问的控制器 {$params['Controller']} 不存在.");
        }
    }
}