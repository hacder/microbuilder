<?php
/**
 * 行为嵌入点定义
 */
$tags = array();
if(defined('IN_APP') && IN_APP === true) {
} else {
    $tags['view_begin'] = array(
        'Common\Behavior\WebTemplateBehavior'
    );
    $tags['action_begin'] = array(
        'Common\Behavior\WebAuthBehavior'
    );
}

return $tags;
