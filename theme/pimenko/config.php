<?php
// This file is part of the Pimenko theme for Moodle
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
 * Theme Pimenko config file.
 *
 * @package    theme_pimenko
 * @copyright  Pimenko 2020
 * @author     Sylvain Revenu - Pimenko 2020 <contact@pimenko.com> <pimenko.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This line protects the file from being accessed by a URL directly.
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/theme/pimenko/lib.php');

// Var $THEME is defined before this page is included and we can define settings by adding properties to this global object.

// The first setting we need is the name of the theme. This should be the last part of the component name, and the same
// as the directory name for our theme.
$THEME->name = 'pimenko';

// This setting list the style sheets we want to include in our theme. Because we want to use SCSS instead of CSS - we won't
// list any style sheets. If we did we would list the name of a file in the /style/ folder for our theme without any css file
// extensions.
$THEME->sheets = [];

// This is a setting that can be used to provide some styling to the content in the TinyMCE text editor. This is no longer the
// default text editor and "Atto" does not need this setting so we won't provide anything. If we did it would work the same
// as the previous setting - listing a file in the /styles/ folder.
$THEME->editor_sheets = [];

// This is a critical setting. We want to inherit from theme_boost because it provides a great starting point for SCSS bootstrap4
// themes. We could add more than one parent here to inherit from multiple parents, and if we did they would be processed in
// order of importance (later themes overriding earlier ones). Things we will inherit from the parent theme include
// styles and mustache templates and some (not all) settings.
$THEME->parents = ['boost'];

// A dock is a way to take blocks out of the page and put them in a persistent floating area on the side of the page. Boost
// does not support a dock so we won't either - but look at bootstrapbase for an example of a theme with a dock.
$THEME->enable_dock = false;

// This is an old setting used to load specific CSS for some YUI JS. We don't need it in Boost based themes because Boost
// provides default styling for the YUI modules that we use. It is not recommended to use this setting anymore.
$THEME->yuicssmodules = array();

// Most themes will use this rendererfactory as this is the one that allows the theme to override any other renderer.
$THEME->rendererfactory = 'theme_overridden_renderer_factory';

// This is a list of blocks that are required to exist on all pages for this theme to function correctly. For example
// bootstrap base requires the settings and navigation blocks because otherwise there would be no way to navigate to all the
// pages in Moodle. Boost does not require these blocks because it provides other ways to navigate built into the theme.
$THEME->requiredblocks = '';

// This is a feature that tells the blocks library not to use the "Add a block" block.
// We don't want this in boost based themes because
// it forces a block region into the page when editing is enabled and it takes up too much room.
$THEME->addblockposition = BLOCK_ADDBLOCK_POSITION_FLATNAV;

// This is the function that returns the SCSS source for the main file in our theme. We override the boost version because
// we want to allow presets uploaded to our own theme file area to be selected in the preset list.
$THEME->scss = function($theme) {
    return theme_pimenko_get_main_scss_content($theme);
};

// This function can make alterations and replace patterns within the CSS.
$THEME->csspostprocess = 'theme_pimenko_process_css';

$THEME->iconsystem = \core\output\icon_system::FONTAWESOME;

$THEME->activityheaderconfig = [
    'notitle' => true
];

// Define here layout we override.
// Login page.
$THEME->layouts = [
    // Most backwards compatible layout without the blocks.
    'base' => array(
        'file' => 'columns2.php',
        'regions' => array(),
    ),
    // Standard layout with blocks.
    'standard' => array(
        'file' => 'columns2.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre',
    ),
    // Main course page.
    'course' => array(
        'file' => 'columns2.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre',
        'options' => array('langmenu' => true),
    ),
    // My dashboard page.
    'mydashboard' => array(
        'file' => 'columns2.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre',
        'options' => array('nonavbar' => true, 'langmenu' => true),
    ),
    'coursecategory' => array(
        'file' => 'columns2.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre',
        'options' => array('langmenu' => true),
    ),
    // My courses page.
    'mycourses' => array(
        'file' => 'columns2.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
        'options' => array('nonavbar' => true),
    ),
    // Server administration scripts.
    'admin' => array(
        'file' => 'columns2.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre',
    ),
    'incourse' => array(
        'file' => 'columns2.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre',
        'options' => array('langmenu' => true),
    ),
    // The site home page.
    'frontpage' => [
        'file' => 'frontpage.php',
        'defaultregion' => 'side-pre',
        'regions' => theme_pimenko_regions(),
    ],
    'login' => array(
        'file' => 'login.php',
        'regions' => array(),
        'options' => array('langmenu' => true),
    ),
    // My public page.
    'mypublic' => array(
        'file' => 'columns2.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre',
    ),
    'pimenkoProfile' => array(
        'file' => 'columns2.php',
        'regions' => [
            'side-pre',
            'side-post'
        ],
        'defaultregion' => 'side-pre',
    ),
    // The pagelayout used for reports.
    'report' => array(
        'file' => 'columns2.php',
        'regions' => array('side-pre'),
        'defaultregion' => 'side-pre',
    ),
    'popup' => array(
        'file' => 'columns2.php',
        'regions' => array(),
        'options' => array(
            'nofooter' => true,
            'nonavbar' => true,
            'activityheader' => [
                'notitle' => true,
                'nocompletion' => true,
                'nodescription' => true
            ]
        )
    ),
];

$THEME->haseditswitch = true;
$THEME->usescourseindex = true;

$THEME->extrascsscallback = 'theme_pimenko_get_extra_scss';
