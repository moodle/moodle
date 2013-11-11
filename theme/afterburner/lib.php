<?php

function afterburner_process_css($css, $theme) {

    // Set the background image for the logo
    $logo = $theme->setting_file_url('logo', 'logo');
    $css = afterburner_set_logo($css, $logo, $theme);

    // Set custom CSS
    if (!empty($theme->settings->customcss)) {
        $customcss = $theme->settings->customcss;
    } else {
        $customcss = null;
    }
    $css = afterburner_set_customcss($css, $customcss);

    return $css;
}

function afterburner_set_logo($css, $logo, $theme = null) {
    global $OUTPUT;
    if ($theme === null) {
        $theme = $OUTPUT;
    }
    $tag = '[[setting:logo]]';
    $replacement = $logo;
    if (is_null($replacement)) {
        $replacement = $theme->pix_url('images/logo','theme');
    }

    $css = str_replace($tag, $replacement, $css);

    return $css;
}

function afterburner_set_customcss($css, $customcss) {
    $tag = '[[setting:customcss]]';
    $replacement = $customcss;
    if (is_null($replacement)) {
        $replacement = '';
    }

    $css = str_replace($tag, $replacement, $css);

    return $css;
}

function theme_afterburner_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    if ($context->contextlevel == CONTEXT_SYSTEM and $filearea === 'logo') {
        $theme = theme_config::load('afterburner');
        return $theme->setting_file_serve('logo', $args, $forcedownload, $options);
    } else {
        send_file_not_found();
    }
}
