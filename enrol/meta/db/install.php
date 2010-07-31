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
 * Meta link enrolment plugin installation.
 *
 * @package    enrol
 * @subpackage meta
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_enrol_meta_install() {
    global $CFG, $DB;

    if (isset($CFG->nonmetacoursesyncroleids)) {
        set_config('nosyncroleids', $CFG->nonmetacoursesyncroleids, 'enrol_meta');
        unset_config('nonmetacoursesyncroleids');
    }

    if (!$DB->record_exists('enrol', array('enrol'=>'meta'))) {
        // no need to syn roles and enrolments
        return;
    }

    // brute force course resync, this may take a while
    require_once("$CFG->dirroot/enrol/meta/locallib.php");
    enrol_meta_sync();
}
