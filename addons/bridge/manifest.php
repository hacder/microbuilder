<?php
use Core\Model\Addon;

$manifest = array();
$manifest['processors'] = array();
$manifest['processors'][] = array('type' => 'text', 'match' => 'contains', 'content' => '你好');

$manifest['bench'] = array();
$manifest['bench']['entries'] = array();
$manifest['bench']['entries'][] = array('title' => '接入第三方平台', 'url' => Addon::U('/connect/list'));

$manifest['control'] = array();
$manifest['control']['entries'] = array();
$manifest['control']['entries'][] = array('title' => '', 'url' => '');

$manifest['app'] = array();
$manifest['app']['entries'] = array();
$manifest['app']['entries'][] = array('title' => '', 'url' => '');

$manifest['api'] = array();
$manifest['api']['entries'] = array();
$manifest['api']['entries'][] = array('title' => '', 'url' => '');
return $manifest;