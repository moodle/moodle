<?PHP
class hotpot_xml_template_default {
    function read_template($filename, $tag='temporary') {
        // create the file path to the template
        $filepath = $this->parent->template_dirpath.DIRECTORY_SEPARATOR.$filename;
        // try and open the template file
        if (!file_exists($filepath) || !is_readable($filepath)) {
            $msg = 'Could not open the '.$this->parent->template_dir.' template file &quot;'.$filepath.'&quot;';
            error($msg, $this->parent->course_homeurl);
        }
        // read in the template and close the file
        $this->$tag = file_get_contents($filepath);
        // expand the blocks and strings in the template
        $this->expand_blocks($tag);
        $this->expand_strings($tag);
        if ($tag=='temporary') {
            $template = $this->$tag;
            $this->$tag = '';
            return $template;
        }
    }
    function expand_blocks($tag) {
        // get block $names
        //  [1] the full block name (including optional leading 'str' or 'incl')
        //  [2] leading 'incl' or 'str', if any
        //  [3] the real block name ([1] without [2])
        $search = '/\[\/((incl|str)?((?:\w|\.)+))\]/';
        preg_match_all($search, $this->$tag, $names);
        $i_max = count($names[0]);
        for ($i=0; $i<$i_max; $i++) {
            $method = $this->parent->template_dir.'_expand_'.str_replace('.', '', $names[3][$i]);
            if (method_exists($this, $method)) {
                eval('$value=$this->'.$method.'();');
                $search = '/\['.$names[1][$i].'\](.*?)\[\/'.$names[1][$i].'\]/s';
                preg_match_all($search, $this->$tag, $blocks);
                $ii_max = count($blocks[0]);
                for ($ii=0; $ii<$ii_max; $ii++) {
                    $replace = empty($value) ? '' : $blocks[1][$ii];
                    $this->$tag = str_replace($blocks[0][$ii], $replace, $this->$tag);
                }
            } else {
                $msg = 'Template block expand method not found: &quot;'.$method.'&quot;';
                error($msg, $this->parent->course_homeurl);
            }
        }
    }
    function expand_strings($tag, $search='') {
        if (empty($search)) {
            // default $search $pattern
            $search = '/\[(?:bool|int|str)(\\w+)\]/';
        }
        preg_match_all($search, $this->$tag, $matches);
        $i_max = count($matches[0]);
        for ($i=0; $i<$i_max; $i++) {
            $method = $this->parent->template_dir.'_expand_'.$matches[1][$i];
            if (method_exists($this, $method)) {
                eval('$replace=$this->'.$method.'();');
                $this->$tag = str_replace($matches[0][$i], $replace, $this->$tag);
            }
        }
    }
    function bool_value($tags, $more_tags="[0]['#']") {
        $value = $this->parent->xml_value($tags, $more_tags);
        return empty($value) ? 'false' : 'true';
    }
    function int_value($tags, $more_tags="[0]['#']") {
        return intval($this->parent->xml_value($tags, $more_tags));
    }
    function js_value($tags, $more_tags="[0]['#']", $convert_to_unicode=false) {
        return $this->js_safe($this->parent->xml_value($tags, $more_tags), $convert_to_unicode);
    }
    function js_safe($str, $convert_to_unicode=false) {
        // encode a string for javascript
        // decode "<" and ">" - not necesary as it was done by xml_value()
        // $str  = strtr($str, array('&#x003C;' => '<', '&#x003E;' => '>'));
        // escape single quotes and backslashes
        $str = strtr($str, array("'"=>"\\'", '\\'=>'\\\\'));
        // convert newlines (win = "\r\n", mac="\r", linix/unix="\n")
        $nl = '\\n'; // javascript newline
        $str = strtr($str, array("\r\n"=>$nl, "\r"=>$nl, "\n"=>$nl));
        // convert (hex and decimal) html entities to unicode, if required
        if ($convert_to_unicode) {
            $str = preg_replace('/&#x([0-9A-F]+);/i', '\\u\\1', $str);
            $str = preg_replace_callback('/&#(\d+);/', array(&$this, 'js_safe_callback'), $str);
        }
        return $str;
    }
    function js_safe_callback(&$matches) {
        return '\\u'.sprintf('%04X', $matches[1]);
    }
    function get_halfway_color($x, $y) {
        // returns the $color that is half way between $x and $y
        $color = $x; // default
        $rgb = '/^\#?([0-9a-f])([0-9a-f])([0-9a-f])$/i';
        $rrggbb = '/^\#?([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/i';
        if ((
            preg_match($rgb, $x, $x_matches) ||
            preg_match($rrggbb, $x, $x_matches)
        ) && (
            preg_match($rgb, $y, $y_matches) ||
            preg_match($rrggbb, $y, $y_matches)
        )) {
            $color = '#';
            for ($i=1; $i<=3; $i++) {
                $x_dec = hexdec($x_matches[$i]);
                $y_dec = hexdec($y_matches[$i]);
                $color .= sprintf('%02x', min($x_dec, $y_dec) + abs($x_dec-$y_dec)/2);
            }
        }
        return $color;
    }
}
?>
