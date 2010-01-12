<?php

////////////////////////////////////////////////////////////////////////////////
/// This file contains a few configuration variables that control
/// how Moodle uses this theme.
////////////////////////////////////////////////////////////////////////////////

$THEME->name = 'anomaly';

$THEME->sheets = array('styles', 'styles_layout', 'styles_select');
/// This variable is an array containing the names of all the
/// stylesheet files you want included in this theme, and in what order
////////////////////////////////////////////////////////////////////////////////

$THEME->parents = array('standardold');  // TODO: new themes can not be based on standardold, instead use 'base' as the base
/// This variable can be set to the name of a parent theme
/// which you want to have included before the current theme.
/// This can make it easy to make modifications to another
/// theme without having to actually change the files
/// If this variable is empty or false then a parent theme
/// is not used.
////////////////////////////////////////////////////////////////////////////////

$THEME->parents_exclude_sheets = array('standardold'=>array('styles_moz'));

$THEME->resource_mp3player_colors =
 'bgColour=000000&btnColour=ffffff&btnBorderColour=cccccc&iconColour=000000&'.
 'iconOverColour=00cc00&trackColour=cccccc&handleColour=ffffff&loaderColour=ffffff&'.
 'font=Arial&fontColour=3333FF&buffer=10&waitForPlay=no&autoPlay=yes';
/// With this you can control the colours of the "big" MP3 player
/// that is used for MP3 resources.


$THEME->filter_mediaplugin_colors =
 'bgColour=000000&btnColour=ffffff&btnBorderColour=cccccc&iconColour=000000&'.
 'iconOverColour=00cc00&trackColour=cccccc&handleColour=ffffff&loaderColour=ffffff&'.
 'waitForPlay=yes';
/// ...And this controls the small embedded player

$THEME->editor_sheets = array('styles_tinymce');

$THEME->layouts = array(
    // Most pages - if we encounter an unknown or a missing page type, this one is used.
    'normal' => array(
        'theme' => 'anomaly',
        'file' => 'general.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-post'
    ),
    // Course page
    'course' => array(
        'theme' => 'anomaly',
        'file' => 'general.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-post'
    ),
    // The site home page.
    'home' => array(
        'theme' => 'anomaly',
        'file' => 'home.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-post'
    ),
    // Server administration scripts.
    'admin' => array(
        'theme' => 'anomaly',
        'file' => 'general.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre'
    ),
    // My moodle page
    'my' => array(
        'theme' => 'anomaly',
        'file' => 'general.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-post'
    ),

    // Settings form pages, like course of module settings.
    'form' => array(
        'theme' => 'anomaly',
        'file' => 'general.php',
        'regions' => array(),
    ),
    // Pages that appear in pop-up windows - no navigation, no blocks, no header.
    'popup' => array(
        'theme' => 'anomaly',
        'file' => 'general.php',
        'regions' => array(),
        'options' => array('nofooter'=>true, 'nonavbar'=>true, 'noblocks'=>true),
    ),
    // No blocks and minimal footer - used for legacy frame layouts only!
    'frametop' => array(
        'theme' => 'anomaly',
        'file' => 'general.php',
        'regions' => array(),
        'options' => array('nofooter', 'noblocks'=>true),
    ),
    // Embeded pages, like iframe embeded in moodleform
    'embedded' => array(
        'theme' => 'anomaly',
        'file' => 'general.php',
        'regions' => array(),
        'options' => array('nofooter'=>true, 'nonavbar'=>true, 'noblocks'=>true),
    ),
    // Used during upgrade and install, and for the 'This site is undergoing maintenance' message.
    // This must not have any blocks, and it is good idea if it does not have links to
    // other places - for example there should not be a home link in the footer...
    'maintenance' => array(
        'theme' => 'anomaly',
        'file' => 'general.php',
        'regions' => array(),
        'options' => array('nofooter'=>true, 'nonavbar'=>true, 'noblocks'=>true),
    ),
);