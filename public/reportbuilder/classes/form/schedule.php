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
use core_user;
use html_writer;
use moodle_url;
use core\output\notification;
use core_form\dynamic_form;
use core_reportbuilder\{manager, permission};
use core_reportbuilder\local\helpers\{audience, schedule as helper};
use core_reportbuilder\local\models\schedule as model;
use core_reportbuilder\local\schedules\base;
use core_reportbuilder\local\report\base as report_base;

/**
 * Schedule form
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class schedule extends dynamic_form {

    /**
     * Return schedule instance
     *
     * @return base
     */
    private function get_schedule(): base {
        $reportid = $this->optional_param('reportid', 0, PARAM_INT);
        $scheduleid = $this->optional_param('id', 0, PARAM_INT);

        if ($scheduleid > 0) {
            $schedule = model::get_record(['id' => $scheduleid, 'reportid' => $reportid], MUST_EXIST);
            return base::from_persistent($schedule);
        } else {
            /** @var base $scheduleclass */
            $scheduleclass = $this->optional_param('classname', '', PARAM_RAW);
            return $scheduleclass::instance();
        }
    }

    /**
     * Return instance of the system report using the filter form
     *
     * @return report_base
     */
    private function get_report(): report_base {
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
     * A {@see \core_reportbuilder\exception\report_access_exception} will be thrown if they can't
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

        $mform->addElement('hidden', 'classname');
        $mform->setType('classname', PARAM_RAW);

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
        $schedule = $this->get_schedule();
        if ($schedule->requires_audience()) {
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
        }

        // Load schedule type form definition.
        $schedule->definition($mform);
    }

    /**
     * Load form data if we are editing an existing schedule
     */
    public function set_data_for_dynamic_submission(): void {
        $schedule = $this->get_schedule();

        if ($schedule->get_persistent()->get('id') > 0) {
            $data = (array) $schedule->get_persistent()->to_record();

            // Pre-process some of the form fields.
            if (!in_array($data['userviewas'], [model::REPORT_VIEWAS_CREATOR, model::REPORT_VIEWAS_RECIPIENT])) {
                $data['user'] = $data['userviewas'];
                $data['userviewas'] = model::REPORT_VIEWAS_USER;
            }

            if ($schedule->requires_audience()) {
                $audiences = (array) json_decode((string) $data['audiences']);
                $data['audiences'] = array_fill_keys($audiences, 1);
            }

            // Load schedule type form definition data.
            $data['configdata'] = $schedule->get_configdata();

            $this->set_data($data);
        } else {
            $reportid = $this->optional_param('reportid', 0, PARAM_INT);
            $this->set_data(['reportid' => $reportid, 'classname' => $schedule::class]);
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

        // Load schedule type form validation.
        $schedule = $this->get_schedule();
        if ($schedule->requires_audience() && empty($data['audiences'])) {
            $errors['audiences'] = get_string('required');
        }

        return array_merge($errors, $schedule->validate($data, $files));
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

        $schedule = $this->get_schedule();

        if ($schedule->requires_audience()) {
            $data->audiences = json_encode(array_keys($data->audiences));
        } else {
            $data->audiences = null;
        }

        $data->configdata = json_encode((array) $data->configdata);

        if ($schedule->get_persistent()->get('id') > 0) {
            helper::update_schedule($data);
        } else {
            unset($data->id);
            $schedule::create($data);
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
