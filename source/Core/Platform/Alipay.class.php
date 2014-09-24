<?php
/**
 * 支付宝服务窗平台
 */
namespace Core\Platform;
use Core\Model\Utility;
use Core\Platform\Alipay\AliClient;

class Alipay extends Platform {
    /**
     * @var \Core\Model\Account
     */
    private $platform;
    /**
     * @var AliClient;
     */
    private $client;
    /**
     * @var array
     */
    public  $params;

    /**
     * 特定公众号平台的操作对象构造方法
     *
     * @param array $platform 公号平台基础对象
     */
    public function __construct($platform) {
        $this->platform = $platform;
        $this->client = new AliClient($platform);
        $this->params = I('post.', '', '');
    }

    public function getPlatform() {
        return $this->platform;
    }

    public function checkSign() {
        $ret = $this->client->checkSignAndDecrypt($this->params, true, false);
        if(empty($ret)) {
            exit('signature failed');
        }
    }

    public function touchCheck() {
        $pub = Utility::sslTrimKey($this->platform['public_key']);
        $ret = "<biz_content>{$pub}</biz_content><success>true</success>";
        $dat = $this->client->encryptAndSign($ret, false, true);
        parent::touchCheck();
        $message = $this->parse($this->params['biz_content']);
        $rec = array();
        $rec['appid'] = $message['to'];
        $m = new Model();
        $m->table('__PLATFORM_ALIPAY__')->data($rec)->where("`id`='{$this->platform['id']}'")->save();
        exit($dat);
    }

    public function parse($message) {
        $packet = array();
        if (!empty($message)){
            $xml = '<?xml version="1.0" encoding="GBK"?>' . $message;
            $dom = new DOMDocument('1.0', 'GBK');
            if($dom->loadXML($xml)) {
                $node = $dom->getElementsByTagName('FromUserId');
                $packet['from'] = strval($node->item(0)->nodeValue);
                $node = $dom->getElementsByTagName('AppId');
                $packet['to'] = strval($node->item(0)->nodeValue);
                $node = $dom->getElementsByTagName('AppId');
                $packet['time'] = intval(substr(strval($node->item(0)->nodeValue), 0, 10));
                $node = $dom->getElementsByTagName('MsgType');
                $packet['type'] = strval($node->item(0)->nodeValue);
                $node = $dom->getElementsByTagName('EventType');
                $packet['event'] = strval($node->item(0)->nodeValue);

                foreach ($dom as $variable => $property) {
                    $packet[strtolower($variable)] = (string)$property;
                }

                //处理其他事件类型
                if($packet['type'] == 'event') {
                    $packet['type'] = $packet['event'];
                }
                if($packet['type'] == 'follow') {
                    //开始关注
                    $packet['type'] = 'subscribe';
                }
                if($packet['type'] == 'unfollow') {
                    //取消关注
                    $packet['type'] = 'unsubscribe';
                }
            }
        }
        return $packet;
    }

    public function queryAvailablePackets($type = '') {
        return array(
            Platform::POCKET_TEXT,
            Platform::POCKET_NEWS
        );
    }


    public function isPushSupported() {
        return true;
    }

    public function push($uid, $packet) {
        import_third('aop.request.AlipayMobilePublicMessageCustomSendRequest');
        $request = new \AlipayMobilePublicMessageCustomSendRequest();
        $set = array();
        $set['toUserId'] = 'L6OOPFU1auUKydq9vHxkKTvoMnZQkHvGW828bvjfD40ZcoV6valQ9EUNUhwK9mTS01';
        $set['msgType'] = 'text';
        $set['createTime'] = TIMESTAMP * 1000;
        $set['text'] = array();
        $set['text']['content'] = $packet['content'];
        $request->setBizContent(json_encode($set));
        $resp = $this->client->execute($request);
        return $resp;
    }
}
