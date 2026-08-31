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
 * Custom Behat page objects for report_aiusage.
 *
 * @package    report_aiusage
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

/**
 * Custom Behat page objects for report_aiusage.
 */
class behat_report_aiusage extends behat_base {
    /**
     * Resolve the URL for a report_aiusage page.
     *
     * Recognised page types:
     * - Course report: identified by the course shortname.
     *
     * @param string $type
     * @param string $identifier the course shortname
     * @return moodle_url
     * @throws Exception for an unrecognised page type
     */
    protected function resolve_page_instance_url(string $type, string $identifier): moodle_url {
        global $DB;

        switch ($type) {
            case 'Course report':
                $courseid = $DB->get_field('course', 'id', ['shortname' => $identifier], MUST_EXIST);
                return new moodle_url('/report/aiusage/index.php', ['id' => $courseid]);

            default:
                throw new Exception("Unrecognised report_aiusage page type '{$type}'");
        }
    }
}
