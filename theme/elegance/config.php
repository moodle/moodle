<?php
// This file is part of the custom Moodle elegance theme
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
 * Renderers to align Moodle's HTML with that expected by elegance
 *
 * @package    theme_elegance
 * @copyright  2015 Bas Brands http://basbrands.nl
 * @authors    Bas Brands, David Scotson.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$THEME->doctype = 'html5';

$THEME->yuicssmodules = array();

$THEME->name = 'elegance';
$THEME->enable_dock = true;
$THEME->parents = array('bootstrap');
$THEME->sheets = array('custom', 'mobile', 'km');
$THEME->lessfile = 'elegance';
$THEME->parents_exclude_sheets = array('bootstrap' => array('moodle'));
$THEME->lessvariablescallback = 'theme_elegance_less_variables';
$THEME->extralesscallback = 'theme_elegance_extra_less';
$THEME->supportscssoptimisation = false;
$THEME->rendererfactory = 'theme_overridden_renderer_factory';
$THEME->csspostprocess = 'theme_elegance_process_css';

$themeconfig = get_config('theme_elegance');

$regions = array('side-pre', 'side-post');
$singleregion = array('side-pre');
$defaultregion = 'side-pre';
$sidemiddle = array('side-middle');

if (empty($themeconfig->blocksconfig)) {
    // Do nothing
} else if ($themeconfig->blocksconfig == 2) {
    $regions = array('side-pre');
    $defaultregion = 'side-pre';
} else if ($themeconfig->blocksconfig == 3) {
    $regions = array('side-post');
    $singleregion = array('side-post');
    $defaultregion = 'side-post';
}


$THEME->layouts = array(
    // Most backwards compatible layout without the blocks - this is the layout used by default.
    'base' => array(
        'file' => 'default.php',
        'regions' => array(),
    ),
    // Standard layout with blocks, this is recommended for most pages with general information.
    'standard' => array(
        'file' => 'default.php',
        'regions' => $regions,
        'defaultregion' => $defaultregion,
    ),
    // Main course page.
    'course' => array(
        'file' => 'default.php',
        'regions' => $regions,
        'defaultregion' => $defaultregion,
        'options' => array('langmenu' => true),
    ),
    'coursecategory' => array(
        'file' => 'default.php',
        'regions' => [],
        'defaultregion' => $defaultregion,
    ),
    // Part of course, typical for modules - default page layout if $cm specified in require_login().
    'incourse' => array(
        'file' => 'default.php',
        'regions' => $regions,
        'defaultregion' => $defaultregion,
    ),
    // The site home page.
    'frontpage' => array(
        'file' => 'default.php',
        'regions' => [],
        'defaultregion' => $defaultregion,
        'options' => array(
            'nobreadcrumb' => true,
            'hasbanner' => true,
            'hasmarketing' => true,
            'hasquicklinks' => true,
            'hasfrontpagecontent' => true,
            'hasmycoursesslick' => true,
            'hasloginoverlay' => true,
            'nomoodleheader' => true),
    ),
    // Server administration scripts.
    'admin' => array(
        'file' => 'default.php',
        'regions' => $regions,
        'defaultregion' => $defaultregion,
        'options' => array('fluid' => true),
    ),
    // My dashboard page.
    'mydashboard' => array(
        'file' => 'default.php',
        'regions' => $regions,
        'defaultregion' => $defaultregion,
        'options' => array('langmenu' => true),
    ),
    // My public page.
    'mypublic' => array(
        'file' => 'default.php',
        'regions' => $regions,
        'defaultregion' => $defaultregion,
    ),
    'login' => array(
        'file' => 'default.php',
        'regions' => array(),
        'options' => array('langmenu' => true, 'nobreadcrumb' => true, 'transparentmain' => true, 'nomoodleheader' => true),
    ),

    // Pages that appear in pop-up windows - no navigation, no blocks, no header.
    'popup' => array(
        'file' => 'popup.php',
        'regions' => array(),
        'options' => array('nofooter' => true, 'nobreadcrumb' => true),
    ),
    // No blocks and minimal footer - used for legacy frame layouts only!
    'frametop' => array(
        'file' => 'default.php',
        'regions' => array(),
        'options' => array('nofooter' => true, 'nocoursefooter' => true),
    ),
    // Embeded pages, like iframe/object embeded in moodleform - it needs as much space as possible.
    'embedded' => array(
        'file' => 'embedded.php',
        'regions' => array()
    ),
    // Used during upgrade and install, and for the 'This site is undergoing maintenance' message.
    // This must not have any blocks, links, or API calls that would lead to database or cache interaction.
    // Please be extremely careful if you are modifying this layout.
    'maintenance' => array(
        'file' => 'default.php',
        'regions' => array(),
        'options' => array('langmenu' => true, 'nonavbar' => true, 'nobreadcrumb' => true, 'transparentmain' => true, 'nomoodleheader' => true, 'nofooter' => true),
    ),
    // Should display the content and basic headers only.
    'print' => array(
        'file' => 'default.php',
        'regions' => array(),
        'options' => array('nofooter' => true, 'nobreadcrumb' => false),
    ),
    // The pagelayout used when a redirection is occuring.
    'redirect' => array(
        'file' => 'embedded.php',
        'regions' => array(),
    ),
    // The pagelayout used for reports.
    'report' => array(
        'file' => 'default.php',
        'regions' => $singleregion,
        'defaultregion' => $defaultregion,
    ),
    // The pagelayout used for safebrowser and securewindow.
    'secure' => array(
        'file' => 'default.php',
        'regions' => $regions,
        'defaultregion' => $defaultregion
    ),
);

$THEME->csspostprocess = 'theme_elegance_process_css';

$THEME->javascripts = array(
    'slick', 'loginoverlay', 'atbar'
);

$THEME->javascripts_footer = array(
    'fitvid', 'blocks','reader'
);

$THEME->hidefromselector = false;
$THEME->lessfile = 'elegance';
