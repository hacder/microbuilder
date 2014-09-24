<?php
/**
 * 支付宝服务窗接口
 */
namespace Api\Controller;
use Think\Controller;

class AlipayController extends Controller {
    public function _empty($name){
        return $this->gatewayAction();
    }

    public function gatewayAction() {
        preg_match('/^api\/alipay\/(?P<id>\d+)$/i', __INFO__, $match);
        $id = intval($match['id']);
        if(empty($id)) {
            exit('request failed. miss platform id');
        }
        $post = I('post.', '', '');
        $platform = \Core\Platform\Platform::create($id, \Core\Model\Platform::PLATFORM_ALIPAY);
        if(empty($platform)) {
            exit('request failed. error platform id');
        }
        $platform->checkSign();

        if($post['service'] == 'alipay.service.check') {
            $platform->touchCheck();
        }
        if($post['service'] == 'alipay.mobile.public.message.notify') {
            $message = $platform->parse($post['biz_content']);
        }
    }
}

