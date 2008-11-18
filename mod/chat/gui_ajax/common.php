<?php
class file_cache{
    private $dir = '';
    public function __construct($dir='/dev/shm'){
        $this->dir = $dir.'/chat_cache';
        if(!is_dir($this->dir)) {
            // create cache folder
            if(mkdir($this->dir, 777)) {
                $this->write('test', 'Deny from all');
            }
        }
    }
    private function write($name, $content){
        $name = $this->dir.'/'.$name.'.data';
        if (file_exists($name)) {
            unlink($name);
        }

        $fp = fopen($name, 'w');
        if($fp) {
            fputs($fp, $content);
            fclose($fp);
        }
    }
    public function get($name){
        $content = '';
        $fp = fopen($this->dir.'/'.$name.'.data', 'r');
        if ($fp){
            while(!feof($fp))
                $content .= fread($fp, 4096);
            fclose($fp);
            return unserialize($content);
        } else {
            return false;
        }
    }
    public function set($name, $content){
        $this->write($name, serialize($content));
    }
}
?>
