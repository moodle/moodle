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
 * Form for scheduled tasks admin pages.
 *
 * @package    tool_task
 * @copyright  2013 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

/**
 * Edit scheduled task form.
 *
 * @copyright  2013 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_task_edit_scheduled_task_form extends moodleform {
    public function definition() {
        global $CFG;

        $mform = $this->_form;
        $task = $this->_customdata;

        $never = get_string('never');
        $none = get_string('none');
        $lastrun = $task->get_last_run_time() ? userdate($task->get_last_run_time()) : $never;
        $nextrun = $task->get_next_run_time() ? userdate($task->get_next_run_time()) : $none;
        $mform->addElement('static', 'lastrun', get_string('lastruntime', 'tool_task'), $lastrun);
        $mform->addElement('static', 'nextrun', get_string('nextruntime', 'tool_task'), $nextrun);

        $mform->addElement('text', 'minute', get_string('taskscheduleminute', 'tool_task'));
        $mform->setType('minute', PARAM_RAW);
        $mform->addHelpButton('minute', 'taskscheduleminute', 'tool_task');
        $mform->setDefault('minute', $task->get_minute());

        $mform->addElement('text', 'hour', get_string('taskschedulehour', 'tool_task'));
        $mform->setType('hour', PARAM_RAW);
        $mform->addHelpButton('hour', 'taskschedulehour', 'tool_task');
        $mform->setDefault('hour', $task->get_hour());

        $mform->addElement('text', 'day', get_string('taskscheduleday', 'tool_task'));
        $mform->setType('day', PARAM_RAW);
        $mform->addHelpButton('day', 'taskscheduleday', 'tool_task');
        $mform->setDefault('day', $task->get_day());

        $mform->addElement('text', 'month', get_string('taskschedulemonth', 'tool_task'));
        $mform->setType('month', PARAM_RAW);
        $mform->addHelpButton('month', 'taskschedulemonth', 'tool_task');
        $mform->setDefault('month', $task->get_month());

        $mform->addElement('text', 'dayofweek', get_string('taskscheduledayofweek', 'tool_task'));
        $mform->setType('dayofweek', PARAM_RAW);
        $mform->addHelpButton('dayofweek', 'taskscheduledayofweek', 'tool_task');
        $mform->setDefault('dayofweek', $task->get_day_of_week());

        $mform->addElement('advcheckbox', 'resettodefaults', get_string('resettasktodefaults', 'tool_task'));
        $mform->addHelpButton('resettodefaults', 'resettasktodefaults', 'tool_task');

        $mform->disabledIf('minute', 'resettodefaults', 'checked');
        $mform->disabledIf('hour', 'resettodefaults', 'checked');
        $mform->disabledIf('day', 'resettodefaults', 'checked');
        $mform->disabledIf('dayofweek', 'resettodefaults', 'checked');
        $mform->disabledIf('month', 'resettodefaults', 'checked');

        $mform->addElement('hidden', 'task', get_class($task));
        $mform->setType('task', PARAM_RAW);
        $mform->addElement('hidden', 'action', 'edit');
        $mform->setType('action', PARAM_ALPHANUMEXT);
        $this->add_action_buttons(true, get_string('savechanges'));
    }
}

