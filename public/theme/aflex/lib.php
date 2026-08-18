<?php

defined('MOODLE_INTERNAL') || die();

function theme_aflex_get_main_scss_content($theme) {
    global $CFG;
    return file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
}

function theme_aflex_get_pre_scss($theme) {
    $brandcolor = !empty($theme->settings->brandcolor) ? $theme->settings->brandcolor : '#173f5f';
    $scss = '$primary: ' . $brandcolor . ";\n";
    if (!empty($theme->settings->scsspre)) {
        $scss .= $theme->settings->scsspre . "\n";
    }
    return $scss;
}

function theme_aflex_get_extra_scss($theme) {
    $scss = file_get_contents(__DIR__ . '/scss/aflex.scss');
    $loginbackground = $theme->setting_file_url('loginbackgroundimage', 'loginbackgroundimage');
    $loginbackground = $loginbackground ?: $theme->image_url('login_background', 'theme');
    $scss .= "\nbody.pagelayout-login #page .login-layout-left {"
        . " background-image: linear-gradient(90deg, rgba(11, 39, 61, .68), rgba(11, 39, 61, .12)),"
        . " url('{$loginbackground}'); background-size: cover; background-position: center; }";
    if (!empty($theme->settings->scss)) {
        $scss .= "\n" . $theme->settings->scss;
    }
    return $scss;
}

function theme_aflex_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    $allowedareas = ['logo', 'favicon', 'loginbackgroundimage'];
    if ($context->contextlevel === CONTEXT_SYSTEM && in_array($filearea, $allowedareas, true)) {
        $theme = theme_config::load('aflex');
        $options['cacheability'] = 'public';
        return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
    }
    send_file_not_found();
}
