<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Config for the splash theme
 *
 * @package   theme_splash
 * @copyright 2010 Caroline Kennedy of Synergy Learning
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$THEME->name = 'splash';

////////////////////////////////////////////////////
// Name of the theme.
////////////////////////////////////////////////////


$THEME->parents = array(
    'canvas',
    'base',
);

/////////////////////////////////////////////////////
// List exsisting theme(s) to use as parents.
////////////////////////////////////////////////////


$THEME->sheets = array(
    'sl',
    'green',
    'blue',
    'orange',
    'ie',
);

////////////////////////////////////////////////////
// Name of the stylesheet(s) you are including in
// this new theme's /styles/ directory.
////////////////////////////////////////////////////

$THEME->enable_dock = true;

////////////////////////////////////////////////////
// Do you want to use the new navigation dock?
////////////////////////////////////////////////////


$THEME->layouts = array(
    // Most pages - if we encounter an unknown or a missing page type, this one is used.
    'base' => array(
        'file' => 'general.php',
        'regions' => array()
    ),
    'standard' => array(
        'file' => 'general.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-post'
    ),
    // Course page
    'course' => array(
        'file' => 'general.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-post'
    ),
    // Course page
    'coursecategory' => array(
        'file' => 'general.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-post'
    ),
    'incourse' => array(
        'file' => 'general.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-post'
    ),
    'frontpage' => array(
        'file' => 'general.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-post'
    ),
    'admin' => array(
        'file' => 'general.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre'
    ),
    'mydashboard' => array(
        'file' => 'general.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-post'
    ),
    'mypublic' => array(
        'file' => 'general.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-post'
    ),
    'login' => array(
        'file' => 'general.php',
        'regions' => array()
    ),
    // Pages that appear in pop-up windows - no navigation, no blocks, no header.
    'popup' => array(
        'file' => 'general.php',
        'regions' => array(),
        'options' => array('nofooter'=>true, 'nonavbar'=>true, 'noblocks'=>true),
    ),
    // No blocks and minimal footer - used for legacy frame layouts only!
    'frametop' => array(
        'file' => 'general.php',
        'regions' => array(),
        'options' => array('nofooter', 'noblocks'=>true),
    ),
    // Embeded pages, like iframe embeded in moodleform
    'embedded' => array(
        'theme' => 'canvas',
        'file' => 'embedded.php',
        'regions' => array(),
        'options' => array('nofooter'=>true, 'nonavbar'=>true),
    ),
    // Used during upgrade and install, and for the 'This site is undergoing maintenance' message.
    // This must not have any blocks, and it is good idea if it does not have links to
    // other places - for example there should not be a home link in the footer...
    'maintenance' => array(
        'file' => 'general.php',
        'regions' => array(),
        'options' => array('nofooter'=>true, 'nonavbar'=>true, 'noblocks'=>true),
    ),
    // Should display the content and basic headers only.
    'print' => array(
        'file' => 'general.php',
        'regions' => array(),
        'options' => array('nofooter'=>true, 'nonavbar'=>false, 'noblocks'=>true),
    ),
    'report' => array(
        'file' => 'report.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre'
    ),
);


///////////////////////////////////////////////////////////////
// These are all of the possible layouts in Moodle.
///////////////////////////////////////////////////////////////


$THEME->csspostprocess = 'splash_process_css';



///////////////////////////////////////////////////////////////
// Splash Theme Specific settings for Administrators to customise
// css.
///////////////////////////////////////////////////////////////

$THEME->editor_sheets = array('editor');