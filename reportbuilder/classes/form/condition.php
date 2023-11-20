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
use moodle_url;
use core_form\dynamic_form;
use core_reportbuilder\manager;
use core_reportbuilder\permission;
use core_reportbuilder\local\report\base;
use core_reportbuilder\local\models\report;

/**
 * Dynamic condition form
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class condition extends dynamic_form {

    /**
     * Return instance of the report using the condition form
     *
     * @return base
     */
    private function get_report(): base {
        $report = new report($this->optional_param('reportid', 0, PARAM_INT));
        $parameters = (array) json_decode($this->optional_param('parameters', '', PARAM_RAW));

        return manager::get_report_from_persistent($report, $parameters);
    }

    /**
     * Return the context for the form, it should be that of the system report itself
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
        permission::require_can_edit_report($this->get_report()->get_report_persistent());
    }

    /**
     * Process the form submission
     *
     * @return bool
     */
    public function process_dynamic_submission() {
        $values = $this->get_data();

        // Remove some unneeded fields.
        unset($values->reportid, $values->parameters);

        return $this->get_report()->set_condition_values((array) $values);
    }

    /**
     * Load in existing data as form defaults
     */
    public function set_data_for_dynamic_submission(): void {
        $defaults = [
            'reportid' => $this->optional_param('reportid', 0, PARAM_INT),
            'parameters' => $this->optional_param('parameters', 0, PARAM_RAW),
        ];

        $this->set_data(array_merge($defaults, $this->get_report()->get_condition_values()));
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
     * Condition form definition
     */
    protected function definition() {
        global $OUTPUT;

        $mform = $this->_form;

        $mform->addElement('hidden', 'reportid');
        $mform->setType('reportid', PARAM_INT);

        $mform->addElement('hidden', 'parameters');
        $mform->setType('parameters', PARAM_RAW);

        // Wrap the form elements inside an outer container, as drag/drop requires draggable elements to be immediate
        // descendants of said container. Note this is identified by it's data-region property in the editor module.
        $mform->addElement('html', '<div class="list-group mt-2" data-region="active-conditions">');

        // Allow each condition instance to add itself to this form, wrapping each inside custom header/footer template.
        $conditioninstances = $this->get_report()->get_condition_instances();
        foreach ($conditioninstances as $conditioninstance) {
            $persistent = $conditioninstance->get_filter_persistent();

            $entityname = $conditioninstance->get_entity_name();
            $displayvalue = $conditioninstance->get_header();

            $mform->addElement('html', $OUTPUT->render_from_template('core_reportbuilder/local/conditions/header', [
                'id' => $persistent->get('id'),
                'entityname' => $this->get_report()->get_entity_title($entityname),
                'heading' => $displayvalue,
                'sortorder' => $persistent->get('filterorder'),
                'movetitle' => get_string('movecondition', 'core_reportbuilder', $displayvalue),
            ]));

            $conditioninstance->setup_form($mform);
            $mform->addElement('html', $OUTPUT->render_from_template('core_reportbuilder/local/conditions/footer', []));
        }
        $mform->addElement('html', '</div>');
        $this->set_display_vertical();

        // We'll add a second submit button to the form that will be used to reset current report conditions.
        $mform->registerNoSubmitButton('resetconditions');

        $buttons = [];
        $buttons[] = $mform->createElement('submit', 'submitbutton', get_string('apply', 'core_reportbuilder'));
        $buttons[] = $mform->createElement('submit', 'resetconditions',  get_string('resetall', 'core_reportbuilder'),
            null, null, ['customclassoverride' => 'btn-link ml-1']);

        $mform->addGroup($buttons, 'buttonar', get_string('formactions', 'core_form'), '', false)
            ->setHiddenLabel(true);
        $mform->closeHeaderBefore('buttonar');

        $mform->disable_form_change_checker();
    }
}
