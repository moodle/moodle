<?php

class ChameleonFileBrowser {
    var $root;
    var $path;
    var $dir;
    var $IMAGE_TYPES;
  
    var $founddirs = array();
    var $foundfiles = array();

    function ChameleonFileBrowser() {
        $this->IMAGE_TYPES = array('jpeg', 'jpg', 'gif', 'png');
        
        $tmp = explode('/', str_replace('\\', '/', __FILE__));
        array_pop($tmp);
        array_pop($tmp);
        $this->root = implode('/', $tmp);

        $this->path = $this->sanitisepath($_GET['path']);
        $this->dir = $this->root . '/' . $this->path;
    }

    function sanitisepath($path) {
        if ($path == 'root') {
            return 'pix';
        }
        
        if (substr($path, 0, 3) != 'pix') {
            $this->send('<chameleon_error>Not a valid directory</chameleon_error>');
        }
        
        return preg_replace('/[.]+/', '', $path);
    }

    function isimage($file) {
        if (strpos($file, '.') === false) {
            return false;
        }
        return in_array(array_pop(explode('.', $file)), $this->IMAGE_TYPES);
    }

    function readfiles() {
        if (!is_dir($this->dir)) {
            $this->send('<chameleon_error>Not a valid directory</chameleon_error>');
        }
        
        $handle = opendir($this->dir);       
        while (false !== ($file = readdir($handle))) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (is_dir($this->dir . '/' . $file)) {
                $this->founddirs[] = $file;
            } else if ($this->isimage($file)) {
                $this->foundfiles[] = $file;
            }
        }
        closedir($handle);

        sort($this->founddirs, SORT_STRING);
        sort($this->foundfiles, SORT_STRING);
        $this->sendfiles();
    }

    function sendfiles() {
        $out = "<files path=\"$this->path\">\n";
        foreach ($this->founddirs as $file) {
            $out .= "  <file type=\"dir\">$this->path/$file</file>\n";
        }
        foreach ($this->foundfiles as $file) {
            $out .= "  <file type=\"img\">$this->path/$file</file>\n";
        }
        $out .= "</files>";
        
        $this->send($out);
    }
    
    function send($out) {
        header("Content-type: application/xml; charset=utf-8");
        die($out);
    }
}

?>