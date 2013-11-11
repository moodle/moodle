<?php

/**
 * Makes our changes to the CSS
 *
 * @param string $css
 * @param theme_config $theme
 * @return string 
 */
function magazine_process_css($css, $theme) {

    // Set the link color
    if (!empty($theme->settings->linkcolor)) {
        $linkcolor = $theme->settings->linkcolor;
    } else {
        $linkcolor = null;
    }
    $css = magazine_set_linkcolor($css, $linkcolor);

    // Set the link hover color
    if (!empty($theme->settings->linkhover)) {
        $linkhover = $theme->settings->linkhover;
    } else {
        $linkhover = null;
    }
    $css = magazine_set_linkhover($css, $linkhover);
    
    // Set the main color
    if (!empty($theme->settings->maincolor)) {
        $maincolor = $theme->settings->maincolor;
    } else {
        $maincolor = null;
    }
    $css = magazine_set_maincolor($css, $maincolor);
    
    // Set the main accent color
    if (!empty($theme->settings->maincoloraccent)) {
        $maincoloraccent = $theme->settings->maincoloraccent;
    } else {
        $maincoloraccent = null;
    }
    $css = magazine_set_maincoloraccent($css, $maincoloraccent);
   
   // Set the main headings color
    if (!empty($theme->settings->headingcolor)) {
        $headingcolor = $theme->settings->headingcolor;
    } else {
        $headingcolor = null;
    }
    $css = magazine_set_headingcolor($css, $headingcolor);
    
     // Set the block headings color
    if (!empty($theme->settings->blockcolor)) {
        $blockcolor = $theme->settings->blockcolor;
    } else {
        $blockcolor = null;
    }
    $css = magazine_set_blockcolor($css, $blockcolor);
    
    // Set the forum background color
    if (!empty($theme->settings->forumback)) {
        $forumback = $theme->settings->forumback;
    } else {
        $forumback = null;
    }
    $css = magazine_set_forumback($css, $forumback);
    
     // Set the body background image
    if (!empty($theme->settings->background)) {
        $background = $theme->settings->background;
    } else {
        $background = null;
    }
    $css = magazine_set_background($css, $background, $theme);
    
     // Set the logo image
    if (!empty($theme->settings->logo)) {
        $logo = $theme->settings->logo;
    } else {
        $logo = null;
    }
    $css = magazine_set_logo($css, $logo, $theme);
    

    // Return the CSS
    return $css;
}



/**
 * Sets the link color variable in CSS
 *
 */
function magazine_set_linkcolor($css, $linkcolor) {
    $tag = '[[setting:linkcolor]]';
    $replacement = $linkcolor;
    if (is_null($replacement)) {
        $replacement = '#32529a';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function magazine_set_linkhover($css, $linkhover) {
    $tag = '[[setting:linkhover]]';
    $replacement = $linkhover;
    if (is_null($replacement)) {
        $replacement = '#4e2300';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function magazine_set_maincolor($css, $maincolor) {
    $tag = '[[setting:maincolor]]';
    $replacement = $maincolor;
    if (is_null($replacement)) {
        $replacement = '#002f2f';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function magazine_set_maincoloraccent($css, $maincoloraccent) {
    $tag = '[[setting:maincoloraccent]]';
    $replacement = $maincoloraccent;
    if (is_null($replacement)) {
        $replacement = '#092323';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function magazine_set_headingcolor($css, $headingcolor) {
    $tag = '[[setting:headingcolor]]';
    $replacement = $headingcolor;
    if (is_null($replacement)) {
        $replacement = '#4e0000';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function magazine_set_blockcolor($css, $blockcolor) {
    $tag = '[[setting:blockcolor]]';
    $replacement = $blockcolor;
    if (is_null($replacement)) {
        $replacement = '#002f2f';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function magazine_set_forumback($css, $forumback) {
    $tag = '[[setting:forumback]]';
    $replacement = $forumback;
    if (is_null($replacement)) {
        $replacement = '#e6e2af';
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function magazine_set_background($css, $background, $theme = null) {
    global $OUTPUT;
    if ($theme === null) {
        $theme = $OUTPUT;
    }
    $tag = '[[setting:background]]';
    $replacement = $background;
    if (is_null($replacement)) {
        $replacement = $theme->pix_url('bg4', 'theme');
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function magazine_set_logo($css, $logo, $theme = null) {
    global $OUTPUT;
    if ($theme === null) {
        $theme = $OUTPUT;
    }
    $tag = '[[setting:logo]]';
    $replacement = $logo;
    if (is_null($replacement)) {
        $replacement = $theme->pix_url('logo', 'theme');
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}
