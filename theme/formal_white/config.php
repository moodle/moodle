<?php

////////////////////////////////////////////////////////////////////////////////
/// This file contains a few configuration variables that control
/// how Moodle uses this theme.
////////////////////////////////////////////////////////////////////////////////

// The name of our theme
$THEME->name = 'formal_white';


$THEME->sheets = array('dock','base','general','fw_corners','formalwhite','settings');
$layoutpage = 'general.php';
/// This variable is an array containing the names of all the
/// stylesheet files you want included in this theme, and in what order
////////////////////////////////////////////////////////////////////////////////

$THEME->parents = array('base');  // TODO: new themes can not be based on standardold, instead use 'base' as the base
/// This variable can be set to the name of a parent theme
/// which you want to have included before the current theme.
/// This can make it easy to make modifications to another
/// theme without having to actually change the files
/// If this variable is empty or false then a parent theme
/// is not used.
////////////////////////////////////////////////////////////////////////////////

$THEME->parents_exclude_sheets = array('base'=>array('styles_moz'));

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
    'base' => array(
        'file' => $layoutpage,
        'regions' => array()
    ),
    'standard' => array(
        'file' => $layoutpage,
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre'
    ),
    // Course page
    'course' => array(
        'file' => $layoutpage,
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-post'
    ),
    // Course page
    'coursecategory' => array(
        'file' => $layoutpage,
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-post'
    ),
    'incourse' => array(
        'file' => $layoutpage,
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-post'
    ),
    'frontpage' => array(
        'file' => $layoutpage,
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-pre'
    ),
    'admin' => array(
        'file' => $layoutpage,
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre'
    ),
    'mydashboard' => array(
        'file' => $layoutpage,
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-post'
    ),
    'mypublic' => array(
        'file' => $layoutpage,
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-post'
    ),
    'login' => array(
        'file' => $layoutpage,
        'regions' => array()
    ),
    // Pages that appear in pop-up windows - no navigation, no blocks, no header.
    'popup' => array(
        'file' => $layoutpage,
        'regions' => array(),
        'options' => array('nofooter'=>true, 'nonavbar'=>true, 'noblocks'=>true),
    ),
    // No blocks and minimal footer - used for legacy frame layouts only!
    'frametop' => array(
        'file' => $layoutpage,
        'regions' => array(),
        'options' => array('nofooter', 'noblocks'=>true),
    ),
    // Embeded pages, like iframe embeded in moodleform
    'embedded' => array(
        'file' => $layoutpage,
        'regions' => array(),
        'options' => array('nofooter'=>true, 'nonavbar'=>true, 'noblocks'=>true),
    ),
    // Used during upgrade and install, and for the 'This site is undergoing maintenance' message.
    // This must not have any blocks, and it is good idea if it does not have links to
    // other places - for example there should not be a home link in the footer...
    'maintenance' => array(
        'file' => $layoutpage,
        'regions' => array(),
        'options' => array('nofooter'=>true, 'nonavbar'=>true, 'noblocks'=>true),
    )
);

$THEME->rendererfactory = 'theme_overridden_renderer_factory';
$THEME->enable_dock = true;
$THEME->javascripts_footer = array('navigation');

/**
 * Sets the function that will replace our settings within the CSS
 */
$THEME->csspostprocess = 'formalwhite_process_css';