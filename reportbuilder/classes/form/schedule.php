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

declare(strict_types=1);

namespace core_reportbuilder\form;

use context;
use context_system;
use core_user;
use html_writer;
use moodle_url;
use core\output\notification;
use core_form\dynamic_form;
use core_reportbuilder\manager;
use core_reportbuilder\permission;
use core_reportbuilder\local\helpers\audience;
use core_reportbuilder\local\helpers\schedule as helper;
use core_reportbuilder\local\models\schedule as model;
use core_reportbuilder\local\report\base;

/**
 * Schedule form
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class schedule extends dynamic_form {

    /**
     * Return instance of the system report using the filter form
     *
     * @return base
     */
    private function get_report(): base {
        $reportid = $this->optional_param('reportid', 0, PARAM_INT);
        return manager::get_report_from_id($reportid);
    }

    /**
     * Return the context for the form, it should be that of the report itself
     *
     * @return context
     */
    protected function get_context_for_dynamic_submission(): context {
        return $this->get_report()->get_context();
    }

    /**
     * Ensure current user is able to use this form
     *
     * A {@see \core_reportbuilder\report_access_exception} will be thrown if they can't
     */
    protected function check_access_for_dynamic_submission(): void {
        $persistent = $this->get_report()->get_report_persistent();
        permission::require_can_edit_report($persistent);
    }

    /**
     * Form definition
     */
    protected function definition() {
        global $OUTPUT;

        $mform = $this->_form;

        $mform->addElement('hidden', 'reportid');
        $mform->setType('reportid', PARAM_INT);

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        // General fields.
        $mform->addElement('header', 'headergeneral', get_string('general'));

        $mform->addElement('text', 'name', get_string('name'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255);

        $mform->addElement('select', 'format', get_string('format'), helper::get_format_options());
        $mform->setType('format', PARAM_PLUGIN);

        $mform->addElement('date_time_selector', 'timescheduled', get_string('startingfrom'), ['optional' => false]);
        $mform->setType('timescheduled', PARAM_INT);

        $mform->addElement('select', 'recurrence', get_string('recurrence', 'core_reportbuilder'),
            helper::get_recurrence_options());
        $mform->setType('recurrence', PARAM_INT);

        // View report data as.
        $context = $this->get_context_for_dynamic_submission();
        if (has_capability('moodle/reportbuilder:scheduleviewas', $context)) {
            $mform->addElement('select', 'userviewas', get_string('scheduleviewas', 'core_reportbuilder'),
                helper::get_viewas_options());
            $mform->setType('userviewas', PARAM_INT);

            $options = [
                'ajax' => 'core_user/form_user_selector',
                'multiple' => false,
                'valuehtmlcallback' => function($userid) use ($context): string {
                    $user = core_user::get_user($userid);
                    return fullname($user, has_capability('moodle/site:viewfullnames', $context));
                }
            ];
            $mform->addElement('autocomplete', 'user', get_string('user'), [], $options)->setHiddenLabel(true);
            $mform->hideIf('user', 'userviewas', 'neq', model::REPORT_VIEWAS_USER);
        }

        // Audience fields.
        $mform->addElement('header', 'headeraudience', get_string('audience', 'core_reportbuilder'));
        $mform->setExpanded('headeraudience', true);

        $audiences = audience::get_base_records($this->optional_param('reportid', 0, PARAM_INT));
        if (empty($audiences)) {
            $notification = new notification(get_string('noaudiences', 'core_reportbuilder'), notification::NOTIFY_INFO, false);
            $mform->addElement('static', 'noaudiences', '', $OUTPUT->render($notification));
        }

        $audiencecheckboxes = [];
        foreach ($audiences as $audience) {
            $persistent = $audience->get_persistent();

            // Check for a custom name, otherwise fall back to default.
            if ('' === $audiencelabel = $persistent->get_formatted_heading($context)) {
                $audiencelabel = get_string('audiencelabel', 'core_reportbuilder', (object) [
                    'name' => $audience->get_name(),
                    'description' => $audience->get_description(),
                ]);
            }

            $audiencecheckboxes[] = $mform->createElement('checkbox', $persistent->get('id'), $audiencelabel);
        }

        $mform->addElement('group', 'audiences', '', $audiencecheckboxes, html_writer::div('', 'w-100 mb-2'));

        // Message fields.
        $mform->addElement('header', 'headermessage', get_string('messagecontent', 'core_reportbuilder'));

        $mform->addElement('text', 'subject', get_string('messagesubject', 'core_reportbuilder'));
        $mform->setType('subject', PARAM_TEXT);
        $mform->addRule('subject', null, 'required', null, 'client');
        $mform->addRule('subject', get_string('maximumchars', '', 255), 'maxlength', 255);

        $mform->addElement('editor', 'message', get_string('messagebody', 'core_reportbuilder'), null, ['autosave' => false]);
        $mform->setType('message', PARAM_RAW);
        $mform->addRule('message', null, 'required', null, 'client');

        // Advanced.
        $mform->addElement('header', 'headeradvanced', get_string('advanced'));

        $mform->addElement('select', 'reportempty', get_string('scheduleempty', 'core_reportbuilder'),
            helper::get_report_empty_options());
        $mform->setType('reportempty', PARAM_INT);
    }

    /**
     * Load form data if we are editing an existing schedule
     */
    public function set_data_for_dynamic_submission(): void {
        $reportid = $this->optional_param('reportid', 0, PARAM_INT);
        $scheduleid = $this->optional_param('id', 0, PARAM_INT);

        if ($scheduleid > 0) {
            $schedule = model::get_record(['id' => $scheduleid, 'reportid' => $reportid]);

            $data = (array) $schedule->to_record();

            // Pre-process some of the form fields.
            if (!in_array($data['userviewas'], [model::REPORT_VIEWAS_CREATOR, model::REPORT_VIEWAS_RECIPIENT])) {
                $data['user'] = $data['userviewas'];
                $data['userviewas'] = model::REPORT_VIEWAS_USER;
            }

            $audiences = json_decode($data['audiences']);
            $data['audiences'] = array_fill_keys($audiences, 1);

            $data['message'] = [
                'text' => $data['message'],
                'format' => $data['messageformat'],
            ];

            $this->set_data($data);
        } else {
            $this->set_data(['reportid' => $reportid]);
        }
    }

    /**
     * Form validation
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);

        if (trim($data['name']) === '') {
            $errors['name'] = get_string('required');
        }

        // Make sure specific user was selected, if required.
        if (array_key_exists('userviewas', $data) &&
                (int) $data['userviewas'] === model::REPORT_VIEWAS_USER && empty($data['user'])) {

            $errors['user'] = get_string('required');
        }

        if (empty($data['audiences'])) {
            $errors['audiences'] = get_string('required');
        }

        return $errors;
    }

    /**
     * Process form submission
     */
    public function process_dynamic_submission(): void {
        $data = $this->get_data();

        // Pre-process some of the form fields.
        if (property_exists($data, 'userviewas') && (int) $data->userviewas === model::REPORT_VIEWAS_USER) {
            $data->userviewas = (int) $data->user;
        }

        $data->audiences = json_encode(array_keys($data->audiences));
        ['text' => $data->message, 'format' => $data->messageformat] = $data->message;

        if ($data->id) {
            helper::update_schedule($data);
        } else {
            helper::create_schedule($data);
        }
    }

    /**
     * URL of the page using this form
     *
     * @return moodle_url
     */
    protected function get_page_url_for_dynamic_submission(): moodle_url {
        return new moodle_url('/reportbuilder/edit.php', ['id' => $this->optional_param('reportid', 0, PARAM_INT)], 'schedules');
    }
}
