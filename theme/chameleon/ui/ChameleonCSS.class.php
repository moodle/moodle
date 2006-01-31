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
        if (!$fp = fopen($this->base . $this->$file, 'w')) {
            $this->error = 'Couldn\'t open file';
            return false;
        }
        fwrite($fp, stripslashes($content));
        fclose($fp);
        return true;
    }

    function read() {
        $permCSS = trim(file_get_contents($this->base . $this->perm));
        $tempCSS = trim(file_get_contents($this->base . $this->temp));
           
        if ($permCSS === false || $tempCSS === false) {
            $this->error = 'Couldn\'t read file';
            return false;
        }
        
        if ($tempCSS == '') {
            return $permCSS;
        }
        return $this->_merge($permCSS, $tempCSS);
    }
    
    
    
    
    function _merge($permCSS, $tempCSS) {
        $cssSrcs = array($this->_toObj($permCSS), $this->_toObj($tempCSS));
        
        $merged = array();
        
        for ($i = 0; $i < 2; ++$i) {
            foreach ($cssSrcs[$i] as $sel => $rule) {
                $newSel = false;
                if (!isset($merged[$sel])) {
                    $merged[$sel] = array();
                    $newSel = true;
                }
                foreach ($rule as $prop => $value) {
                    $merged[$sel][$prop] = $value;
                }
                if ($i > 0 && !$newSel) {
                    foreach ($merged[$sel] as $prop => $value) {
                        if (!isset($cssSrcs[$i][$sel][$prop])) {
                            unset($merged[$sel][$prop]);
                        }
                    }
                }
            }
            if ($i > 0) {
                foreach ($merged as $sel => $value) {
                    if (!isset($cssSrcs[$i][$sel])) {
                        unset($merged[$sel]);
                    }
                }
            }
        }
        
        return $this->_toStr($merged);
    }
    
   
    
    
    function _toObj($cssStr) {
        $cssObj = array();
        $end = strpos($cssStr, '}');
        while ($end !== false) {
            $curRule = substr($cssStr, 0, $end);
            $parts = explode('{', $curRule);
            $selector = trim($parts[0]);
            $rules = trim($parts[1]);
            $rulesArr = explode(';', $rules);

            $num = count($rulesArr);
            for ($i = 0; $i < $num; ++$i) {
                if (strpos($rulesArr[$i], ':') === false) {
                    break;
                }
                $rule = explode(':', $rulesArr[$i]);
                $prop = trim($rule[0]);
                $value = trim($rule[1]);

                if (!isset($cssObj[$selector])) {
                    $cssObj[$selector] = array();
                }
                $cssObj[$selector][$prop] = $value;
            }
            $cssStr = substr($cssStr, $end + 1);
            $end = strpos($cssStr, '}');
        }
        return $cssObj;
    }
    
    
    function _toStr($cssObj) {
        $cssStr = '';
        foreach ($cssObj as $sel => $rule) {
            $cssStr .= "$sel {\n";
            foreach ($rule as $prop => $value) {
                $cssStr .= "  $prop: $value;\n";
            }
            $cssStr .= "}\n";
        }
        return $cssStr;
    }
}


?>