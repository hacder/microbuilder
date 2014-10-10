<?php
namespace Addon\Deploy\Api;

use Core\Model\Addon;

class Application {
    /**
     * @var Addon
     */
    public $addon;
    
    private $installSql = <<<'DOC'
DOC;

    private $uninstallSql = <<<'DOC'
DOC;

    
    public function install() {
        $this->addon->pasteControlEntry('发布', 'publish/exec');
        return true;
    }
    
    public function uninstall() {
    }
    
    public function upgrade($versionOriginal, $versionNew) {
        
    }
}
