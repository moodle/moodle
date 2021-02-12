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
 * @package   local_iomad
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function xmldb_local_iomad_install() {
    global $CFG, $DB;

    $systemcontext = context_system::instance();

    // Even worse - change the theme.
    $theme = theme_config::load('iomadboost');
    set_config('theme', $theme->name);
    set_config('allowuserthemes', 1);

    // Enable completion tracking.
    set_config('enablecompletion', 1);

    // Set the default blocks in courses.
    $defblocks = '';
    set_config('defaultblocks_topics', $defblocks);
    set_config('defaultblocks_weeks', $defblocks);

    // Change the default settings for extended username chars to be true.
    $DB->execute("update {config} set value='1' where name='extendedusernamechars'");

}
