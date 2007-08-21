<?php

require_once 'HTMLPurifier/DefinitionCache.php';

HTMLPurifier_ConfigSchema::define(
    'Cache', 'SerializerPath', null, 'string/null', '
<p>
    Absolute path with no trailing slash to store serialized definitions in.
    Default is within the
    HTML Purifier library inside DefinitionCache/Serializer. This
    path must be writable by the webserver. This directive has been
    available since 2.0.0.
</p>
');

class HTMLPurifier_DefinitionCache_Serializer extends
      HTMLPurifier_DefinitionCache
{
    
    function add($def, $config) {
        if (!$this->checkDefType($def)) return;
        $file = $this->generateFilePath($config);
        if (file_exists($file)) return false;
        if (!$this->_prepareDir($config)) return false;
        return $this->_write($file, serialize($def));
    }
    
    function set($def, $config) {
        if (!$this->checkDefType($def)) return;
        $file = $this->generateFilePath($config);
        if (!$this->_prepareDir($config)) return false;
        return $this->_write($file, serialize($def));
    }
    
    function replace($def, $config) {
        if (!$this->checkDefType($def)) return;
        $file = $this->generateFilePath($config);
        if (!file_exists($file)) return false;
        if (!$this->_prepareDir($config)) return false;
        return $this->_write($file, serialize($def));
    }
    
    function get($config) {
        $file = $this->generateFilePath($config);
        if (!file_exists($file)) return false;
        return unserialize(file_get_contents($file));
    }
    
    function remove($config) {
        $file = $this->generateFilePath($config);
        if (!file_exists($file)) return false;
        return unlink($file);
    }
    
    function flush($config) {
        if (!$this->_prepareDir($config)) return false;
        $dir = $this->generateDirectoryPath($config);
        $dh  = opendir($dir);
        while (false !== ($filename = readdir($dh))) {
            if (empty($filename)) continue;
            if ($filename[0] === '.') continue;
            unlink($dir . '/' . $filename);
        }
    }
    
    function cleanup($config) {
        if (!$this->_prepareDir($config)) return false;
        $dir = $this->generateDirectoryPath($config);
        $dh  = opendir($dir);
        while (false !== ($filename = readdir($dh))) {
            if (empty($filename)) continue;
            if ($filename[0] === '.') continue;
            $key = substr($filename, 0, strlen($filename) - 4);
            if ($this->isOld($key, $config)) unlink($dir . '/' . $filename);
        }
    }
    
    /**
     * Generates the file path to the serial file corresponding to
     * the configuration and definition name
     */
    function generateFilePath($config) {
        $key = $this->generateKey($config);
        return $this->generateDirectoryPath($config) . '/' . $key . '.ser';
    }
    
    /**
     * Generates the path to the directory contain this cache's serial files
     * @note No trailing slash
     */
    function generateDirectoryPath($config) {
        $base = $this->generateBaseDirectoryPath($config);
        return $base . '/' . $this->type;
    }
    
    /**
     * Generates path to base directory that contains all definition type
     * serials
     */
    function generateBaseDirectoryPath($config) {
        $base = $config->get('Cache', 'SerializerPath');
        $base = is_null($base) ? HTMLPURIFIER_PREFIX . '/HTMLPurifier/DefinitionCache/Serializer' : $base;
        return $base;
    }
    
    /**
     * Convenience wrapper function for file_put_contents
     * @param $file File name to write to
     * @param $data Data to write into file
     * @return Number of bytes written if success, or false if failure.
     */
    function _write($file, $data) {
        static $file_put_contents;
        if ($file_put_contents === null) {
            $file_put_contents = function_exists('file_put_contents');
        }
        if ($file_put_contents) {
            return file_put_contents($file, $data);
        }
        $fh = fopen($file, 'w');
        if (!$fh) return false;
        $status = fwrite($fh, $data);
        fclose($fh);
        return $status;
    }
    
    /**
     * Prepares the directory that this type stores the serials in
     * @return True if successful
     */
    function _prepareDir($config) {
        $directory = $this->generateDirectoryPath($config);
        if (!is_dir($directory)) {
            $base = $this->generateBaseDirectoryPath($config);
            if (!is_dir($base)) {
                trigger_error('Base directory '.$base.' does not exist,
                    please create or change using %Cache.SerializerPath',
                    E_USER_ERROR);
                return false;
            } elseif (!$this->_testPermissions($base)) {
                return false;
            }
            mkdir($directory);
        } elseif (!$this->_testPermissions($directory)) {
            return false;
        }
        return true;
    }
    
    /**
     * Tests permissions on a directory and throws out friendly
     * error messages and attempts to chmod it itself if possible
     */
    function _testPermissions($dir) {
        // early abort, if it is writable, everything is hunky-dory
        if (is_writable($dir)) return true;
        if (!is_dir($dir)) {
            // generally, you'll want to handle this beforehand
            // so a more specific error message can be given
            trigger_error('Directory '.$dir.' does not exist',
                E_USER_ERROR);
            return false;
        }
        if (function_exists('posix_getuid')) {
            // POSIX system, we can give more specific advice
            if (fileowner($dir) === posix_getuid()) {
                // we can chmod it ourselves
                chmod($dir, 0755);
                return true;
            } elseif (filegroup($dir) === posix_getgid()) {
                $chmod = '775';
            } else {
                // PHP's probably running as nobody, so we'll
                // need to give global permissions
                $chmod = '777';
            }
            trigger_error('Directory '.$dir.' not writable, '.
                'please chmod to ' . $chmod,
                E_USER_ERROR);
        } else {
            // generic error message
            trigger_error('Directory '.$dir.' not writable, '.
                'please alter file permissions',
                E_USER_ERROR);
        }
        return false;
    }
    
}

