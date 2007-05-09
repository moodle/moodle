<?php

class ChameleonCSS {
    var $error;
    var $base;
    
    var $perm;
    var $temp;

    function ChameleonCSS($base, $perm, $temp) { 
        $this->base = $base;
        $this->perm = $perm;
        $this->temp = $temp;
    }
    
    function update($file, $content = '') {
        if (!is_writable($this->base . $this->$file)) {
            $this->error = $this->$file . ' is not writeable, the file permissions are currently ' . $this->getfilepermissions($this->$file);
            return false;
        }
        
        if (!$fp = fopen($this->base . $this->$file, 'w')) {
            $this->error = 'couldn\'t open file';
            return false;
        }
        fwrite($fp, stripslashes($content));
        fclose($fp);
        return true;
    }
    
    function getfilepermissions($file) {
        return substr(sprintf('%o', fileperms($this->base . $file)), -4);
    }

    function read() {
        $permcss = file_get_contents($this->base . $this->perm);
        $tempcss = file_get_contents($this->base . $this->temp);
           
        if ($permcss === false || $tempcss === false) {
            $this->error = 'Couldn\'t read file';
            return false;
        }
        
        $permcss = trim($permcss);
        $tempcss = trim($tempcss);
        
        if ($tempcss == '') {
            return $permcss;
        }
        return $this->_merge($permcss, $tempcss);
    }
    
    
    
    
    function _merge($permcss, $tempcss) {
        $csssrcs = array($this->_toobj($permcss), $this->_toobj($tempcss));
        
        $merged = array();
        
        for ($i = 0; $i < 2; ++$i) {
            foreach ($csssrcs[$i] as $sel => $rule) {
                $newsel = false;
                if (!isset($merged[$sel])) {
                    $merged[$sel] = array();
                    $newsel = true;
                }
                foreach ($rule as $prop => $value) {
                    $merged[$sel][$prop] = $value;
                }
                if ($i > 0 && !$newsel) {
                    foreach ($merged[$sel] as $prop => $value) {
                        if (!isset($csssrcs[$i][$sel][$prop])) {
                            unset($merged[$sel][$prop]);
                        }
                    }
                }
            }
            if ($i > 0) {
                foreach ($merged as $sel => $value) {
                    if (!isset($csssrcs[$i][$sel])) {
                        unset($merged[$sel]);
                    }
                }
            }
        }
        
        return $this->_tostr($merged);
    }
    
   
    
    
    function _toobj($cssstr) {
        $cssobj = array();
        $end = strpos($cssstr, '}');
        while ($end !== false) {
            $currule = substr($cssstr, 0, $end);
            $parts = explode('{', $currule);
            $selector = trim($parts[0]);
            $rules = trim($parts[1]);
            $rulesarr = explode(';', $rules);

            $num = count($rulesarr);
            for ($i = 0; $i < $num; ++$i) {
                if (strpos($rulesarr[$i], ':') === false) {
                    break;
                }
                $rule = explode(':', $rulesarr[$i]);
                $prop = trim($rule[0]);
                $value = trim($rule[1]);

                if (!isset($cssobj[$selector])) {
                    $cssobj[$selector] = array();
                }
                $cssobj[$selector][$prop] = $value;
            }
            $cssstr = substr($cssstr, $end + 1);
            $end = strpos($cssstr, '}');
        }
        return $cssobj;
    }
    
    
    function _tostr($cssobj) {
        $cssstr = '';
        foreach ($cssobj as $sel => $rule) {
            $cssstr .= "$sel {\n";
            foreach ($rule as $prop => $value) {
                $cssstr .= "  $prop: $value;\n";
            }
            $cssstr .= "}\n";
        }
        return $cssstr;
    }
}


?>