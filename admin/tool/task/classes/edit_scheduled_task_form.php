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
        global $PAGE;

        $mform = $this->_form;
        /** @var \core\task\scheduled_task $task */
        $task = $this->_customdata;
        $defaulttask = \core\task\manager::get_default_scheduled_task(get_class($task), false);
        $renderer = $PAGE->get_renderer('tool_task');

        $mform->addElement('static', 'lastrun', get_string('lastruntime', 'tool_task'),
                $renderer->last_run_time($task));

        $mform->addElement('static', 'nextrun', get_string('nextruntime', 'tool_task'),
                $renderer->next_run_time($task));

        $mform->addGroup([
                $mform->createElement('text', 'minute'),
                $mform->createElement('static', 'minutedefault', '',
                        get_string('defaultx', 'tool_task', $defaulttask->get_minute())),
            ], 'minutegroup', get_string('taskscheduleminute', 'tool_task'), null, false);
        $mform->setType('minute', PARAM_RAW);
        $mform->addHelpButton('minutegroup', 'taskscheduleminute', 'tool_task');

        $mform->addGroup([
                $mform->createElement('text', 'hour'),
                $mform->createElement('static', 'hourdefault', '',
                        get_string('defaultx', 'tool_task', $defaulttask->get_hour())),
        ], 'hourgroup', get_string('taskschedulehour', 'tool_task'), null, false);
        $mform->setType('hour', PARAM_RAW);
        $mform->addHelpButton('hourgroup', 'taskschedulehour', 'tool_task');

        $mform->addGroup([
                $mform->createElement('text', 'day'),
                $mform->createElement('static', 'daydefault', '',
                        get_string('defaultx', 'tool_task', $defaulttask->get_day())),
        ], 'daygroup', get_string('taskscheduleday', 'tool_task'), null, false);
        $mform->setType('day', PARAM_RAW);
        $mform->addHelpButton('daygroup', 'taskscheduleday', 'tool_task');

        $mform->addGroup([
                $mform->createElement('text', 'month'),
                $mform->createElement('static', 'monthdefault', '',
                        get_string('defaultx', 'tool_task', $defaulttask->get_month())),
        ], 'monthgroup', get_string('taskschedulemonth', 'tool_task'), null, false);
        $mform->setType('month', PARAM_RAW);
        $mform->addHelpButton('monthgroup', 'taskschedulemonth', 'tool_task');

        $mform->addGroup([
                $mform->createElement('text', 'dayofweek'),
                $mform->createElement('static', 'dayofweekdefault', '',
                        get_string('defaultx', 'tool_task', $defaulttask->get_day_of_week())),
        ], 'dayofweekgroup', get_string('taskscheduledayofweek', 'tool_task'), null, false);
        $mform->setType('dayofweek', PARAM_RAW);
        $mform->addHelpButton('dayofweekgroup', 'taskscheduledayofweek', 'tool_task');

        $mform->addElement('advcheckbox', 'disabled', get_string('disabled', 'tool_task'));
        $mform->addHelpButton('disabled', 'disabled', 'tool_task');

        $mform->addElement('advcheckbox', 'resettodefaults', get_string('resettasktodefaults', 'tool_task'));
        $mform->addHelpButton('resettodefaults', 'resettasktodefaults', 'tool_task');

        $mform->disabledIf('minute', 'resettodefaults', 'checked');
        $mform->disabledIf('hour', 'resettodefaults', 'checked');
        $mform->disabledIf('day', 'resettodefaults', 'checked');
        $mform->disabledIf('dayofweek', 'resettodefaults', 'checked');
        $mform->disabledIf('month', 'resettodefaults', 'checked');
        $mform->disabledIf('disabled', 'resettodefaults', 'checked');

        $mform->addElement('hidden', 'task', get_class($task));
        $mform->setType('task', PARAM_RAW);
        $mform->addElement('hidden', 'action', 'edit');
        $mform->setType('action', PARAM_ALPHANUMEXT);
        $this->add_action_buttons(true, get_string('savechanges'));

        // Do not use defaults for existing values, the set_data() is the correct way.
        $this->set_data(\core\task\manager::record_from_scheduled_task($task));
    }

    /**
     * Custom validations.
     *
     * @param array $data
     * @param array $files
     *
     * @return array
     */
    public function validation($data, $files) {
        $error = parent::validation($data, $files);
        // Use a checker class.
        $checker = new \tool_task\scheduled_checker_task();
        $checker->set_minute($data['minute']);
        $checker->set_hour($data['hour']);
        $checker->set_month($data['month']);
        $checker->set_day_of_week($data['dayofweek']);
        $checker->set_day($data['day']);
        $checker->set_disabled(false);
        $checker->set_customised(false);

        if (!$checker->is_valid($checker::FIELD_MINUTE)) {
            $error['minutegroup'] = get_string('invaliddata', 'core_error');
        }
        if (!$checker->is_valid($checker::FIELD_HOUR)) {
            $error['hourgroup'] = get_string('invaliddata', 'core_error');
        }
        if (!$checker->is_valid($checker::FIELD_DAY)) {
            $error['daygroup'] = get_string('invaliddata', 'core_error');
        }
        if (!$checker->is_valid($checker::FIELD_MONTH)) {
            $error['monthgroup'] = get_string('invaliddata', 'core_error');
        }
        if (!$checker->is_valid($checker::FIELD_DAYOFWEEK)) {
            $error['dayofweekgroup'] = get_string('invaliddata', 'core_error');
        }

        return $error;
    }
}

