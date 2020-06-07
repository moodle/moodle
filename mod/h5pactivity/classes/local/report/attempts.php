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
 * H5P activity attempts report
 *
 * @package    mod_h5pactivity
 * @since      Moodle 3.9
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_h5pactivity\local\report;

use mod_h5pactivity\local\report;
use mod_h5pactivity\local\manager;
use mod_h5pactivity\local\attempt;
use mod_h5pactivity\output\reportattempts;
use stdClass;

/**
 * Class  H5P activity attempts report.
 *
 * @package    mod_h5pactivity
 * @since      Moodle 3.9
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 */
class attempts implements report {

    /** @var manager the H5P activity manager instance. */
    private $manager;

    /** @var stdClass the user record. */
    private $user;

    /**
     * Create a new participants report.
     *
     * @param manager $manager h5pactivity manager object
     * @param stdClass $user user record
     */
    public function __construct(manager $manager, stdClass $user) {
        $this->manager = $manager;
        $this->user = $user;
    }

    /**
     * Return the report user record.
     *
     * @return stdClass|null a user or null
     */
    public function get_user(): ?stdClass {
        return $this->user;
    }

    /**
     * Return the report attempt object.
     *
     * Attempts report has no specific attempt.
     *
     * @return attempt|null the attempt object or null
     */
    public function get_attempt(): ?attempt {
        return null;
    }

    /**
     * Print the report.
     */
    public function print(): void {
        global $OUTPUT;

        $manager = $this->manager;
        $cm = $manager->get_coursemodule();

        $scored = $this->get_scored();
        $title = $scored->title ?? null;
        $scoredattempt = $scored->attempt ?? null;

        $attempts = $this->get_attempts();

        $widget = new reportattempts($attempts, $this->user, $cm->course, $title, $scoredattempt);
        echo $OUTPUT->render($widget);
    }

    /**
     * Return the current report attempts.
     *
     * This method is used to render the report in both browser and mobile.
     *
     * @return attempts[]
     */
    public function get_attempts(): array {
        return $this->manager->get_user_attempts($this->user->id);
    }

    /**
     * Return the current report attempts.
     *
     * This method is used to render the report in both browser and mobile.
     *
     * @return stdClass|null a structure with
     *      - title => name of the selected attempt (or null)
     *      - attempt => the selected attempt object (or null)
     *      - gradingmethos => the activity grading method (or null)
     */
    public function get_scored(): ?stdClass {
        $manager = $this->manager;
        $scores = $manager->get_users_scaled_score($this->user->id);
        $score = $scores[$this->user->id] ?? null;

        if (empty($score->attemptid)) {
            return null;
        }

        list($grademethod, $title) = $manager->get_selected_attempt();
        $scoredattempt = $manager->get_attempt($score->attemptid);

        $result = (object)[
            'title' => $title,
            'attempt' => $scoredattempt,
            'grademethod' => $grademethod,
        ];
        return $result;
    }
}
