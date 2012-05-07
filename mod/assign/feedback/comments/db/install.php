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
 * Post-install code for the feedback_comments module.
 *
 * @package   assignfeedback_comments
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Set the initial order for the feedback comments plugin (top)
 * @return bool
 */
function xmldb_assignfeedback_comments_install() {
    global $CFG;

    // do the install
    require_once($CFG->dirroot . '/mod/assign/adminlib.php');

    // set the correct initial order for the plugins
    $pluginmanager = new assign_plugin_manager('assignfeedback');
    $pluginmanager->move_plugin('comments', 'up');

    // do the upgrades
    return true;
}
