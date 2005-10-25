<?php
    // check/set default config settings for media plugin
    
    if (!isset($CFG->filter_mediaplugin_enable_mp3)) {
        if (isset($CFG->filter_mediaplugin_ignore_mp3)) {
            set_config( 'filter_mediaplugin_enable_mp3', !$CFG->filter_mediaplugin_ignore_mp3 );
        }
        else {
            set_config( 'filter_mediaplugin_enable_mp3', 1 );
        }
    }
    if (!isset($CFG->filter_mediaplugin_enable_swf)) {
        if (isset($CFG->filter_mediaplugin_ignore_swf)) {
            set_config( 'filter_mediaplugin_enable_swf', !$CFG->filter_mediaplugin_ignore_swf );
        }
        else {
            set_config( 'filter_mediaplugin_enable_swf', 1 );
        }
    }
    if (!isset($CFG->filter_mediaplugin_enable_mov)) {
        if (isset($CFG->filter_mediaplugin_ignore_mov)) {
            set_config( 'filter_mediaplugin_enable_mov', !$CFG->filter_mediaplugin_ignore_mov );
        }
        else {
            set_config( 'filter_mediaplugin_enable_mov', 1 );
        }
    }
    if (!isset($CFG->filter_mediaplugin_enable_mpg)) {
        if (isset($CFG->filter_mediaplugin_ignore_mpg)) {
            set_config( 'filter_mediaplugin_enable_mpg', !$CFG->filter_mediaplugin_ignore_mpg );
        }
        else {
            set_config( 'filter_mediaplugin_enable_mpg', 1 );
        }
    }
    if (!isset($CFG->filter_mediaplugin_enable_wmv)) {
        if (isset($CFG->filter_mediaplugin_ignore_wmv)) {
            set_config( 'filter_mediaplugin_enable_wmv', !$CFG->filter_mediaplugin_ignore_wmv );
        }
        else {
            set_config( 'filter_mediaplugin_enable_wmv', 1 );
        }
    }
    if (!isset($CFG->filter_mediaplugin_enable_avi)) {
        if (isset($CFG->filter_mediaplugin_ignore_avi)) {
            set_config( 'filter_mediaplugin_enable_avi', !$CFG->filter_mediaplugin_ignore_avi );
        }
        else {
            set_config( 'filter_mediaplugin_enable_avi', 1 );
        }
    }
    if (!isset($CFG->filter_mediaplugin_enable_flv)) {
        if (isset($CFG->filter_mediaplugin_ignore_flv)) {
            set_config( 'filter_mediaplugin_enable_flv', !$CFG->filter_mediaplugin_ignore_flv );
        }
        else {
            set_config( 'filter_mediaplugin_enable_flv', 1 );
        }
    }
?>
