<?php
namespace Core;
use Core\Model\Addon;
use Think\Controller;

class AddonController extends Controller {
    /**
     * @var array
     */
    private $params;
    /**
     * @var Addon
     */
    protected $addon;
    
    function __construct($params, $addon) {
        parent::__construct();
        $this->params = $params;
        $this->addon = $addon;
        $this->assign('__addon', $this->addon->getCurrentAddon());
        $this->assign('__controller', $this);
        $this->assign('C', $this);
    }
    
    public function U($url='', $vars='') {
        $entry = strtolower(MODULE_NAME);
        return $this->addon->U($entry, $url, $vars);
    }

    protected function display($templateFile = '', $charset = '', $contentType = '', $content = '', $prefix = '') {
        $tmp = $templateFile;
        if(empty($templateFile)) {
            $templateFile = $this->params['Action'];
        }
        $pieces = explode('/', $templateFile);
        if(count($pieces) <= 3) {
            if(count($pieces) == 1) {
                $controller = ucfirst($this->params['Controller']);
                $templateFile = MB_ROOT . "addons/{$this->params['Addon']}/{$this->params['Entry']}/View/{$controller}/{$pieces[0]}.html";
            }
            if(count($pieces) == 2) {
                $controller = ucfirst($pieces[0]);
                $templateFile = MB_ROOT . "addons/{$this->params['Addon']}/{$this->params['Entry']}/View/{$controller}/{$pieces[1]}.html";
            }
            if(count($pieces) == 3) {
                $entry = ucfirst($pieces[0]);
                $controller = ucfirst($pieces[1]);
                $templateFile = MB_ROOT . "addons/{$this->params['Addon']}/{$entry}/View/{$controller}/{$pieces[2]}.html";
            }
            if(!is_file($templateFile)) {
                $templateFile = $tmp;
            }
        }
        parent::display($templateFile, $charset, $contentType, $content, $prefix);
    }

}
