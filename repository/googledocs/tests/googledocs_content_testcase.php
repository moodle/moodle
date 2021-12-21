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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/repository/googledocs/tests/repository_googledocs_testcase.php');

/**
 * Base class for the googledoc repository unit tests related to content browsing and searching.
 *
 * @package    repository_googledocs
 * @copyright  2021 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class googledocs_content_testcase extends repository_googledocs_testcase {

    /** @var array The array which contains the disallowed file extensions. */
    protected $disallowedextensions = [];

    /**
     * Method used for filtering repository file nodes based on the current disallowed file extensions list.
     *
     * @param array $content The repository content node
     * @return bool If returns false, the repository content node should be filtered, otherwise do not filter.
     */
    public function filter(array $content): bool {
        // If the disallowed file extensions list is empty, do not filter the content node.
        if (empty($this->disallowedextensions)) {
            return true;
        }
        foreach ($this->disallowedextensions as $extension) {
            // If the disallowed file extension matches the extension of the repository file node,
            // than filter this node.
            if (preg_match("#.{$extension}#i", $content['title'])) {
                return false;
            }
        }
        return true;
    }
}
