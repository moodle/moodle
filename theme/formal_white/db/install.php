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
 * formal_white module upgrade code
 *
 * This file keeps track of upgrades to
 * the theme plugin
 *
 * Sometimes, changes between versions involve
 * alterations to database structures and other
 * major things that may lose the effort they've put
 * into customising and setting up your theme.
 *
 * The upgrade function in this file will attempt
 * to perform all the necessary actions to upgrade
 * your older installation to the current version.
 *
 * If there's something it cannot do itself, it
 * will tell you what you need to do.
 *
 * The commands in here will all be database-neutral,
 * using the methods of database_manager class
 *
 * Please do not forget to use upgrade_set_timeout()
 * before any action that may take longer time to finish.
 *
 * @package    theme
 * @subpackage formal_white
 * @copyright  Mediatouch 2000 (http://mediatouch.it/)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_theme_formal_white_install() {
    $currentsetting = get_config('theme_formal_white');

    // Remove all the useless settings of the first pre-release
    // Remove backgroundcolor
    unset_config('backgroundcolor', 'theme_formal_white');
    // Remove regionwidth
    unset_config('regionwidth', 'theme_formal_white');
    // Remove alwayslangmenu
    unset_config('alwayslangmenu', 'theme_formal_white');

    // Create a new config setting called lblockcolumnbgc and give it blockcolumnbgc's value.
    set_config('lblockcolumnbgc', $currentsetting->blockcolumnbgc, 'theme_formal_white');
    // Remove blockcolumnbgc
    unset_config('blockcolumnbgc', 'theme_formal_white');

    return true;
}