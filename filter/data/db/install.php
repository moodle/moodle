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
 * Database activity filter post install hook
 *
 * @package    filter
 * @subpackage data
 * @copyright  2011 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_filter_data_install() {
    global $DB;

    // If the legacy mod/data filter is installed we need to:
    //   1- Delete new filter (filter_active and filter_config) information, in order to
    //   2- Usurpate the identity of the legacy filter by moving all its
    //      information to filter/data
    // If the legacy mod/data filter was not installed, no action is needed
    if ($DB->record_exists('filter_active', array('filter' => 'mod/data'))) {
        $DB->delete_records('filter_active', array('filter' => 'filter/data'));
        $DB->set_field('filter_active', 'filter', 'filter/data', array('filter' => 'mod/data'));
    }
}
