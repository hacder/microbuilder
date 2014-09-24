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
        util_curd($this, 'alipay');
    }

    public function alipayList() {
        $p = new Account();
        $condition = '`type`=:type';
        $pars = array();
        $pars[':type'] = Account::ACCOUNT_ALIPAY;
        $platforms = $p->table('__PLATFORMS__')->where($condition)->bind($pars)->select();
        $this->assign('platforms', $platforms);
        $this->display('alipay');
    }

    public function alipayModify() {
        $id = intval(I('get.id'));
        if(empty($id)) {
            $this->error('访问错误');
        }
        $p = new Account();
        $platform = $p->getPlatform($id, Account::ACCOUNT_ALIPAY);
        if(empty($platform)) {
            $this->error('访问错误');
        }
        if(IS_POST) {
            if(I('post.method') == 'generate') {
                $ret = Utility::sslGenKey();
                if(!is_error($ret)) {
                    $rec = array();
                    $rec['public_key'] = $ret['public'];
                    $rec['private_key'] = $ret['private'];
                    $p->table('__PLATFORM_ALIPAY__')->data($rec)->where("`id`='{$id}'")->save();
                }
                exit(json_encode($ret));
            }
            $ret = $p->modify(Account::ACCOUNT_ALIPAY, $id);
            if(is_error($ret)) {
                $this->error($ret['message']);
            }
            $this->success('保存成功');
            exit;
        }

        $isGen = function_exists('openssl_pkey_new');

        $this->assign('isGen', $isGen);
        $this->assign('entity', $platform);
        $this->display('alipay-form');
    }

    public function alipayCreate() {
        if(IS_POST) {
            $p = new Account();
            $ret = $p->create(Account::ACCOUNT_ALIPAY);
            if(is_error($ret)) {
                $this->error($ret['message']);
            }
            $this->success('成功新增服务窗账号, 接下来您可以将这个服务窗接入您的系统了', U('control/platform/alipay?do=modify&id=' . $ret));
            exit;
        }
        $this->display('alipay-form');
    }

    public function alipayDelete() {

    }
}