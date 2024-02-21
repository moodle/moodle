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
 * Base class for the table used by a {@link quiz_attempts_report}.
 *
 * @package   local_report_user_logins
 * @copyright 2012 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_trainingevent\tables;

use \table_sql;
use \iomad;
use \context_system;
use \moodle_url;
use \context_module;
use \single_select;
use html_writer;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');

class attendees_table extends table_sql {

    /**
     * Generate the display of the user's| fullname
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_fullname($row) {
        global $id;

        $name = fullname($row, has_capability('moodle/site:viewfullnames', context_module::instance($id)));
        return $name;
    }

    /**
     * Generate the display of the user's| fullname
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_event($row) {
        global $params, $id, $waitingoption, $event, $eventselect, $OUTPUT;

        if ($this->is_downloading()) {
            return format_text($event->name);
        }

        if (has_capability('mod/trainingevent:add', context_module::instance($id))) {
            $select = new single_select(new moodle_url('/mod/trainingevent/view.php',
                                                       ['userid' => $row->id,
                                                        'id' => $id,
                                                        'view' => 1,
                                                        'waiting' => $waitingoption]),
                                                       'chosenevent',
                                                       $eventselect,
                                                       $event->id);
            $select->formid = 'chooseevent'.$row->id;
            return html_writer::tag('div',
                                    $OUTPUT->render($select),
                                    ['id' => 'iomad_event_selector']);
        }
    }

    /**
     * Generate the display of the user's| fullname
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_action($row) {
        global $params, $id, $waitingoption, $event, $eventselect, $OUTPUT, $numattending, $maxcapacity;

        $actionhtml = "";
        if ($this->is_downloading()) {
            return;
        }
        if (has_capability('mod/trainingevent:add', context_module::instance($id))) {
            if ($waitingoption && $numattending < $maxcapacity) {
                $actionhtml = $OUTPUT->single_button(new moodle_url('view.php',
                                                                     array('userid' => $row->id,
                                                                           'id' => $id,
                                                                           'action' => 'add',
                                                                        'view' => 1 )),
                                                                     get_string("add"));
                $actionhtml .= "&nbsp";
            }
            $actionhtml .= $OUTPUT->single_button(new moodle_url('view.php',
                                                                  ['userid' => $row->id,
                                                                   'id' => $id,
                                                                   'action' => 'delete',
                                                                   'view' => 1,
                                                                   'waiting' => $waitingoption]),
                                                                  get_string("remove", 'trainingevent'));

        }
        return $actionhtml;
    }

    /**
     * Generate the display of the user's| fullname
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_grade($row) {
        global $params, $id, $waitingoption, $event, $eventselect, $OUTPUT, $numattending, $maxcapacity;

        $gradehtml = "";
        $usergradeentry = grade_get_grades($event->course, 'mod', 'trainingevent', $event->id, $row->id);

        if ($this->is_downloading()) {
            return $usergradeentry->items[0]->grades[$row->id]->str_grade;
        }

        if (has_capability('mod/trainingevent:grade', context_module::instance($id)) && $waitingoption == 0) {
            $gradehtml = '<input type="hidden" name="id" value="' . $id . '" />
                         <input type="hidden" name="usergradeusers[]" value="'.$row->id.'" />
                         <input type="hidden" name="action" value="grade" />
                         <input type="hidden" name="view" value="1" />
                         <input type="text" name="usergrades[]" id="id_usergrade"
                                value="'.$usergradeentry->items[0]->grades[$row->id]->str_grade.'" />
                         <input type="submit" value="' . get_string('grade', 'grades') . '" />';

        }

        return $gradehtml;
    }

    /**
     * Generate the display of the user's lastname
     * @param object $user the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_department($row) {
        global $CFG, $DB;

        $userdepartments = $DB->get_records_sql("select d.* FROM {department} d JOIN {company_users} cu ON (d.id = cu.departmentid)
                                                 WHERE cu.userid = :userid
                                                 AND cu.companyid = :companyid",
                                                 ['userid' => $row->id,
                                                  'companyid' => $row->companyid]);
        $count = count($userdepartments);
        $current = 1;
        $returnstr = "";
        if ($count > 5) {
            $returnstr = "<details><summary>" . get_string('show') . "</summary>";
        }

        $first = true;
        foreach($userdepartments as $department) {
            $returnstr .= format_string($department->name);

            if ($current < $count) {
                $returnstr .= ",<br>";
            }
            $current++;
        }

        if ($count > 5) {
            $returnstr .= "</details>";
        }

        return $returnstr;
    }

    public function wrap_html_start() {
        global $params, $id, $waitingoption;

        if (has_capability('mod/trainingevent:grade', context_module::instance($id)) && $waitingoption == 0) {
            echo '<form action="view.php" method="get">';
        }
    }

    public function wrap_html_finish() {
        global $params, $id, $waitingoption;

        if (has_capability('mod/trainingevent:grade', context_module::instance($id)) && $waitingoption == 0) {
            echo '<br><input type="submit" value="' . get_string('grade', 'grades') . '" />
                  </form>';
        }
    }
}