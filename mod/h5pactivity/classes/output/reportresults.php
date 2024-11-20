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
 * Contains class mod_h5pactivity\output\reportresults
 *
 * @package   mod_h5pactivity
 * @copyright 2020 Ferran Recio
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_h5pactivity\output;

defined('MOODLE_INTERNAL') || die();

use mod_h5pactivity\local\attempt;
use mod_h5pactivity\output\attempt as output_attempt;
use mod_h5pactivity\output\result as output_result;
use renderable;
use templatable;
use renderer_base;
use stdClass;

/**
 * Class to display the result report in mod_h5pactivity.
 *
 * @copyright 2020 Ferran Recio
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class reportresults implements renderable, templatable {

    /** @var attempt the header attempt */
    public $attempt;

    /** @var stdClass user record */
    public $user;

    /** @var int courseid necesary to present user picture */
    public $courseid;

    /**
     * Constructor.
     *
     * @param attempt $attempt the current attempt
     * @param stdClass $user a user record
     * @param int $courseid course id
     */
    public function __construct(attempt $attempt, stdClass $user, int $courseid) {
        $this->attempt = $attempt;
        $this->user = $user;
        $this->courseid = $courseid;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {

        $outputattempt = new output_attempt($this->attempt, $this->user, $this->courseid);

        $data = (object)[
            'attempt' => $outputattempt->export_for_template($output),
        ];

        $results = $this->attempt->get_results();
        $data->results = [];
        foreach ($results as $key => $result) {
            $outputresult = output_result::create_from_record($result);
            if ($outputresult) {
                $data->results[] = $outputresult->export_for_template($output);
            }
        }

        return $data;
    }
}
