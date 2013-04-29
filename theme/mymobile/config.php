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
 * Config file for mymobile theme
 *
 * @package    theme_mymobile
 * @copyright  John Stabinger
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// The name of the theme
$THEME->name = 'mymobile';

// This theme relies on canvas and of course base themes
$THEME->parents = array(
    'canvas',
    'base',
);

// Set the stylesheets that we want to include for this theme
$THEME->sheets = array(
    'jmobile11',
    'jmobile11_rtl',
    'core',
    'media'
);

// Exclude parent sheets that we don't want
$THEME->parents_exclude_sheets = array(
    'base' => array(
        'pagelayout',
        'dock',
        'editor',
    ),
    'canvas' => array(
        'pagelayout',
        'tabs',
        'editor',
    ),
);

// Disable the dock - this theme does not support it.
$THEME->enable_dock = false;

// Set up the default layout options. Note that none of these have block
// regions. See the code below this for where and when block regions are added.
$THEME->layouts = array(
    'base' => array(
        'file' => 'general.php',
        'regions' => array(),
    ),
    'standard' => array(
        'file' => 'general.php',
        'regions' => array(),
    ),
    'course' => array(
        'file' => 'general.php',
        'regions' => array(),
    ),
    'coursecategory' => array(
        'file' => 'general.php',
        'regions' => array(),
    ),
    'incourse' => array(
        'file' => 'general.php',
        'regions' => array(),
    ),
    'frontpage' => array(
        'file' => 'general.php',
        'regions' => array(),
    ),
    'admin' => array(
        'file' => 'general.php',
        'regions' => array(),
    ),
    'mydashboard' => array(
        'file' => 'general.php',
        'regions' => array(),
        'options' => array('nonavbar' => true),
    ),
    'mypublic' => array(
        'file' => 'general.php',
        'regions' => array(),
    ),
    'login' => array(
        'file' => 'general.php',
        'regions' => array(),
        'options' => array('langmenu'=>true, 'nonavbar'=>true),
    ),
    'popup' => array(
        'file' => 'embedded.php',
        'regions' => array(),
        'options' => array('nofooter'=>true, 'noblocks'=>true, 'nonavbar'=>true, 'nocourseheaderfooter'=>true),
    ),
    'frametop' => array(
        'file' => 'general.php',
        'regions' => array(),
        'options' => array('nofooter'=>true, 'nocoursefooter'=>true),
    ),
    'maintenance' => array(
        'file' => 'general.php',
        'regions' => array(),
        'options' => array('nofooter'=>true, 'nonavbar'=>true, 'nocourseheaderfooter'=>true),
    ),
    'embedded' => array(
        'file' => 'embedded.php',
        'regions' => array(),
        'options' => array('nofooter'=>true, 'nonavbar'=>true, 'nocourseheaderfooter'=>true),
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
     // The pagelayout used for reports
    'report' => array(
        'file' => 'general.php',
        'regions' => array(),
        'options' => array('nofooter'=>true, 'nonavbar'=>false, 'noblocks'=>true),
    ),
);

// Get whether to show blocks and use appropriate pagelayout
// this is necessary for block JS errors and other block problems
$thisdevice = get_device_type();
if ($thisdevice == "default" || $thisdevice == "tablet" || optional_param('mymobile_blocks', false, PARAM_BOOL)) {
    // These are layouts with blocks
    $blocklayouts = array('course', 'incourse', 'frontpage', 'mydashboard', 'mypublic');
    foreach ($blocklayouts as $layout) {
        $THEME->layouts[$layout]['regions'] = array('myblocks');
        $THEME->layouts[$layout]['defaultregion'] = 'myblocks';
    }
}

// Sets a custom render factory to use with the theme, used when working with custom renderers.
$THEME->rendererfactory = 'theme_overridden_renderer_factory';
$THEME->csspostprocess = 'mymobile_user_settings';

// Disables CSS Optimiser for MyMobile theme.
$THEME->supportscssoptimisation = false;
