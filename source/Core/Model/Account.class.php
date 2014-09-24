<?php
namespace Core\Model;
use Think\Model;
use Think\Upload;

class Account extends Model {
    protected $autoCheckFields = false;
    const ACCOUNT_ALIPAY = 'alipay';


    public function create($type) {
        $rec = array();
        $rec['title'] = trim(I('post.title'));
        if(empty($rec['title'])) {
            return error(1, '必须输入名称');
        }
        $exists = $this->table('__PLATFORMS__')->where('`title`=:title')->bind(array(':title' => $rec['title']))->find();
        if(!empty($exists)) {
            return error(-1, '输入的名称已经被使用');
        }
        $rec['remark'] = I('post.remark');
        $rec['qr'] = '';
        $rec['type'] = $type;
        $rec['isconnect'] = '0';

        $ret = $this->table('__PLATFORMS__')->data($rec)->add();
        if(empty($ret)) {
            return error(-2, '保存失败, 请稍后重试');
        }
        $id = $this->getLastInsID();
        if(empty($id)) {
            return error(-3, '保存失败, 请稍后重试');
        }

        if($type == self::ACCOUNT_ALIPAY) {
            $record = array();
            $record['id'] = $id;
            $this->table('__PLATFORM_ALIPAY__')->data($record)->add();
        }

        $cfg = C('UPLOAD');
        $cfg['rootPath'] = MB_ROOT . 'attachment/qr/';
        $cfg['autoSub'] = false;
        $cfg['saveName'] = $id;
        $cfg['replace'] = true;
        $uploader = new Upload($cfg);
        $ret = $uploader->uploadOne($_FILES['qr']);
        if(empty($ret)) {
            return error(-4, $uploader->getError());
        }
        $rec = array();
        $rec['qr'] = '/attachment/qr/' . $ret['savename'];
        $ret = $this->table('__PLATFORMS__')->data($rec)->where("`id`='{$id}'")->save();
        if($ret === false) {
            return error(-5, '保存失败, 请稍后重试');
        }
        return $id;
    }

    public function modify($type, $id) {
        $rec = array();
        $rec['title'] = trim(I('post.title'));
        if(empty($rec['title'])) {
            return error(1, '必须输入名称');
        }
        $exists = $this->table('__PLATFORMS__')->where("`title`=:title AND `id`!='{$id}'")->bind(array(':title' => $rec['title']))->find();
        if(!empty($exists)) {
            return error(-1, '输入的名称已经被使用');
        }
        $rec['remark'] = I('post.remark');

        if(!empty($_FILES['qr']['name'])) {
            $cfg = C('UPLOAD');
            $cfg['rootPath'] = MB_ROOT . 'attachment/qr/';
            $cfg['autoSub'] = false;
            $cfg['saveName'] = $id;
            $cfg['replace'] = true;
            $uploader = new Upload($cfg);
            $ret = $uploader->uploadOne($_FILES['qr']);
            if(empty($ret)) {
                return error(-4, $uploader->getError());
            }
            $rec['qr'] = '/attachment/qr/' . $ret['savename'];
        }

        $ret = $this->table('__PLATFORMS__')->data($rec)->where("`id`='{$id}'")->save();
        if($ret === false) {
            return error(-2, '保存失败, 请稍后重试');
        }

        if($type == self::ACCOUNT_ALIPAY) {
            $record = array();
            $record['public_key'] = I('post.public_key');
            $record['private_key'] = I('post.private_key');
            $this->table('__PLATFORM_ALIPAY__')->data($record)->where("`id`='{$id}'")->save();
        }
        return true;
    }

    public function getPlatform($id, $type = '') {
        $condition = '`id`=:id';
        $pars = array();
        $pars[':id'] = $id;
        if(!empty($type)) {
            $condition .= ' AND `type`=:type';
            $pars[':type'] = Account::ACCOUNT_ALIPAY;
        }
        $platform = $this->table('__PLATFORMS__')->where($condition)->bind($pars)->find();
        if(!empty($platform)) {
            $alipay = $this->table('__PLATFORM_ALIPAY__')->where('`id`=:id')->bind(array(':id'=>$id))->find();
            $platform = array_merge($platform, $alipay);
        }
        return $platform;
    }
}
