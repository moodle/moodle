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
 * Contains class mod_feedback_responses_anon_table
 *
 * @package   mod_feedback
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class mod_feedback_responses_anon_table
 *
 * @package   mod_feedback
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_feedback_responses_anon_table extends mod_feedback_responses_table {

    /** @var string */
    protected $showallparamname = 'ashowall';

    /** @var string */
    protected $downloadparamname = 'adownload';

    /**
     * Initialises table
     */
    public function init() {

        $cm = $this->feedbackstructure->get_cm();
        $this->uniqueid = 'feedback-showentry-anon-list-' . $cm->instance;

        // There potentially can be both tables with anonymouns and non-anonymous responses on
        // the same page (for example when feedback anonymity was changed after some people
        // already responded). In this case we need to distinguish tables' pagination parameters.
        $this->request[TABLE_VAR_PAGE] = 'apage';

        $tablecolumns = ['random_response'];
        $tableheaders = [get_string('response_nr', 'feedback')];

        if ($this->feedbackstructure->get_feedback()->course == SITEID && !$this->feedbackstructure->get_courseid()) {
            $tablecolumns[] = 'courseid';
            $tableheaders[] = get_string('course');
        }

        $this->define_columns($tablecolumns);
        $this->define_headers($tableheaders);

        $this->sortable(true, 'random_response');
        $this->collapsible(true);
        $this->set_attribute('id', 'showentryanontable');

        $params = ['instance' => $cm->instance,
            'anon' => FEEDBACK_ANONYMOUS_YES,
            'courseid' => $this->feedbackstructure->get_courseid()];

        $fields = 'c.id, c.random_response, c.courseid';
        $from = '{feedback_completed} c';
        $where = 'c.anonymous_response = :anon AND c.feedback = :instance';
        if ($this->feedbackstructure->get_courseid()) {
            $where .= ' AND c.courseid = :courseid';
        }

        $group = groups_get_activity_group($this->feedbackstructure->get_cm(), true);
        if ($group) {
            $where .= ' AND c.userid IN (SELECT g.userid FROM {groups_members} g WHERE g.groupid = :group)';
            $params['group'] = $group;
        }

        $this->set_sql($fields, $from, $where, $params);
        $this->set_count_sql("SELECT COUNT(c.id) FROM $from WHERE $where", $params);
    }

    /**
     * Returns a link for viewing a single response
     * @param stdClass $row
     * @return \moodle_url
     */
    protected function get_link_single_entry($row) {
        return new moodle_url($this->baseurl, ['showcompleted' => $row->id]);
    }

    /**
     * Prepares column reponse for display
     * @param stdClass $row
     * @return string
     */
    public function col_random_response($row) {
        if ($this->is_downloading()) {
            return $row->random_response;
        } else {
            return html_writer::link($this->get_link_single_entry($row),
                    get_string('response_nr', 'feedback').': '. $row->random_response);
        }
    }
}
