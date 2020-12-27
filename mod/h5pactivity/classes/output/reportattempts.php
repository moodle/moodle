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
 * Contains class mod_h5pactivity\output\report\attempts
 *
 * @package   mod_h5pactivity
 * @copyright 2020 Ferran Recio
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_h5pactivity\output;

defined('MOODLE_INTERNAL') || die();

use mod_h5pactivity\local\attempt;
use mod_h5pactivity\output\attempt as output_attempt;
use renderable;
use templatable;
use renderer_base;
use user_picture;
use stdClass;

/**
 * Class to output an attempts report on mod_h5pactivity.
 *
 * @copyright 2020 Ferran Recio
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class reportattempts implements renderable, templatable {

    /** @var attempt[] attempts */
    public $attempts;

    /** @var stdClass user record */
    public $user;

    /** @var int courseid necesary to present user picture */
    public $courseid;

    /** @var attempt scored attempt */
    public $scored;

    /** @var string scored attempt title */
    public $title;

    /**
     * Constructor.
     *
     * The "scored attempt" is the attempt used for grading. By default it is the max score attempt
     * but this could be defined in the activity settings. In some cases this scored attempts does not
     * exists at all, this is the reason why it's an optional param.
     *
     * @param array $attempts an array of attempts
     * @param stdClass $user a user record
     * @param int $courseid course id
     * @param string|null $title title to display on the scored attempt (null if none attempt is the scored one)
     * @param attempt|null $scored the scored attempt (null if none)
     */
    public function __construct(array $attempts, stdClass $user, int $courseid, string $title = null, attempt $scored = null) {
        $this->attempts = $attempts;
        $this->user = $user;
        $this->courseid = $courseid;
        $this->title = $title;
        $this->scored = $scored;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        global $USER;

        $data = (object)['attempts' => [], 'user' => $this->user];
        foreach ($this->attempts as $attempt) {
            $outputattempt = new output_attempt($attempt);
            $data->attempts[] = $outputattempt->export_for_template($output);
        }
        $data->attemptscount = count($data->attempts);

        $userpicture = new user_picture($this->user);
        $userpicture->courseid = $this->courseid;
        $data->user->fullname = fullname($this->user);
        $data->user->picture = $output->render($userpicture);

        if ($USER->id == $this->user->id) {
            $data->title = get_string('myattempts', 'mod_h5pactivity');
        }

        if (!empty($this->title)) {
            $scored = (object)[
                'title' => $this->title,
                'attempts' => [],
            ];
            $outputattempt = new output_attempt($this->scored);
            $scored->attempts[] = $outputattempt->export_for_template($output);
            $data->scored = $scored;
        }

        return $data;
    }
}
