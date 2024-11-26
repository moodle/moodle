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
 * Config
 *
 * @package    theme_adaptable
 * @copyright  2015-2019 Jeremy Hopkins (Coventry University)
 * @copyright  2015-2019 Fernando Acedo (3-bits.com)
 * @copyright  2017-2019 Manoj Solanki (Coventry University)
 * @copyright  2019 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die();

global $PAGE;

// The plugin internal name.
$THEME->name = 'adaptable';

// The frontpage regions.
$frontlayoutregions = [
    'side-post',
    'frnt-footer',
    'frnt-market-a',
    'frnt-market-b',
    'frnt-market-c',
    'frnt-market-d',
    'frnt-market-e',
    'frnt-market-f',
    'frnt-market-g',
    'frnt-market-h',
    'frnt-market-i',
    'frnt-market-j',
    'frnt-market-k',
    'frnt-market-l',
    'frnt-market-m',
    'frnt-market-n',
    'frnt-market-o',
    'frnt-market-p',
    'frnt-market-q',
    'frnt-market-r',
    'frnt-market-s',
    'frnt-market-t',
    'information',
    'news-slider-a',
    'course-tab-one-a',
    'course-tab-two-a',
    'my-tab-one-a',
    'my-tab-two-a',
    'course-section-a',
];

// The course page regions.
$courselayoutregions = [
    'side-post',
    'frnt-footer',
    'course-top-a',
    'course-top-b',
    'course-top-c',
    'course-top-d',
    'news-slider-a',
    'course-tab-one-a',
    'course-tab-two-a',
    'my-tab-one-a',
    'my-tab-two-a',
    'course-bottom-a',
    'course-bottom-b',
    'course-bottom-c',
    'course-bottom-d',
    'course-section-a',
];

$standardregions = ['side-post'];

// The theme HTML DOCTYPE.
$THEME->doctype = 'html5';

// Theme parent.
$THEME->parents = ['boost'];

// Styles.
$THEME->sheets = [
    'custom',
];

$THEME->supportscssoptimisation = false;
$THEME->yuicssmodules = [];
$THEME->editor_sheets = [];

$THEME->plugins_exclude_sheets = [
    'block' => [
        'html',
    ],
];

// Disabling block docking.
$THEME->enable_dock = false;

// Call the renderer.
$THEME->rendererfactory = 'theme_overridden_renderer_factory';

// Load the theme layouts.
$THEME->layouts = [
    // Most backwards compatible layout without the blocks - this is the layout used by default.
    'base' => [
        'file' => 'columns2.php',
        'regions' => [],
    ],
    // Standard layout with blocks, this is recommended for most pages with general information.
    'standard' => [
        'file' => 'columns2.php',
        'regions' => $standardregions,
        'defaultregion' => 'side-post',
    ],
    // Main course page.
    'course' => [
        'file' => 'course.php',
        'regions' => $courselayoutregions,
        'defaultregion' => 'side-post',
        'options' => ['langmenu' => true],
    ],
    'coursecategory' => [
        'file' => 'columns2.php',
        'regions' => $standardregions,
        'defaultregion' => 'side-post',
    ],
    // Part of course, typical for modules - default page layout if $cm specified in require_login().
    'incourse' => [
        'file' => 'columns2.php',
        'regions' => array_merge($standardregions, ['course-section-a']),
        'defaultregion' => 'side-post',
    ],
    // The site home page.
    'frontpage' => [
        'file' => 'frontpage.php',
        'regions' => $frontlayoutregions,
        'defaultregion' => 'side-post',
    ],
    // Server administration scripts.
    'admin' => [
        'file' => 'columns2.php',
        'regions' => $standardregions,
        'defaultregion' => 'side-post',

    ],
    // My courses page.
    'mycourses' => [
        'file' => 'columns2.php',
        'regions' => $standardregions,
        'defaultregion' => 'side-post',
        'options' => ['langmenu' => true],
    ],
    // My dashboard page.
    'mydashboard' => [
        'file' => 'dashboard.php',
        'regions' => array_merge($frontlayoutregions, ['content']),
        'defaultregion' => 'side-post',
        'options' => ['langmenu' => true],
    ],
    // My public page.
    'mypublic' => [
        'file' => 'columns2.php',
        'regions' => $standardregions,
        'defaultregion' => 'side-post',
    ],
    // Login page.
    'login' => [
        'file' => 'login.php',
        'regions' => [],
        'options' => ['langmenu' => true, 'nonavbar' => true],
    ],
    // Pages that appear in pop-up windows - no navigation, no blocks, no header.
    'popup' => [
        'file' => 'columns1.php',
        'regions' => [],
        'options' => ['nofooter' => true, 'nonavbar' => true],
    ],
    // No blocks and minimal footer - used for legacy frame layouts only!
    'frametop' => [
        'file' => 'columns1.php',
        'regions' => [],
        'options' => ['nofooter' => true, 'nocoursefooter' => true],
    ],
    // Embeded pages, like iframe/object embeded in moodleform - it needs as much space as possible.
    'embedded' => [
        'file' => 'embedded.php',
        'regions' => [],
    ],
    /* Used during upgrade and install, and for the 'This site is undergoing maintenance' message.
       This must not have any blocks, and it is good idea if it does not have links to
       other places - for example there should not be a home link in the footer... */
    'maintenance' => [
        'file' => 'maintenance.php',
        'regions' => [],
        'options' => ['nofooter' => true, 'nonavbar' => true, 'nocoursefooter' => true, 'nocourseheader' => true],
    ],
    // Should display the content and basic headers only.
    'print' => [
        'file' => 'columns1.php',
        'regions' => [],
        'options' => ['nofooter' => true, 'nonavbar' => false],
    ],
    // The pagelayout used when a redirection is occuring.
    'redirect' => [
        'file' => 'embedded.php',
        'regions' => [],
    ],
    // The pagelayout used for reports.
    'report' => [
        'file' => 'columns2.php',
        'regions' => $standardregions,
        'defaultregion' => 'side-post',
    ],
    // The pagelayout used for safebrowser and securewindow.
    'secure' => [
        'file' => 'secure.php',
        'regions' => array_merge($standardregions, ['course-section-a']),
        'options' => ['nofooter' => true, 'nonavbar' => true],
        'defaultregion' => 'side-post',
    ],
];

// Select the opposite sidebar when switch to RTL.
$THEME->blockrtlmanipulations = [
    'side-pre' => 'side-post',
    'side-post' => 'side-pre',
];

$THEME->prescsscallback = 'theme_adaptable_pre_scss';
$THEME->scss = function (theme_config $theme) {
    return theme_adaptable_get_main_scss_content($theme);
};

$THEME->csspostprocess = 'theme_adaptable_process_customcss';
$THEME->haseditswitch = false;
$THEME->usescourseindex = true;
$THEME->iconsystem = '\\theme_adaptable\\output\\icon_system_fontawesome';
$THEME->addblockposition = BLOCK_ADDBLOCK_POSITION_FLATNAV;
