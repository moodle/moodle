<?php

defined('MOODLE_INTERNAL') || die();

/**
 * Makes our changes to the CSS
 *
 * @param string $css
 * @param theme_config $theme
 * @return string
 */
function formal_white_user_settings($css, $theme) {

    // Set the font reference size
    if (empty($theme->settings->fontsizereference)) {
        $fontsizereference = '13'; // default
    } else {
        $fontsizereference = $theme->settings->fontsizereference;
    }
    $css = formal_white_set_fontsizereference($css, $fontsizereference);

    // Set the page header background color
    if (empty($theme->settings->headerbgc)) {
        $headerbgc = '#E3DFD4'; // default 
    } else {
        $headerbgc = $theme->settings->headerbgc;
    }
    $css = formal_white_set_headerbgc($css, $headerbgc);

    // Set the block content background color
    if (empty($theme->settings->blockcontentbgc)) {
        $blockcontentbgc = '#F6F6F6'; // default
    } else {
        $blockcontentbgc = $theme->settings->blockcontentbgc;
    }
    $css = formal_white_set_blockcontentbgc($css, $blockcontentbgc);

    // Set the left block column background color
    if (empty($theme->settings->lblockcolumnbgc)) {
        $lblockcolumnbgc = '#E3DFD4'; // default
    } else {
        $lblockcolumnbgc = $theme->settings->lblockcolumnbgc;
    }
    $css = formal_white_set_lblockcolumnbgc($css, $lblockcolumnbgc);

    // Set the right block column background color
    if (empty($theme->settings->rblockcolumnbgc)) {
        $rblockcolumnbgc = $lblockcolumnbgc; // default
    } else {
        $rblockcolumnbgc = $theme->settings->rblockcolumnbgc;
    }
    $css = formal_white_set_rblockcolumnbgc($css, $rblockcolumnbgc);

    // set the width of the two blocks columns
    if (!empty($theme->settings->blockcolumnwidth)) {
        $blockcolumnwidth = $theme->settings->blockcolumnwidth;
    } else {
        $blockcolumnwidth = '200'; // default
    }
    $css = formal_white_set_blockcolumnwidth($css, $blockcolumnwidth);

    // set the customcss
    if (!empty($theme->settings->customcss)) {
        $customcss = $theme->settings->customcss;
    } else {
        $customcss = null;
    }
    $css = formal_white_set_customcss($css, $customcss);

    // Return the CSS
    return $css;
}



/**
 * Sets the link color variable in CSS
 *
 */
function formal_white_set_fontsizereference($css, $fontsizereference) {
    $tag = '[[setting:fontsizereference]]';
    $css = str_replace($tag, $fontsizereference.'px', $css);
    return $css;
}

function formal_white_set_headerbgc($css, $headerbgc) {
    $tag = '[[setting:headerbgc]]';
    $css = str_replace($tag, $headerbgc, $css);
    return $css;
}

function formal_white_set_blockcontentbgc($css, $blockcontentbgc) {
    $tag = '[[setting:blockcontentbgc]]';
    $css = str_replace($tag, $blockcontentbgc, $css);
    return $css;
}

function formal_white_set_lblockcolumnbgc($css, $lblockcolumnbgc) {
    $tag = '[[setting:lblockcolumnbgc]]';
    $css = str_replace($tag, $lblockcolumnbgc, $css);
    return $css;
}

function formal_white_set_rblockcolumnbgc($css, $rblockcolumnbgc) {
    $tag = '[[setting:rblockcolumnbgc]]';
    $css = str_replace($tag, $rblockcolumnbgc, $css);
    return $css;
}

function formal_white_set_blockcolumnwidth($css, $blockcolumnwidth) {
    $tag = '[[setting:blockcolumnwidth]]';
    $css = str_replace($tag, $blockcolumnwidth.'px', $css);

    $tag = '[[setting:minusdoubleblockcolumnwidth]]';
    $css = str_replace($tag, (-2*$blockcolumnwidth).'px', $css);

    $tag = '[[setting:doubleblockcolumnwidth]]';
    $css = str_replace($tag, (2*$blockcolumnwidth).'px', $css);

    return $css;
}

function formal_white_set_customcss($css, $customcss) {
    $tag = '[[setting:customcss]]';
    $css = str_replace($tag, $customcss, $css);
    return $css;
}

