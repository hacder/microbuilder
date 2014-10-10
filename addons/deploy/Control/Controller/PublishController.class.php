<?php
namespace Addon\Deploy\Control\Controller;
use Addon\Bridge\Model\Deploy;
use Core\AddonController;
use Core\Model\Utility;

class PublishController extends AddonController {
    public function execAction() {
        $u = new Utility();
        $tables = array(
            'core_settings',
            'ex_addon_entries',
            'ex_addons',
            'mmb_groups',
            'mmb_mapping_fans',
            'mmb_mapping_ucenter',
            'mmb_members',
            'mmb_profiles',
            'platform_alipay',
            'platforms',
            'rp_processors',
            'rp_replies',
            'usr_acl',
            'usr_resources',
            'usr_roles',
            'usr_users',
        );
        $schemas = array();
        foreach($tables as $table) {
            $schemas[] = $u->dbTableSchema($table);
        }
        exit(serialize($schemas));
    }
}