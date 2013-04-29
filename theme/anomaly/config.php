<?php
////////////////////////////////////////////////////////////////////////////////
/// This file contains a few configuration variables that control
/// how Moodle uses this theme.
////////////////////////////////////////////////////////////////////////////////

$THEME->name = 'anomaly';

$THEME->sheets = array(
    'base',
    'general',
    'browser',
    'dock',
    'menu',
    'settings'
);
/// This variable is an array containing the names of all the
/// stylesheet files you want included in this theme, and in what order
////////////////////////////////////////////////////////////////////////////////

$THEME->parents = array('base');
/// This variable can be set to the name of a parent theme
/// which you want to have included before the current theme.
/// This can make it easy to make modifications to another
/// theme without having to actually change the files
/// If this variable is empty or false then a parent theme
/// is not used.
////////////////////////////////////////////////////////////////////////////////

$THEME->layouts = array(
    // Most pages - if we encounter an unknown or a missing page type, this one is used.
    'base' => array(
        'file' => 'general.php',
        'regions' => array()
    ),
    'standard' => array(
        'file' => 'general.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-pre',
        'options' => array('langmenu' => true)
    ),
    // Course page
    'course' => array(
        'file' => 'general.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-pre',
        'options' => array('langmenu' => true)
    ),
    // Course page
    'coursecategory' => array(
        'file' => 'general.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-pre',
        'options' => array('langmenu' => true)
    ),
    'incourse' => array(
        'file' => 'general.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-pre',
        'options' => array('langmenu' => true)
    ),
    'frontpage' => array(
        'file' => 'frontpage.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-pre',
        'options' => array('langmenu' => true)
    ),
    'admin' => array(
        'file' => 'general.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre',
        'options' => array('langmenu' => true)
    ),
    'mydashboard' => array(
        'file' => 'general.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-pre',
        'options' => array('langmenu' => true)
    ),
    'mypublic' => array(
        'file' => 'general.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-pre',
        'options' => array('langmenu' => true)
    ),
    'login' => array(
        'file' => 'general.php',
        'regions' => array()
    ),
    // Pages that appear in pop-up windows - no navigation, no blocks, no header.
    'popup' => array(
        'file' => 'general.php',
        'regions' => array(),
        'options' => array('nofooter'=>true, 'nonavbar'=>true, 'noblocks'=>true, 'nocourseheaderfooter'=>true),
    ),
    // No blocks and minimal footer - used for legacy frame layouts only!
    'frametop' => array(
        'file' => 'general.php',
        'regions' => array(),
        'options' => array('nofooter', 'noblocks'=>true, 'nocoursefooter'=>true),
    ),
    // Embeded pages, like iframe embeded in moodleform
    'embedded' => array(
        'file' => 'general.php',
        'regions' => array(),
        'options' => array('nofooter'=>true, 'nonavbar'=>true, 'noblocks'=>true, 'nocourseheaderfooter'=>true),
    ),
    // Used during upgrade and install, and for the 'This site is undergoing maintenance' message.
    // This must not have any blocks, and it is good idea if it does not have links to
    // other places - for example there should not be a home link in the footer...
    'maintenance' => array(
        'file' => 'general.php',
        'regions' => array(),
        'options' => array('nofooter'=>true, 'nonavbar'=>true, 'noblocks'=>true, 'nocourseheaderfooter'=>true),
    ),
    // Should display the content and basic headers only.
    'print' => array(
        'file' => 'general.php',
        'regions' => array(),
        'options' => array('nofooter'=>true, 'nonavbar'=>false, 'noblocks'=>true, 'nocourseheaderfooter'=>true),
    ),
    // The pagelayout used when a redirection is occuring.
    'redirect' => array(
        'file' => 'embedded.php',
        'regions' => array(),
        'options' => array('nofooter'=>true, 'nonavbar'=>true, 'nocustommenu'=>true, 'nocourseheaderfooter'=>true),
    ),
    // The pagelayout used for reports.
    'report' => array(
        'file' => 'report.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre',
        'options' => array('langmenu' => true),
    ),
    // The pagelayout used for safebrowser and securewindow.
    'secure' => array(
        'file' => 'general.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-pre',
        'options' => array('nofooter'=>true, 'nonavbar'=>true, 'nocustommenu'=>true, 'nologinlinks'=>true, 'nocourseheaderfooter'=>true),
    ),
);

$THEME->rendererfactory = 'theme_overridden_renderer_factory';

$THEME->csspostprocess = 'anomaly_process_css';

$THEME->enable_dock = true;

$THEME->editor_sheets = array('editor');
