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
 * Contains class used to prepare verification results for display.
 *
 * @package   mod_customcert
 * @copyright 2017 Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_customcert\output;

use renderable;
use templatable;

/**
 * Class to prepare verification results for display.
 *
 * @package   mod_customcert
 * @copyright 2017 Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class verify_certificate_results implements templatable, renderable {

    /**
     * @var bool Was the code successfully verified?
     */
    public $success;

    /**
     * @var string The message to display.
     */
    public $message;

    /**
     * @var array The certificates issued with the matching code.
     */
    public $issues;

    /**
     * Constructor.
     *
     * @param \stdClass $result
     */
    public function __construct($result) {
        $this->success = $result->success;
        if ($this->success) {
            $this->message = get_string('verified', 'customcert');
        } else {
            $this->message = get_string('notverified', 'customcert');
        }
        $this->issues = $result->issues;
    }

    /**
     * Function to export the renderer data in a format that is suitable for a mustache template.
     *
     * @param \renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return \stdClass|array
     */
    public function export_for_template(\renderer_base $output) {
        $result = new \stdClass();
        $result->success = $this->success;
        $result->message = $this->message;
        $result->issues = [];
        foreach ($this->issues as $issue) {
            $resultissue = new verify_certificate_result($issue);
            $result->issues[] = $resultissue->export_for_template($output);
        }

        return $result;
    }
}
