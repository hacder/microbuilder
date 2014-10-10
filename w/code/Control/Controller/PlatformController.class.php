<?php
/**
 * 营销渠道平台
 */
namespace Control\Controller;
use Core\Model\Account;
use Core\Model\Utility;
use Core\Platform\Alipay;
use Think\Controller;

class PlatformController extends Controller {

    public function alipayAction() {
        C('FRAME_CURRENT', U('control/platform/alipay'));
        util_curd($this, 'alipay');
    }

    public function alipayList() {
        $a = new Account();
        $condition = '`type`=:type';
        $pars = array();
        $pars[':type'] = Account::ACCOUNT_ALIPAY;
        $accounts = $a->table('__PLATFORMS__')->where($condition)->bind($pars)->select();
        $this->assign('accounts', $accounts);
        $this->display('alipay');
    }

    public function alipayModify() {
        $id = intval(I('get.id'));
        if(empty($id)) {
            $this->error('访问错误');
        }
        $a = new Account();
        $account = $a->getPlatform($id, Account::ACCOUNT_ALIPAY);
        if(empty($account)) {
            $this->error('访问错误');
        }
        if(IS_POST) {
            if(I('post.method') == 'generate') {
                $ret = Utility::sslGenKey();
                if(!is_error($ret)) {
                    $rec = array();
                    $rec['public_key'] = $ret['public'];
                    $rec['private_key'] = $ret['private'];
                    $a->table('__PLATFORM_ALIPAY__')->data($rec)->where("`id`='{$id}'")->save();
                }
                exit(json_encode($ret));
            }
            $ret = $a->modify(Account::ACCOUNT_ALIPAY, $id);
            if(is_error($ret)) {
                $this->error($ret['message']);
            }
            $this->success('保存成功');
            exit;
        }

        $isGen = function_exists('openssl_pkey_new');

        $this->assign('isGen', $isGen);
        $this->assign('entity', $account);
        $this->display('alipay-form');
    }

    public function alipayCreate() {
        if(IS_POST) {
            $a = new Account();
            $ret = $a->create(Account::ACCOUNT_ALIPAY);
            if(is_error($ret)) {
                $this->error($ret['message']);
            }
            $this->success('成功新增服务窗账号, 接下来您可以将这个服务窗接入您的系统了', U('control/platform/alipay?do=modify&id=' . $ret));
            exit;
        }
        $this->display('alipay-form');
    }

    public function alipayDelete() {
        $id = intval(I('get.id'));
        if(empty($id)) {
            $this->error('访问错误');
        }
        $a = new Account();
        $account = $a->getPlatform($id, Account::ACCOUNT_ALIPAY);
        if(empty($account)) {
            $this->error('访问错误');
        }
        $a->remove($id);
        $this->success('删除成功');
        exit;
    }
    
    public function weixinAction() {
        $this->display('weixin');
    }
    
    public function openAction() {
        $this->display('open');
    }
}