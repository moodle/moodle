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
 * Glossary filter post install hook
 *
 * @package    filter
 * @subpackage glossary
 * @copyright  2011 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_filter_glossary_install() {
    global $DB;

    // If the legacy mod/glossary filter is installed we need to:
    //   1- Delete new filter (filter_active and filter_config) information, in order to
    //   2- Usurpate the identity of the legacy filter by moving all its
    //      information to filter/glossary
    // If the legacy mod/glossary filter was not installed, no action is needed
    if ($DB->record_exists('filter_active', array('filter' => 'mod/glossary'))) {
        $DB->delete_records('filter_active', array('filter' => 'filter/glossary'));
        $DB->set_field('filter_active', 'filter', 'filter/glossary', array('filter' => 'mod/glossary'));
    }
}
