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

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');
/**
 * Step definition for report_loglive behat tests.
 *
 * @package    report_loglive
 * @category   test
 * @copyright  2025 Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_report_loglive extends behat_base {
    /**
     * Convert page names to URLs for steps like 'When I am on the "[identifier]" "[page type]" page'.
     *
     * Recognised page names are:
     * | pagetype     | name meaning | description                     |
     * | Logs         | Course name  | The course report loglive page  |
     *
     * @param string $page identifies which type of page this is, e.g. 'Logs'.
     * @param string $identifier identifies the particular page, e.g. 'C1'.
     * @return moodle_url the corresponding URL.
     * @throws Exception with a meaningful error message if the specified page cannot be found.
     */
    protected function resolve_page_instance_url(string $page, string $identifier): moodle_url {
        switch (strtolower($page)) {
            case 'logs':
                $courseid = $this->get_course_id($identifier);
                return new moodle_url('/report/loglive/index.php', [
                    'id' => $courseid,
                ]);
            default:
                throw new Exception("Unrecognised page type '{$page}'");
        }
    }
}
