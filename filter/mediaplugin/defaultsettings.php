<?php
    // check/set default config settings for media plugin
    // $forcereset is set in calling routine
    
    if (!isset($forcereset)) {
        $forcereset = false;
    }
    
    if (!isset($CFG->filter_mediaplugin_enable_mp3) or $forcereset) {
        if (isset($CFG->filter_mediaplugin_ignore_mp3)) {
            set_config( 'filter_mediaplugin_enable_mp3', !$CFG->filter_mediaplugin_ignore_mp3 );
            set_config( 'filter_mediaplugin_ignore_mp3', '' );
        }
        else {
            set_config( 'filter_mediaplugin_enable_mp3', 1 );
        }
    }
    if (!isset($CFG->filter_mediaplugin_enable_swf) or $forcereset) {
        if (isset($CFG->filter_mediaplugin_ignore_swf)) {
            set_config( 'filter_mediaplugin_enable_swf', !$CFG->filter_mediaplugin_ignore_swf );
            set_config( 'filter_mediaplugin_ignore_swf','' );
        }
        else {
            set_config( 'filter_mediaplugin_enable_swf', 0 ); //disable swf embedding by default for now
        }
    }
    if (!isset($CFG->filter_mediaplugin_enable_mov) or $forcereset) {
        if (isset($CFG->filter_mediaplugin_ignore_mov)) {
            set_config( 'filter_mediaplugin_enable_mov', !$CFG->filter_mediaplugin_ignore_mov );
            set_config( 'filter_mediaplugin_ignore_mov', '' );
        }
        else {
            set_config( 'filter_mediaplugin_enable_mov', 1 );
        }
    }
    if (!isset($CFG->filter_mediaplugin_enable_mpg) or $forcereset) {
        if (isset($CFG->filter_mediaplugin_ignore_mpg)) {
            set_config( 'filter_mediaplugin_enable_mpg', !$CFG->filter_mediaplugin_ignore_mpg );
            set_config( 'filter_mediaplugin_ignore_mpg', '' );
        }
        else {
            set_config( 'filter_mediaplugin_enable_mpg', 1 );
        }
    }
    if (!isset($CFG->filter_mediaplugin_enable_wmv) or $forcereset) {
        if (isset($CFG->filter_mediaplugin_ignore_wmv)) {
            set_config( 'filter_mediaplugin_enable_wmv', !$CFG->filter_mediaplugin_ignore_wmv );
            set_config( 'filter_mediaplugin_ignore_wmv', '' );
        }
        else {
            set_config( 'filter_mediaplugin_enable_wmv', 1 );
        }
    }
    if (!isset($CFG->filter_mediaplugin_enable_avi) or $forcereset) {
        if (isset($CFG->filter_mediaplugin_ignore_avi)) {
            set_config( 'filter_mediaplugin_enable_avi', !$CFG->filter_mediaplugin_ignore_avi );
            set_config( 'filter_mediaplugin_ignore_avi', '' );
        }
        else {
            set_config( 'filter_mediaplugin_enable_avi', 1 );
        }
    }
    if (!isset($CFG->filter_mediaplugin_enable_flv) or $forcereset) {
        if (isset($CFG->filter_mediaplugin_ignore_flv)) {
            set_config( 'filter_mediaplugin_enable_flv', !$CFG->filter_mediaplugin_ignore_flv );
            set_config( 'filter_mediaplugin_ignore_flv','' );
        }
        else {
            set_config( 'filter_mediaplugin_enable_flv', 1 );
        }
    }
    if (!isset($CFG->filter_mediaplugin_enable_ram) or $forcereset) {
        if (isset($CFG->filter_mediaplugin_ignore_ram)) {
            set_config( 'filter_mediaplugin_enable_ram', !$CFG->filter_mediaplugin_ignore_ram );
            set_config( 'filter_mediaplugin_ignore_ram','' );
        }
        else {
            set_config( 'filter_mediaplugin_enable_ram', 1 );
        }
    }
    if (!isset($CFG->filter_mediaplugin_enable_rpm) or $forcereset) {
        if (isset($CFG->filter_mediaplugin_ignore_rpm)) {
            set_config( 'filter_mediaplugin_enable_rpm', !$CFG->filter_mediaplugin_ignore_rpm );
            set_config( 'filter_mediaplugin_ignore_rpm','' );
        }
        else {
            set_config( 'filter_mediaplugin_enable_rpm', 1 );
        }
    }
    if (!isset($CFG->filter_mediaplugin_enable_rm) or $forcereset) {
        if (isset($CFG->filter_mediaplugin_ignore_rm)) {
            set_config( 'filter_mediaplugin_enable_rm', !$CFG->filter_mediaplugin_ignore_rm );
            set_config( 'filter_mediaplugin_ignore_rm','' );
        }
        else {
            set_config( 'filter_mediaplugin_enable_rm', 1 );
        }
    }
?>
