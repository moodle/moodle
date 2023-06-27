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
 * H5P activity participants report
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
use core\dml\sql_join;
use table_sql;
use moodle_url;
use html_writer;
use stdClass;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir.'/tablelib.php');

/**
 * Class  H5P activity participants report.
 *
 * @package    mod_h5pactivity
 * @since      Moodle 3.9
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 */
class participants extends table_sql implements report {

    /** @var manager the H5P activity manager instance. */
    private $manager;

    /** @var array the users scored attempts. */
    private $scores;

    /** @var array the user attempts count. */
    private $count;

    /**
     * Create a new participants report.
     *
     * @param manager $manager h5pactivitymanager object
     */
    public function __construct(manager $manager) {
        parent::__construct('mod_h5pactivity-participants');
        $this->manager = $manager;
        $this->scores = $manager->get_users_scaled_score();
        $this->count = $manager->count_users_attempts();

        // Setup table_sql.
        $columns = ['fullname', 'timemodified', 'score', 'attempts'];
        $headers = [
            get_string('fullname'), get_string('date'),
            get_string('score', 'mod_h5pactivity'), get_string('attempts', 'mod_h5pactivity'),
        ];
        $this->define_columns($columns);
        $this->define_headers($headers);
        $this->set_attribute('class', 'generaltable generalbox boxaligncenter boxwidthwide');
        $this->sortable(true);
        $this->no_sorting('score');
        $this->no_sorting('timemodified');
        $this->no_sorting('attempts');
        $this->pageable(true);

        $capjoin = $this->manager->get_active_users_join(true);

        // Final SQL.
        $this->set_sql(
            'DISTINCT u.id, u.picture, u.firstname, u.lastname, u.firstnamephonetic, u.lastnamephonetic,
            u.middlename, u.alternatename, u.imagealt, u.email',
            "{user} u $capjoin->joins",
            $capjoin->wheres,
            $capjoin->params);
    }

    /**
     * Return the report user record.
     *
     * Participants report has no specific user.
     *
     * @return stdClass|null a user or null
     */
    public function get_user(): ?stdClass {
        return null;
    }

    /**
     * Return the report attempt object.
     *
     * Participants report has no specific attempt.
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
        global $PAGE, $OUTPUT;

        $this->define_baseurl($PAGE->url);

        echo $OUTPUT->heading(get_string('attempts_report', 'mod_h5pactivity'));

        $this->out($this->get_page_size(), true);
    }

    /**
     * Warning in case no user has the selected initials letters.
     *
     */
    public function print_nothing_to_display() {
        global $OUTPUT;
        echo $this->render_reset_button();
        $this->print_initials_bar();
        echo $OUTPUT->notification(get_string('noparticipants', 'mod_h5pactivity'), 'warning');
    }

    /**
     * Generate the fullname column.
     *
     * @param stdClass $user
     * @return string
     */
    public function col_fullname($user): string {
        global $OUTPUT;
        $cm = $this->manager->get_coursemodule();
        return $OUTPUT->user_picture($user, ['size' => 35, 'courseid' => $cm->course, 'includefullname' => true]);
    }

    /**
     * Generate score column.
     *
     * @param stdClass $user the user record
     * @return string
     */
    public function col_score(stdClass $user): string {
        $cm = $this->manager->get_coursemodule();
        if (isset($this->scores[$user->id])) {
            $score = $this->scores[$user->id];
            $maxgrade = floatval(100);
            $scaled = round($maxgrade * $score->scaled).'%';
            if (empty($score->attemptid)) {
                return $scaled;
            } else {
                $url = new moodle_url('/mod/h5pactivity/report.php', ['a' => $cm->instance, 'attemptid' => $score->attemptid]);
                return html_writer::link($url, $scaled);
            }
        }
        return '';
    }

    /**
     * Generate attempts count column, if any.
     *
     * @param stdClass $user the user record
     * @return string
     */
    public function col_attempts(stdClass $user): string {
        $cm = $this->manager->get_coursemodule();
        if (isset($this->count[$user->id])) {
            $msg = get_string('review_user_attempts', 'mod_h5pactivity', $this->count[$user->id]);
            $url = new moodle_url('/mod/h5pactivity/report.php', ['a' => $cm->instance, 'userid' => $user->id]);
            return html_writer::link($url, $msg);
        }
        return '';

    }

    /**
     * Generate attempt timemodified column, if any.
     *
     * @param stdClass $user the user record
     * @return string
     */
    public function col_timemodified(stdClass $user): string {
        if (isset($this->scores[$user->id])) {
            $score = $this->scores[$user->id];
            return userdate($score->timemodified);
        }
        return '';
    }
}
