<?php

/******************************************************************************
 Plug in constants/variables 
 ******************************************************************************/
function replace_cssconstants($css) {
    if (preg_match_all("/@server\s+(?:variables|constants)\s*\{\s*([^\}]+)\s*\}\s*/i",$css,$matches)) {
        $variables  = array();
        foreach ($matches[0] as $key=>$server) {
            $css = str_replace($server,'',$css);
            preg_match_all("/([^:\}\s]+)\s*:\s*([^;\}]+);/",$matches[1][$key],$vars);
            foreach ($vars[1] as $var=>$value) {
                $variables[$value] = $vars[2][$var];
                }
            }
        $css = str_replace(array_keys($variables),array_values($variables),$css);
        }
    return ($css);
}

// vim:autoindent:expandtab:shiftwidth=4:tabstop=4:tw=140:
?>
