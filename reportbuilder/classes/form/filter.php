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
use core_reportbuilder\local\report\base;
use core_reportbuilder\permission;
use moodle_url;
use core_form\dynamic_form;
use core_reportbuilder\manager;
use core_reportbuilder\local\models\report;
use core_reportbuilder\local\models\filter as filter_model;

/**
 * Dynamic filter form
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter extends dynamic_form {

    /**
     * Return instance of the system report using the filter form
     *
     * @return base
     */
    private function get_report(): base {
        $reportpersistent = new report($this->optional_param('reportid', 0, PARAM_INT));
        $parameters = (array) json_decode($this->optional_param('parameters', '', PARAM_RAW));

        return manager::get_report_from_persistent($reportpersistent, $parameters);
    }

    /**
     * Return the context for the form, it should be that of the system report itself
     *
     * @return context
     */
    protected function get_context_for_dynamic_submission(): context {
        return ($this->get_report())->get_context();
    }

    /**
     * Ensure current user is able to use this form
     *
     * A {@see \core_reportbuilder\report_access_exception} will be thrown if they can't
     */
    protected function check_access_for_dynamic_submission(): void {
        $reportpersistent = $this->get_report()->get_report_persistent();
        if ($reportpersistent->get('type') === base::TYPE_CUSTOM_REPORT) {
            permission::require_can_view_report($reportpersistent);
        } else {
            $this->get_report()->require_can_view();
        }
    }

    /**
     * Process the form submission
     *
     * @return int Number of applied filter instances
     */
    public function process_dynamic_submission() {
        $values = $this->get_data();

        // Remove some unneeded fields, apply filters.
        unset($values->reportid, $values->parameters);
        $this->get_report()->set_filter_values((array) $values);

        return $this->get_report()->get_applied_filter_count();
    }

    /**
     * Load in existing data as form defaults
     */
    public function set_data_for_dynamic_submission(): void {
        $defaults = [
            'reportid' => $this->optional_param('reportid', 0, PARAM_INT),
            'parameters' => $this->optional_param('parameters', 0, PARAM_RAW),
        ];

        $this->set_data(array_merge($defaults, $this->get_report()->get_filter_values()));
    }

    /**
     * URL of the page using this form
     *
     * @return moodle_url
     */
    protected function get_page_url_for_dynamic_submission(): moodle_url {
        return new moodle_url('/');
    }

    /**
     * Filter form definition. It should provide necessary field itself, then allow all report filters to add their own elements
     */
    protected function definition() {
        global $OUTPUT;

        $mform = $this->_form;

        $mform->addElement('hidden', 'reportid');
        $mform->setType('reportid', PARAM_INT);

        $mform->addElement('hidden', 'parameters');
        $mform->setType('parameters', PARAM_RAW);

        // Allow each filter instance to add itself to this form, wrapping each inside custom header/footer template.
        $filterinstances = $this->get_report()->get_filter_instances();
        foreach ($filterinstances as $filterinstance) {
            $header = $filterinstance->get_header();

            // Check if filter has a custom header set.
            if ($persistent = $filterinstance->get_filter_persistent()) {
                if ('' !== (string) $persistent->get('heading')) {
                    $header = $persistent->get_formatted_heading($this->get_report()->get_context());
                }
            }

            $mform->addElement('html', $OUTPUT->render_from_template('core_reportbuilder/local/filters/header', [
                'name' => $header,
            ]));

            $filterinstance->setup_form($mform);
            $mform->addElement('html', $OUTPUT->render_from_template('core_reportbuilder/local/filters/footer', []));
        }

        $this->set_display_vertical();

        // We'll add a second submit button to the form that will be used to reset current report filters.
        $mform->registerNoSubmitButton('resetfilters');

        $buttons = [];
        $buttons[] = $mform->createElement('submit', 'submitbutton', get_string('apply', 'core_reportbuilder'));
        $buttons[] = $mform->createElement('submit', 'resetfilters',  get_string('resetall', 'core_reportbuilder'),
            null, null, ['customclassoverride' => 'btn-link']);

        $mform->addGroup($buttons, 'buttonar', '', [' '], false);
        $mform->closeHeaderBefore('buttonar');

        $mform->disable_form_change_checker();
    }
}
