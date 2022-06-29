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
 * DB schema performance check
 *
 * @package    core
 * @category   check
 * @copyright  2021 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\check\performance;

defined('MOODLE_INTERNAL') || die();

use core\check\check;
use core\check\result;

/**
 * DB schema performance check
 *
 * @copyright  2021 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dbschema extends check {

    /**
     * Get the short check name
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('check_dbschema_name', 'report_performance');
    }

    /**
     * A link to a place to action this
     *
     * @return action_link|null
     */
    public function get_action_link(): ?\action_link {
        return new \action_link(
            new \moodle_url(\get_docs_url('Verify_Database_Schema')),
            get_string('moodledocs'));
    }

    /**
     * Return result
     * @return result
     */
    public function get_result(): result {
        global $DB;

        $dbmanager = $DB->get_manager();
        $schema = $dbmanager->get_install_xml_schema();

        if (!$errors = $dbmanager->check_database_schema($schema)) {
            return new result(result::OK, get_string('check_dbschema_ok', 'report_performance'), '');
        }

        $details = '';
        foreach ($errors as $tablename => $items) {
            $details .= \html_writer::tag('h4', $tablename);
            foreach ($items as $item) {
                $details .= \html_writer::tag('pre', $item);
            }
        }
        return new result(result::ERROR, get_string('check_dbschema_errors', 'report_performance'), $details);
    }
}

