<?php

function sky_high_process_css($css, $theme) {
 
    if (!empty($theme->settings->regionwidth)) {
        $regionwidth = $theme->settings->regionwidth;
    } else {
        $regionwidth = null;
    }
    $css = sky_high_set_regionwidth($css, $regionwidth);
 
    if (!empty($theme->settings->customcss)) {
        $customcss = $theme->settings->customcss;
    } else {
        $customcss = null;
    }
    $css = sky_high_set_customcss($css, $customcss);
 
    return $css;
}

/**
 * Sets the region width variable in CSS
 *
 * @param string $css
 * @param mixed $regionwidth
 * @return string
 */
function sky_high_set_regionwidth($css, $regionwidth) {
    $tag = '[[setting:regionwidth]]';
    $doubletag = '[[setting:regionwidthdouble]]';
    $leftmargintag = '[[setting:leftregionwidthmargin]]';
    $rightmargintag = '[[setting:rightregionwidthmargin]]';
    $replacement = $regionwidth;
    if (is_null($replacement)) {
        $replacement = 240;
    }
    $css = str_replace($tag, $replacement.'px', $css);
    $css = str_replace($doubletag, ($replacement*2).'px', $css);
    $css = str_replace($rightmargintag, ($replacement*3-5).'px', $css);
    $css = str_replace($leftmargintag, ($replacement+5).'px', $css);
    return $css;
}

/**
 * Sets the custom css variable in CSS
 *
 * @param string $css
 * @param mixed $customcss
 * @return string
 */
function sky_high_set_customcss($css, $customcss) {
    $tag = '[[setting:customcss]]';
    $replacement = $customcss;
    if (is_null($replacement)) {
        $replacement = '';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

?>