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
 * Essential is a clean and customizable theme.
 *
 * @package     theme_essential
 * @copyright   2017 Gareth J Barnard
 * @copyright   2016 Gareth J Barnard
 * @copyright   2015 Gareth J Barnard
 * @copyright   2014 Gareth J Barnard, David Bezemer
 * @copyright   2013 Julian Ridden
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$THEME->name = 'essential';

$THEME->doctype = 'html5';
$THEME->yuicssmodules = array();
$THEME->parents = array('bootstrapbase');
$THEME->parents_exclude_sheets = array('bootstrapbase' => array('moodle', 'editor'));

$THEME->sheets[] = 'fontawesome';
$THEME->sheets[] = 'essential';
$THEME->sheets[] = 'bootstrap-pix';
$THEME->sheets[] = 'essential-settings';

if ((get_config('theme_essential', 'enablealternativethemecolors1')) ||
        (get_config('theme_essential', 'enablealternativethemecolors2')) ||
        (get_config('theme_essential', 'enablealternativethemecolors3')) ||
        (get_config('theme_essential', 'enablealternativethemecolors4'))
) {
    $THEME->sheets[] = 'essential-alternative';
}

if (get_config('theme_essential', 'customscrollbars')) {
    $THEME->sheets[] = 'essential-scrollbars';
}

$THEME->sheets[] = 'custom';

$THEME->supportscssoptimisation = false;

$THEME->javascripts_footer = array('essential');
$THEME->enable_dock = true;
$THEME->javascripts_footer[] = 'dock';

$THEME->editor_sheets = array('editor', 'custom');

$baseregions = array('footer-left', 'footer-middle', 'footer-right');
$fpaddregions = array();
if (get_config('theme_essential', 'frontpagemiddleblocks') > 0) {
    $fpaddregions[] = 'home';
}
if (get_config('theme_essential', 'fppagetopblocks') > 0) {
    $fpaddregions[] = 'page-top';
}
if (get_config('theme_essential', 'haveheaderblock') > 0) {
    $baseregions[] = 'header';
    $fpaddregions[] = 'header';
}
$onecolumnregions = array_merge($baseregions);
$standardregions = array_merge(array('side-pre'), $baseregions);
if (get_config('theme_essential', 'pagetopblocks')) {
    $onecolumnregions[] = 'page-top';
    $standardregions[] = 'page-top';
}

$THEME->layouts = array(
    // Most backwards compatible layout without the blocks - this is the layout used by default.
    'base' => array(
        'file' => 'columns1.php',
        'regions' => $onecolumnregions,
        'defaultregion' => 'footer-middle',
    ),
    // Front page.
    'frontpage' => array(
        'file' => 'frontpage.php',
        'regions' => array_merge(array('side-pre', 'footer-left', 'footer-middle', 'footer-right', 'hidden-dock'),
                $fpaddregions),
        'defaultregion' => 'side-pre',
    ),
    // Standard layout with blocks, this is recommended for most pages with general information.
    'standard' => array(
        'file' => 'columns2.php',
        'regions' => $standardregions,
        'defaultregion' => 'side-pre',
    ),
    // Main course page.
    'course' => array(
        'file' => 'columns3.php',
        'regions' => array_merge($standardregions, array('side-post')),
        'defaultregion' => 'side-post',
    ),
    'coursecategory' => array(
        'file' => 'columns2.php',
        'regions' => $standardregions,
        'defaultregion' => 'side-pre',
    ),
    // Part of course, typical for modules - default page layout if $cm specified in require_login().
    'incourse' => array(
        'file' => 'columns2.php',
        'regions' => $standardregions,
        'defaultregion' => 'side-pre',
    ),
    // Server administration scripts.
    'admin' => array(
        'file' => 'admin.php',
        'regions' => array_merge($baseregions, array('side-pre')),
        'defaultregion' => 'side-pre',
    ),
    // My dashboard page.
    'mydashboard' => array(
        'file' => 'columns3.php',
        'regions' => array_merge($standardregions, array('side-post')),
        'defaultregion' => 'side-post',
    ),
    // My public page.
    'mypublic' => array(
        'file' => 'columns3.php',
        'regions' => array_merge($standardregions, array('side-post')),
        'defaultregion' => 'side-post',
    ),
    'login' => array(
        'file' => 'login.php',
        'regions' => array('footer-left', 'footer-middle', 'footer-right'),
        'defaultregion' => '',
    ),
    // Pages that appear in pop-up windows - no navigation, no blocks, no header.
    'popup' => array(
        'file' => 'popup.php',
        'regions' => array(),
        'options' => array('nofooter' => true, 'nonavbar' => true),
    ),
    // No blocks and minimal footer - used for legacy frame layouts only!
    'frametop' => array(
        'file' => 'columns1.php',
        'regions' => array('footer-left', 'footer-middle', 'footer-right'),
        'defaultregion' => 'footer-right',
        'options' => array('nofooter' => true, 'nocoursefooter' => true),
    ),
    // Embeded pages, like iframe/object embeded in moodleform - it needs as much space as possible.
    'embedded' => array(
        'file' => 'embedded.php',
        'regions' => array(),
        'defaultregion' => '',
    ),
    // Used during upgrade and install, and for the 'This site is undergoing maintenance' message.
    // This must not have any blocks, links, or API calls that would lead to database or cache interaction.
    // Please be extremely careful if you are modifying this layout.
    'maintenance' => array(
        'file' => 'maintenance.php',
        'regions' => array(),
        'defaultregion' => '',
    ),
    // Should display the content and basic headers only.
    'print' => array(
        'file' => 'columns1.php',
        'regions' => $onecolumnregions,
        'defaultregion' => '',
        'options' => array('nofooter' => true),
    ),
    // The pagelayout used when a redirection is occuring.
    'redirect' => array(
        'file' => 'redirect.php',
        'regions' => array(),
        'defaultregion' => '',
    ),
    // The pagelayout used for reports.
    'report' => array(
        'file' => 'report.php',
        'regions' => array_merge($baseregions, array('side-pre')),
        'defaultregion' => 'side-pre',
    ),
    // The pagelayout used for safebrowser and securewindow.
    'secure' => array(
        'file' => 'secure.php',
        'regions' => array('side-pre', 'side-post'),
        'defaultregion' => 'side-pre'
    ),
);

$THEME->rendererfactory = 'theme_overridden_renderer_factory';
$THEME->csspostprocess = 'theme_essential_process_css';

$THEME->iconsystem = '\\theme_essential\\output\\icon_system_fontawesome';