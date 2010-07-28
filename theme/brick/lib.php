<?php
function brick_set_linkcolor($css, $linkcolor) {
    $tag = '[[setting:linkcolor]]';
    $replacement = $linkcolor;
    if (is_null($replacement)) {
        $replacement = '#06365b';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function brick_set_linkhover($css, $linkhover) {
    $tag = '[[setting:linkhover]]';
    $replacement = $linkhover;
    if (is_null($replacement)) {
        $replacement = '#5487ad';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function brick_set_maincolor($css, $maincolor) {
    $tag = '[[setting:maincolor]]';
    $replacement = $maincolor;
    if (is_null($replacement)) {
        $replacement = '#8e2800';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function brick_set_maincolorlink($css, $maincolorlink) {
    $tag = '[[setting:maincolorlink]]';
    $replacement = $maincolorlink;
    if (is_null($replacement)) {
        $replacement = '#fff0a5';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function brick_set_headingcolor($css, $headingcolor) {
    $tag = '[[setting:headingcolor]]';
    $replacement = $headingcolor;
    if (is_null($replacement)) {
        $replacement = '#5c3500';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function brick_set_logo($css, $logo) {
 global $OUTPUT;
 $tag = '[[setting:logo]]';
 $replacement = $logo;
 if (is_null($replacement)) {
 $replacement = $OUTPUT->pix_url('logo', 'theme');
 }
 $css = str_replace($tag, $replacement, $css);
 return $css;
}






function brick_process_css($css, $theme) {
       
     if (!empty($theme->settings->linkcolor)) {
        $linkcolor = $theme->settings->linkcolor;
    } else {
        $linkcolor = null;
    }
    $css = brick_set_linkcolor($css, $linkcolor);

// Set the link hover color
    if (!empty($theme->settings->linkhover)) {
        $linkhover = $theme->settings->linkhover;
    } else {
        $linkhover = null;
    }
    $css = brick_set_linkhover($css, $linkhover);
    
    // Set the main color
    if (!empty($theme->settings->maincolor)) {
        $maincolor = $theme->settings->maincolor;
    } else {
        $maincolor = null;
    }
    $css = brick_set_maincolor($css, $maincolor);
    
      // Set the main accent color
    if (!empty($theme->settings->maincolorlink)) {
        $maincolorlink = $theme->settings->maincolorlink;
    } else {
        $maincolorlink = null;
    }
    $css = brick_set_maincolorlink($css, $maincolorlink);
   
   // Set the main headings color
    if (!empty($theme->settings->headingcolor)) {
        $headingcolor = $theme->settings->headingcolor;
    } else {
        $headingcolor = null;
    }
    $css = brick_set_headingcolor($css, $headingcolor);
    
     // Set the logo image
    if (!empty($theme->settings->logo)) {
        $logo = $theme->settings->logo;
    } else {
        $logo = null;
    }
    $css = brick_set_logo($css, $logo);
    
    
    
    return $css;
    
    
    
}
