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
 * Card view dynamic form
 *
 * @package     core_reportbuilder
 * @copyright   2021 Mikel Martín <mikel@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class card_view extends dynamic_form {

    /**
     * Return instance of the report using the card view form
     *
     * @return base
     */
    private function get_report(): base {
        $report = new report($this->optional_param('reportid', 0, PARAM_INT));
        $parameters = (array) json_decode($this->optional_param('parameters', '', PARAM_RAW));

        return manager::get_report_from_persistent($report, $parameters);
    }

    /**
     * Returns context where this form is used
     *
     * @return context
     */
    protected function get_context_for_dynamic_submission(): context {
        return $this->get_report()->get_context();
    }

    /**
     * Check if current user has access to this form, otherwise throw exception
     */
    public function check_access_for_dynamic_submission(): void {
        permission::require_can_edit_report($this->get_report()->get_report_persistent());
    }

    /**
     * Store the conditions values and operators
     *
     * @return bool
     */
    public function process_dynamic_submission(): bool {
        $values = $this->get_data();

        $settings = [
            'cardview_showfirsttitle' => (int)$values->showfirsttitle,
            // Minimum value for 'cardview_visiblecolumns' should be 1.
            'cardview_visiblecolumns' => max((int)$values->visiblecolumns, 1)
        ];
        return $this->get_report()->set_settings_values($settings);
    }

    /**
     * Load in existing data as form defaults
     */
    public function set_data_for_dynamic_submission(): void {
        $report = $this->get_report();
        $settings = $report->get_settings_values();

        $defaults = [
            // Maximum value for 'cardview_visiblecolumns' should be the report total number of columns.
            'visiblecolumns' => min($settings['cardview_visiblecolumns'] ?? 1, count($report->get_active_columns())),
            'showfirsttitle' => $settings['cardview_showfirsttitle'] ?? 0,
        ];
        $this->set_data(array_merge($defaults, $this->_ajaxformdata));
    }

    /**
     * Returns url to set in $PAGE->set_url() when form is being rendered or submitted via AJAX
     *
     * @return moodle_url
     */
    protected function get_page_url_for_dynamic_submission(): moodle_url {
        return new moodle_url('/reportbuilder/edit.php');
    }

    /**
     * Card view form definition
     */
    public function definition(): void {
        $report = $this->get_report();

        $mform = $this->_form;

        $mform->addElement('hidden', 'reportid');
        $mform->setType('reportid', PARAM_INT);

        // Generate select options from 1 to report total number of columns.
        $visiblecolumns = range(1, max(count($report->get_active_columns()), 1));
        $mform->addElement('select', 'visiblecolumns', get_string('cardviewvisiblecolumns', 'core_reportbuilder'),
            array_combine($visiblecolumns, $visiblecolumns));
        $mform->setType('visiblecolumns', PARAM_INT);

        $mform->addElement('selectyesno', 'showfirsttitle', get_string('cardviewfirstcolumntitle', 'core_reportbuilder'));
        $mform->setType('showfirsttitle', PARAM_BOOL);

        $mform->disable_form_change_checker();

        $this->add_action_buttons(false);
    }
}
