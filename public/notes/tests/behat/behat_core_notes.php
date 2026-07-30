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
 * Behat step definitions for core_notes.
 *
 * @package    core_notes
 * @category   test
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

require_once(__DIR__ . '/../../../lib/behat/behat_base.php');

/**
 * Behat context for core_notes page type resolution.
 *
 * @package    core_notes
 * @category   test
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_core_notes extends behat_base {
    /**
     * Resolve page instance URLs for core_notes.
     *
     * Supported page types:
     * - 'course index': The notes index page for a given course (identified by shortname).
     *
     * @param string $type The page type, e.g. 'course index'.
     * @param string $identifier The course shortname.
     * @return moodle_url
     * @throws Exception if the page type is not recognised.
     */
    protected function resolve_page_instance_url(string $type, string $identifier): moodle_url {
        switch (strtolower($type)) {
            case 'course index':
                $courseid = $this->get_course_id($identifier);
                return new moodle_url('/notes/index.php', [
                    'filtertype' => 'course',
                    'filterselect' => $courseid,
                ]);

            default:
                throw new Exception("Unrecognised core_notes page type '{$type}'.");
        }
    }
}
