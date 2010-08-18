<?php
function overlay_set_linkcolor($css, $linkcolor) {
    $tag = '[[setting:linkcolor]]';
    $replacement = $linkcolor;
    if (is_null($replacement)) {
        $replacement = '#428ab5';
    }
    $css = str_replace($tag, $replacement, $css);
    

    return $css;
}

function overlay_set_headercolor($css, $headercolor) {
    $tag = '[[setting:headercolor]]';
    $replacement = $headercolor;
    if (is_null($replacement)) {
        $replacement = '#2a4c7b';
    }
    $css = str_replace($tag, $replacement, $css);
    

    return $css;
}


function overlay_set_customcss($css, $customcss) {
    $tag = '[[setting:customcss]]';
    $replacement = $customcss;
    if (is_null($replacement)) {
        $replacement = '';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}





function overlay_process_css($css, $theme) {
       
     if (!empty($theme->settings->linkcolor)) {
        $linkcolor = $theme->settings->linkcolor;
    } else {
        $linkcolor = null;
    }
    $css = overlay_set_linkcolor($css, $linkcolor);
	
	 if (!empty($theme->settings->headercolor)) {
        $headercolor = $theme->settings->headercolor;
    } else {
        $headercolor = null;
    }
    $css = overlay_set_headercolor($css, $headercolor);
	
     if (!empty($theme->settings->customcss)) {
        $customcss = $theme->settings->customcss;
    } else {
        $customcss = null;
    }
    $css = overlay_set_customcss($css, $customcss);
    
    return $css;
    
    
    
}

