<?php
/**
 * 扩展功能访问入口
 */
namespace Bench\Controller;
use Core\Model\Addon;
use Think\Controller;

class EmptyController extends Controller {
    public function _empty() {
        $pieces = explode('/', __INFO__, 5);
        if(count($pieces) >= 4 && $pieces[0] == 'bench') {
            $params = array();
            list($params['Entry'], $params['Addon'], $params['Controller'], $params['Action'], $params['Stuff']) = $pieces;
            unset($_GET[$params['Action']]);
            if(!empty($params['Stuff'])) {
                $var  =  array();
                preg_replace_callback('/(\w+)\/([^\/]+)/', function($match) use(&$var){$var[$match[1]]=strip_tags($match[2]);}, $params['Stuff']);
                $_GET = array_merge($_GET, $var);
            }
            $this->proc($params);
        }
    }
    
    private function proc($params) {
        C('FRAME_ACTIVE', 'addons');
        $a = new Addon($params['Addon']);
        define('ADDON_NAME', $params['Addon']);
        C('ADDON_INSTANCE', $a);
        Addon::autoload();
        $class = "Addon\\{$params['Addon']}\\Bench\\Controller\\{$params['Controller']}Controller";
        $instance = new $class($params, $a);
        $method = $params['Action'] . C('ACTION_SUFFIX');
        call_user_func(array($instance, $method));
    }
}