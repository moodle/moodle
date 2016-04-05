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

    /**
     * Initialises table
     */
    public function init() {

        $this->uniqueid = 'feedback-showentry-anon-list-' . $this->cm->instance;

        // There potentially can be both tables with anonymouns and non-anonymous responses on
        // the same page (for example when feedback anonymity was changed after some people
        // already responded). In this case we need to distinguish tables' pagination parameters.
        $this->request[TABLE_VAR_PAGE] = 'apage';

        $tablecolumns = array('random_response', 'showresponse');
        $tableheaders = array('', '');

        $context = context_module::instance($this->cm->id);
        if (has_capability('mod/feedback:deletesubmissions', $context)) {
            $tablecolumns[] = 'deleteentry';
            $tableheaders[] = '';
        }

        $this->define_columns($tablecolumns);
        $this->define_headers($tableheaders);

        $this->sortable(false, 'random_response');
        $this->collapsible(false);
        $this->set_attribute('id', 'showentryanonymtable');

        $params = ['instance' => $this->cm->instance, 'anon' => FEEDBACK_ANONYMOUS_YES];

        $fields = 'DISTINCT c.id, c.random_response';
        $from = '{feedback_completed} c';
        $where = 'c.anonymous_response = :anon AND c.feedback = :instance';

        $group = groups_get_activity_group($this->cm, true);
        if ($group) {
            $from .= ' JOIN {groups_members} g ON g.groupid = :group AND g.userid = c.userid';
            $params['group'] = $group;
        }

        $this->set_sql($fields, $from, $where, $params);
        $this->set_count_sql("SELECT COUNT(DISTINCT c.id) FROM $from WHERE $where", $params);
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
        return get_string('response_nr', 'feedback').': '. $row->random_response;
    }

    /**
     * Prepares column showresponse for display
     * @param stdClass $row
     * @return string
     */
    public function col_showresponse($row) {
        return html_writer::link($this->get_link_single_entry($row), get_string('show_entry', 'feedback'));
    }

    /**
     * Generate the HTML for the table preferences reset button.
     */
    protected function render_reset_button() {
        return '';
    }
}
