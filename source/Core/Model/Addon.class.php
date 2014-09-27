<?php
namespace Core\Model;
use Core\Platform\Platform;
use Think\Model;

class Addon extends Model {
    protected $autoCheckFields = false;
    
    public static function autoload() {
        spl_autoload_register(function($class){
            $pieces = explode('\\', $class, 2);
            if($pieces[0] == 'Addon' && !empty($pieces[1])) {
                $filename = MB_ROOT . 'addons/' . str_replace('\\', '/', $pieces[1]);
                $filename .= '.class.php';
                if(is_file($filename)) {
                    include $filename;
                }
            }
        });
    }
    
    const TYPE_APP      = 'app';
    const TYPE_ACTIVITY = 'activity';
    const TYPE_CRM      = 'crm';
    const TYPE_GAME     = 'game';
    const TYPE_TOOL     = 'tool';
    const TYPE_OTHER    = 'other';
    
    const ENTRY_BENCH   = 'bench';
    const ENTRY_CONTROL = 'control';
    const ENTRY_API     = 'api';
    const ENTRY_WANDER  = 'wander';

    public static function types() {
        static $types;
        if(empty($types)) {
            $types['app'] = array(
                'name' => 'app',
                'title' => 'WebApp',
                'desc' => ''
            );
            $types['activity'] = array(
                'name' => 'activity',
                'title' => '营销及活动',
                'desc' => ''
            );
            $types['crm'] = array(
                'name' => 'crm',
                'title' => '客户关系',
                'desc' => ''
            );
            $type['game'] = array(
                'name' => 'game',
                'title' => '客户关系',
                'desc' => ''
            );
            $types['tool'] = array(
                'name' => 'tool',
                'title' => '服务及工具',
                'desc' => ''
            );
            $types['other'] = array(
                'name' => 'other',
                'title' => '其他',
                'desc' => ''
            );
        }
        return $types;
    }
    
    public static function getAddons($type = '') {
        $condition = '';
        $pars = array();
        if(!empty($type)) {
            $condition = '`type`=:type';
            $pars[':type'] = $type;
        }

        $m = new Model();
        $addons = $m->table('__EX_ADDONS__')->where($condition)->bind($pars)->select();
        return $addons;
    }
    
    private $addon;

    function __construct($addon) {
        parent::__construct();
        if(is_array($addon)) {
            $this->addon = $addon;
        } else {
            $condition = '`name`=:name';
            $pars = array();
            $pars[':name'] = strval($addon);
            $addon = $this->table('__EX_ADDONS__')->where($condition)->bind($pars)->find();
            if(empty($addon)) {
                trigger_error('扩展不存在', E_USER_WARNING);
            }
            $this->addon = $addon;
        }
    }
    
    public function getCurrentAddon() {
        return $this->addon;
    }

    public function getEntries($type) {
        $condition = '`addon`=:addon AND `type`=:type';
        $pars = array();
        $pars[':addon'] = $this->addon['name'];
        $pars[':type'] = $type;

        $entries = $this->table('__EX_ADDON_ENTRIES__')->where($condition)->bind($pars)->select();
        return $entries;
    }

    /**
     * 为当前扩展注册关键字
     *
     * @param string $keyword 关键字
     * @param int $resp       响应内容, 大于0是响应内容, 等于零是动态调用
     * @param string $match   匹配方式
     * @param string $extra   附加保存数据
     * @param int $order      优先级
     * @param string $remark  备注
     * @return error|string   处理器标识
     */
    public function registerKeyword($keyword, $resp = 0, $match = Processor::MATCH_EQUAL, $extra = '', $order = 0, $remark = '') {
        $matchs = array(
            Processor::MATCH_CONTAINS,
            Processor::MATCH_EQUAL,
            Processor::MATCH_REGEX
        );
        $match = in_array($match, $matchs) ? $match : Processor::MATCH_EQUAL;
        
        $rec = array();
        $rec['msg_type'] = Platform::MSG_TEXT;
        $rec['msg_match'] = $match;
        $rec['msg_content'] = $keyword;
        $rec['resp_forward'] = $resp;
        $rec['resp_extra'] = $extra;
        $rec['from'] = $this->addon['name'];
        $rec['remark'] = $remark;
        $rec['orderlist'] = $order;
        $rec['status'] = 1;
        
        $ret = $this->table('__RP_PROCESSORS__')->data($rec)->add();
        if(empty($ret)) {
            return error(-2, '保存处理器失败');
        }
        return $this->getLastInsID();
    }

    /**
     * 为当前扩展注册接管
     *
     * @param int $resp 
     * @param string $extra
     * @param int $order
     * @param string $remark
     * @return error|string 成功返回处理器标识, 失败返回错误信息
     */
    public function registerTakeOver($resp = 0, $extra = '', $order = 0, $remark = '') {
        $condition = '`from`=:from AND `msg_match`=:match AND `order`=:order';
        $pars = array();
        $pars[':from'] = $this->addon['name'];
        $pars[':match'] = Processor::MATCH_TAKEOVER;
        $pars[':order'] = $order;
        $rec = $this->table('__RP_PROCESSORS__')->where($condition)->bind($pars)->find();
        if(!empty($rec)) {
            return error(-1, '这个优先级的接管操作已经定义, 请检查');
        }
        
        $rec = array();
        $rec['msg_type'] = '';
        $rec['msg_match'] = Processor::MATCH_TAKEOVER;
        $rec['msg_content'] = '';
        $rec['resp_forward'] = $resp;
        $rec['resp_extra'] = $extra;
        $rec['from'] = $this->addon['name'];
        $rec['remark'] = $remark;
        $rec['orderlist'] = $order;
        $rec['status'] = 1;

        $ret = $this->table('__RP_PROCESSORS__')->data($rec)->add();
        if(empty($ret)) {
            return error(-2, '注册项注册失败');
        } else {
            return $this->getLastInsID();
        }
    }

    /**
     * 为当前扩展注册特殊类型
     *
     * @param string $msgType 消息类型
     * @param string $params  消息参数
     * @param int $resp
     * @param string $extra
     * @param int $order
     * @param string $remark
     * @return error|string 成功返回处理器标识, 失败返回错误信息
     */
    public function registerType($msgType, $params = '', $resp = 0, $extra = '', $order = 0, $remark = '') {
        $rec = array();
        $rec['msg_type'] = $msgType;
        $rec['msg_match'] = '';
        $rec['msg_content'] = $params;
        $rec['resp_forward'] = $resp;
        $rec['resp_extra'] = $extra;
        $rec['from'] = $this->addon['name'];
        $rec['remark'] = $remark;
        $rec['orderlist'] = $order;
        $rec['status'] = 1;

        $ret = $this->table('__RP_PROCESSORS__')->data($rec)->add();
        if(empty($ret)) {
            return error(-2, '注册项注册失败');
        } else {
            return $this->getLastInsID();
        }
    }

    /**
     * 清除注册的处理器
     * @param $id
     * @return bool
     */
    public function unRegister($id) {
        $ret = $this->table('__RP_PROCESSORS__')->where("`id`='{$id}'")->delete();
        if(empty($ret)) {
            return false;
        } else {
            return true;
        }
    }
}
