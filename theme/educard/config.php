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
 * educard config.
 * @package   theme_educard
 * @copyright 2022 ThemesAlmond - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/lib.php');

$THEME->name = 'educard';
$THEME->sheets = [];
$THEME->editor_sheets = [];
$THEME->editor_scss = ['editor'];
$THEME->usefallback = true;
$THEME->scss = function($theme) {
    return theme_educard_get_main_scss_content($theme);
};

$THEME->layouts = [
    // Most backwards compatible layout without the blocks.
    'base' => [
        'file' => 'drawers.php',
        'regions' => [],
    ],
    // Standard layout with blocks.
    'standard' => [
        'file' => 'drawers.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
    ],
    // Main course page.
    'course' => [
        'file' => 'drawers.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
        'options' => ['langmenu' => true],
    ],
    'coursecategory' => [
        'file' => 'drawers.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
    ],
    // Part of course, typical for modules - default page layout if $cm specified in require_login().
    'incourse' => [
        'file' => 'drawers.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
    ],
    // The site home page.
    'frontpage' => [
        'file' => 'frontpage.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
        'options' => ['nonavbar' => true],
    ],
    // Server administration scripts.
    'admin' => [
        'file' => 'drawers.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
    ],
    // My courses page.
    'mycourses' => [
        'file' => 'drawers.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
        'options' => ['nonavbar' => true],
    ],
    // My dashboard page.
    'mydashboard' => [
        'file' => 'drawers.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
        'options' => ['nonavbar' => true, 'langmenu' => true],
    ],
    // My public page.
    'mypublic' => [
        'file' => 'drawers.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
    ],
    'login' => [
        'file' => 'login.php',
        'regions' => [],
        'options' => ['langmenu' => true],
    ],

    // Pages that appear in pop-up windows - no navigation, no blocks, no header and bare activity header.
    'popup' => [
        'file' => 'columns1.php',
        'regions' => [],
        'options' => [
            'nofooter' => true,
            'nonavbar' => true,
            'activityheader' => [
                'notitle' => true,
                'nocompletion' => true,
                'nodescription' => true,
            ],
        ],
    ],
    // No blocks and minimal footer - used for legacy frame layouts only!
    'frametop' => [
        'file' => 'columns1.php',
        'regions' => [],
        'options' => [
            'nofooter' => true,
            'nocoursefooter' => true,
            'activityheader' => [
                'nocompletion' => true,
            ],
        ],
    ],
    // Embeded pages, like iframe/object embeded in moodleform - it needs as much space as possible.
    'embedded' => [
        'file' => 'embedded.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
    ],
    // Used during upgrade and install, and for the 'This site is undergoing maintenance' message.
    // This must not have any blocks, links, or API calls that would lead to database or cache interaction.
    // Please be extremely careful if you are modifying this layout.
    'maintenance' => [
        'file' => 'maintenance.php',
        'regions' => [],
    ],
    // Should display the content and basic headers only.
    'print' => [
        'file' => 'columns1.php',
        'regions' => [],
        'options' => ['nofooter' => true, 'nonavbar' => false, 'noactivityheader' => true],
    ],
    // The pagelayout used when a redirection is occuring.
    'redirect' => [
        'file' => 'embedded.php',
        'regions' => [],
    ],
    // The pagelayout used for reports.
    'report' => [
        'file' => 'drawers.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
    ],
    // The pagelayout used for safebrowser and securewindow.
    'secure' => [
        'file' => 'secure.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
    ],
];

$THEME->parents = ['boost'];
$THEME->enable_dock = false;
$THEME->extrascsscallback = 'theme_educard_get_extra_scss';
$THEME->prescsscallback = 'theme_educard_get_pre_scss';
$THEME->precompiledcsscallback = 'theme_educard_get_precompiled_css';
$THEME->yuicssmodules = [];
$THEME->rendererfactory = 'theme_overridden_renderer_factory';
$THEME->requiredblocks = '';
$THEME->addblockposition = BLOCK_ADDBLOCK_POSITION_FLATNAV;
$THEME->iconsystem = \core\output\icon_system::FONTAWESOME;
$THEME->haseditswitch = true;
$THEME->usescourseindex = false;
// By default, all boost theme do not need their titles displayed.
$THEME->activityheaderconfig = [
    'notitle' => true,
];

