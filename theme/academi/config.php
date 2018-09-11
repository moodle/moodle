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
 * Theme Config details
 *
 * @package    theme_academi
 * @copyright  2015 LmsaceDev Team , lmsace.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
defined('MOODLE_INTERNAL') || die;

$THEME->name = 'academi';

$THEME->sheets = [];

$THEME->editor_sheets = [];

$THEME->parents = ['boost'];

$THEME->enable_dock = false;

$THEME->yuicssmodules = array();

$THEME->rendererfactory = 'theme_overridden_renderer_factory';

$THEME->requiredblocks = '';

$THEME->addblockposition = BLOCK_ADDBLOCK_POSITION_FLATNAV;

$THEME->prescsscallback = 'theme_academi_get_pre_scss';

$THEME->scss = function($theme) {
    return theme_academi_get_main_scss_content($theme);
};

$THEME->csspostprocess = 'theme_academi_process_css';

$THEME->extrascsscallback = 'theme_academi_get_extra_scss';

$THEME->javascripts_footer = ['theme'];

$THEME->layouts = array(
        // The site home page.
        'frontpage' => array(
                'file' => 'frontpage.php',
                'regions' => array('side-pre'),
                'defaultregion' => 'side-pre',
                'options' => array('nonavbar' => true),
        ),
        'login' => array(
                'file' => 'login.php',
                'regions' => array('side-pre'),
                'defaultregion' => 'side-pre',
                'options' => array('nonavbar' => true),
        )
);