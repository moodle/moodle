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
 * H5P activity results report.
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
use mod_h5pactivity\output\reportresults;
use stdClass;

/**
 * Class  H5P activity results report.
 *
 * @package    mod_h5pactivity
 * @since      Moodle 3.9
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 */
class results implements report {

    /** @var manager the H5P activity manager instance. */
    private $manager;

    /** @var stdClass the user record. */
    private $user;

    /** @var attempt the h5pactivity attempt to show. */
    private $attempt;

    /**
     * Create a new participants report.
     *
     * @param manager $manager h5pactivity manager object
     * @param stdClass $user user record
     * @param attempt $attempt attempt object
     */
    public function __construct(manager $manager, stdClass $user, attempt $attempt) {
        $this->manager = $manager;
        $this->user = $user;
        $this->attempt = $attempt;
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
        return $this->attempt;
    }

    /**
     * Print the report.
     */
    public function print(): void {
        global $OUTPUT;

        $manager = $this->manager;
        $attempt = $this->attempt;
        $cm = $manager->get_coursemodule();

        $widget = new reportresults($attempt, $this->user, $cm->course);
        echo $OUTPUT->render($widget);
    }

    /**
     * Get the export data form this report.
     *
     * This method is used to render the report in mobile.
     */
    public function export_data_for_external(): stdClass {
        global $PAGE;

        $manager = $this->manager;
        $attempt = $this->attempt;
        $cm = $manager->get_coursemodule();

        $widget = new reportresults($attempt, $this->user, $cm->course);
        return $widget->export_for_template($PAGE->get_renderer('core'));
    }
}
