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
 * Course trainingsessions report
 *
 * @package    report_trainingsessions
 * @category   report
 * @version    moodle 2.x
 * @author     Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/formslib.php');
require_once($CFG->dirroot.'/report/trainingsessions/__other/elementgrid.php');

class SelectorForm extends moodleform {

    protected $courseid;
    protected $mode;

    public function __construct($courseid, $mode = 'user') {
        $this->courseid = $courseid;
        $this->mode = $mode;
        parent::__construct();
    }

    public function definition() {
        global $USER;

        $config = get_config('report_trainingsessions');

        $mform = $this->_form;

        $mform->addElement('hidden', 'id', $this->courseid);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'view', $this->mode);
        $mform->setType('view', PARAM_TEXT);

        $mform->addElement('hidden', 'output', 'html');
        $mform->setType('output', PARAM_TEXT);

        $grid = &$mform->addElement('elementgrid', 'grid', '', '');

        $titles = array();
        $row = array();
        $row2 = array();

        $dateparms = array(
            'startyear' => 2008,
            'stopyear'  => 2020,
            'timezone'  => 99,
            'applydst'  => true,
            'optional'  => false
        );
        $titles[] = get_string('from');
        $row[] = & $mform->createElement('date_selector', 'from', '', $dateparms);

        $titles[] = get_string('to');
        $row[] = & $mform->createElement('date_selector', 'to', '', $dateparms);

        $context = context_course::instance($this->courseid);

        $allgroupaccess = has_capability('moodle/site:accessallgroups', $context, $USER->id);
        $mygroups = groups_get_my_groups();

        if ($this->mode == 'user' || $this->mode == 'allcourses') {

            if (has_capability('report/trainingsessions:viewother', $context)) {
                $users = get_enrolled_users($context, '', 0, 'u.*', 'u.lastname,u.firstname', 0, 0, $config->disablesuspendedenrolments);
                $useroptions = array();

                foreach ($users as $user) {
                    if (!has_capability('report/trainingsessions:iscompiled', $context, $user->id, false)) {
                        continue;
                    }

                    if (!empty($config->disablesuspendedstudents) && $user->suspended) {
                        continue;
                    }

                    if (!$allgroupaccess) {
                        $keep = false;
                        foreach ($mygroups as $g) { // Is the user in my groups ?
                            if (groups_is_member($g->id, $user->id)) {
                                $keep = true;
                            }
                        }
                        if (!$keep) {
                            continue;
                        }
                    }

                    $useroptions[$user->id] = $user->lastname.' '.$user->firstname;
                    if (!array_key_exists($USER->id, $useroptions)) {
                        /*
                         * In some case, you may also want to see your data even if NOT
                         * primarily concerned with reports.
                         */
                        $useroptions[$USER->id] = fullname($USER);
                    }
                }
                $titles[] = get_string('user');
                $row[] = & $mform->createElement('select', 'userid', '', $useroptions);
            } else {
                $mform->addElement('hidden', 'userid', $USER->id);
                $mform->setType('userid', PARAM_INT);
            }
        } else {
            $groups = groups_get_all_groups($this->courseid);

            $groupoptions = array();
            if ($allgroupaccess) {
                $groupoptions[0] = get_string('allgroups');
            }
            foreach ($groups as $g) {
                if ($allgroupaccess || groups_is_member($g->id, $USER->id)) {
                    $groupoptions[$g->id] = $g->name;
                }
            }
            $titles[] = get_string('group');
            $row[] = & $mform->createElement('select', 'groupid', '', $groupoptions);

        }
        $updatestr = get_string('updatefromcoursestart', 'report_trainingsessions');
        $updatestr2 = get_string('updatefromaccountstart', 'report_trainingsessions');
        $updatefromstr = ($this->mode == 'user') ? $updatestr : $updatestr2;
        $updatetostr = get_string('tonow', 'report_trainingsessions');
        $debugmodestr = get_string('debugmode', 'report_trainingsessions');

        $row[] = $mform->createElement('submit', 'go_btn', get_string('update'));

        $row2[] = & $mform->createElement('checkbox', 'fromstart', '', $updatefromstr);
        $row2[] = & $mform->createElement('checkbox', 'tonow', '', $updatetostr);
        if (has_capability('moodle/site:config', $context)) {
            $row2[] = & $mform->createElement('checkbox', 'debug', '', $debugmodestr);
        }
        $row2[] = $mform->createElement('html', ''); // This stands for an empty cell, but needs being a Quickform object.
        $row2[] = $mform->createElement('html', ''); // This stands for an empty cell, but needs being a Quickform object.

        $grid->setColumnNames($titles);
        $grid->setColumnWidths(array('30%', '30%', '25%', '15%'));
        $grid->addRow($row);
        $grid->addRow($row2);

        $mform->disabledIf('from[\'day\']', 'fromstart', 'checked');
        $mform->disabledIf('to[\'day\']', 'tonow', 'checked');
    }
}