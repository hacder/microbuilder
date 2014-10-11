<?php
namespace Core\Util;

class File {
    public static function tree($path) {
        $files = array();
        $ds = glob($path . '/*');
        if(is_array($ds)) {
            foreach($ds as $entry) {
                if(is_file($entry)) {
                    $files[] = $entry;
                }
                if(is_dir($entry)) {
                    $rs = self::tree($entry);
                    foreach($rs as $f) {
                        $files[] = $f;
                    }
                }
            }
        }
        return $files;
    }

    public  function rmdirs($path, $clean=false) {
        if(!is_dir($path)) {
            return false;
        }
        $files = glob($path . '/*');
        if($files) {
            foreach($files as $file) {
                is_dir($file) ? self::rmdirs($file) : @unlink($file);
            }
        }
        return $clean ? true : @rmdir($path);
    }
}