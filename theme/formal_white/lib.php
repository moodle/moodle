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

    // Set the page header background color
    if (empty($theme->settings->headerbgc)) {
        $headerbgc = '#E3DFD4'; // default (o forse è meglio #FEF9F6?)
    } else {
        $headerbgc = $theme->settings->headerbgc;
    }
    $css = formal_white_set_headerbgc($css, $headerbgc);

    // Set the block content background color
    if (empty($theme->settings->blockcontentbgc)) {
        $blockcontentbgc = '#F6F6F6'; // default (o forse è meglio #FEF9F6?)
    } else {
        $blockcontentbgc = $theme->settings->blockcontentbgc;
    }
    $css = formal_white_set_blockcontentbgc($css, $blockcontentbgc);

    // Set the block column background color
    if (empty($theme->settings->blockcolumnbgc)) {
        $blockcolumnbgc = '#E3DFD4'; // default
    } else {
        $blockcolumnbgc = $theme->settings->blockcolumnbgc;
    }
    $css = formal_white_set_blockcolumnbgc($css, $blockcolumnbgc);

    // Set the logo image
    if (!empty($theme->settings->logo)) {
        $logo = $theme->settings->logo;
    } else {
        $logo = null;
    }
    $css = formal_white_set_logo($css, $logo);

    // set the width of the two blocks colums
    if (!empty($theme->settings->blockcolumnwidth)) {
        $blockcolumnwidth = $theme->settings->blockcolumnwidth;
    } else {
        $blockcolumnwidth = '200'; // default
    }
    $css = formal_white_set_blockcolumnwidth($css, $blockcolumnwidth);

    // Return the CSS
    return $css;
}



/**
 * Sets the link color variable in CSS
 *
 */
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

function formal_white_set_blockcolumnbgc($css, $blockcolumnbgc) {
    $tag = '[[setting:blockcolumnbgc]]';
    $css = str_replace($tag, $blockcolumnbgc, $css);
    return $css;
}

function formal_white_set_logo($css, $logo) {
    global $OUTPUT;

    $tag = '[[setting:logo]]';
    if (is_null($logo)) {
         $logo = $OUTPUT->pix_url('logo', 'theme');
     }
    $css = str_replace($tag, $logo, $css);
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