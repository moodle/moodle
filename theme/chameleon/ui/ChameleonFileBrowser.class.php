<?php

class ChameleonFileBrowser {
    var $root;
    var $path;
    var $dir;
    var $IMAGE_TYPES;
  
    var $foundDirs = array();
    var $foundFiles = array();

    function ChameleonFileBrowser() {
        $this->IMAGE_TYPES = array('jpeg', 'jpg', 'gif', 'png');
        
        $tmp = explode('/', str_replace('\\', '/', __FILE__));
        array_pop($tmp);
        array_pop($tmp);
        $this->root = implode('/', $tmp);

        $this->path = $this->sanitisePath($_GET['path']);
        $this->dir = $this->root . '/' . $this->path;
    }

    function sanitisePath($path) {
        if ($path == 'root') {
            return 'pix';
        }
        
        if (substr($path, 0, 3) != 'pix') {
            $this->send('<chameleon_error>Not a valid directory</chameleon_error>');
        }
        
        return preg_replace('/[.]+/', '', $path);
    }

    function isImage($file) {
        if (strpos($file, '.') === false) {
            return false;
        }
        return in_array(array_pop(explode('.', $file)), $this->IMAGE_TYPES);
    }

    function readFiles() {
        if (!is_dir($this->dir)) {
            $this->send('<chameleon_error>Not a valid directory</chameleon_error>');
        }
        
        $handle = opendir($this->dir);       
        while (false !== ($file = readdir($handle))) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (is_dir($this->dir . '/' . $file)) {
                $this->foundDirs[] = $file;
            } else if ($this->isImage($file)) {
                $this->foundFiles[] = $file;
            }
        }
        closedir($handle);

        sort($this->foundDirs, SORT_STRING);
        sort($this->foundFiles, SORT_STRING);
        $this->sendFiles();
    }

    function sendFiles() {
        $out = "<files path=\"$this->path\">\n";
        foreach ($this->foundDirs as $file) {
            $out .= "  <file type=\"dir\">$this->path/$file</file>\n";
        }
        foreach ($this->foundFiles as $file) {
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