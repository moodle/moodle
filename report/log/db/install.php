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
 * Post installation and migration code.
 *
 * @package    report
 * @subpackage log
 * @copyright  2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

function xmldb_report_log_install() {
    global $DB;

    // this is a hack which is needed for cleanup of original coursereport_log stuff
    unset_all_config_for_plugin('coursereport_log');
    capabilities_cleanup('coursereport_log');

    // update existing block page patterns
    $DB->set_field('block_instances', 'pagetypepattern', 'report-log-index', array('pagetypepattern'=>'course-report-log-index'));
}

