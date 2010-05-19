<?php
/**
 * Class Minify_Cache_File  
 * @package Minify
 */

class Minify_Cache_File {
    
    public function __construct($path = '', $fileLocking = false)
    {
        if (! $path) {
            require_once 'Solar/Dir.php';
            $path = rtrim(Solar_Dir::tmp(), DIRECTORY_SEPARATOR);
        }
        $this->_locking = $fileLocking;
        $this->_path = $path;
    }
    
    /**
     * Write data to cache.
     *
     * @param string $id cache id (e.g. a filename)
     * 
     * @param string $data
     * 
     * @return bool success
     */
    public function store($id, $data)
    {
        $flag = $this->_locking
            ? LOCK_EX
            : null;
        if (is_file($this->_path . '/' . $id)) {
            @unlink($this->_path . '/' . $id);
        }
        if (! @file_put_contents($this->_path . '/' . $id, $data, $flag)) {
            return false;
        }
        // write control
        if ($data !== $this->fetch($id)) {
            @unlink($file);
            return false;
        }
        return true;
    }
    
    /**
     * Get the size of a cache entry
     *
     * @param string $id cache id (e.g. a filename)
     * 
     * @return int size in bytes
     */
    public function getSize($id)
    {
        return filesize($this->_path . '/' . $id);
    }
    
    /**
     * Does a valid cache entry exist?
     *
     * @param string $id cache id (e.g. a filename)
     * 
     * @param int $srcMtime mtime of the original source file(s)
     * 
     * @return bool exists
     */
    public function isValid($id, $srcMtime)
    {
        $file = $this->_path . '/' . $id;
        return (is_file($file) && (filemtime($file) >= $srcMtime));
    }
    
    /**
     * Send the cached content to output
     *
     * @param string $id cache id (e.g. a filename)
     */
    public function display($id)
    {
        if ($this->_locking) {
            $fp = fopen($this->_path . '/' . $id, 'rb');
            flock($fp, LOCK_SH);
            fpassthru($fp);
            flock($fp, LOCK_UN);
            fclose($fp);
        } else {
            readfile($this->_path . '/' . $id);            
        }
    }
    
	/**
     * Fetch the cached content
     *
     * @param string $id cache id (e.g. a filename)
     * 
     * @return string
     */
    public function fetch($id)
    {
        if ($this->_locking) {
            $fp = fopen($this->_path . '/' . $id, 'rb');
            flock($fp, LOCK_SH);
            $ret = stream_get_contents($fp);
            flock($fp, LOCK_UN);
            fclose($fp);
            return $ret;
        } else {
            return file_get_contents($this->_path . '/' . $id);
        }
    }
    
    /**
     * Fetch the cache path used
     *
     * @return string
     */
    public function getPath()
    {
        return $this->_path;
    }
    
    private $_path = null;
    private $_locking = null;
}
