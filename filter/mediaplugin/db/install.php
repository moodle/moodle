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
 * Media filter post install hook
 *
 * @package    filter
 * @subpackage mediaplugin
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function xmldb_filter_mediaplugin_install() {
    global $CFG;

    //enable by default in new installs and upgrades (because we did not have version.php before)
    // but only if insecure swf embedding is off - we definitely do not want to open security hopes on existing sites
    if (empty($CFG->filter_mediaplugin_enable_swf)) {
        filter_set_global_state('filter/mediaplugin', TEXTFILTER_ON);
    }
}

