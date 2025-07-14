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

use core\context;
use core_form\dynamic_form;
use core_reportbuilder\local\helpers\report;
use core_reportbuilder\local\models\report as report_model;
use core_reportbuilder\permission;
use core\url;

/**
 * Dynamic duplicate custom reports form
 *
 * @package     core_reportbuilder
 * @copyright   2024 David Carrillo <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class duplicate_report extends dynamic_form {

    /**
     * Form definition.
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('text', 'name', get_string('name', 'core'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', get_string('required'), 'required');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255);

        $mform->addElement('advcheckbox', 'audiences', get_string('duplicateaudiences', 'core_reportbuilder'));
        $mform->addElement('advcheckbox', 'schedules', get_string('duplicateschedules', 'core_reportbuilder'));
        $mform->disabledIf('schedules', 'audiences', 'notchecked');
    }

    /**
     * Returns context where this form is used
     *
     * @return context
     */
    protected function get_context_for_dynamic_submission(): context {
        $report = report_model::get_record(['id' => $this->optional_param('id', 0, PARAM_INT)], MUST_EXIST);
        return $report->get_context();
    }

    /**
     * Ensure current user is able to use this form
     *
     * A {@see \core_reportbuilder\exception\report_access_exception} will be thrown if they can't
     */
    protected function check_access_for_dynamic_submission(): void {
        $report = report_model::get_record(['id' => $this->optional_param('id', 0, PARAM_INT)], MUST_EXIST);
        permission::require_can_duplicate_report($report);
    }

    /**
     * Process the form submission, used if form was submitted via AJAX
     */
    public function process_dynamic_submission(): string {
        $data = $this->get_data();
        $report = report_model::get_record(['id' => $data->id], MUST_EXIST);

        $newreport = report::duplicate_report(
            $report,
            $data->name,
            !empty($data->audiences),
            !empty($data->schedules),
        );

        return (new url('/reportbuilder/edit.php', ['id' => $newreport->get('id')]))->out(false);
    }

    /**
     * Load in existing data as form defaults
     */
    public function set_data_for_dynamic_submission(): void {
        $this->set_data($this->_ajaxformdata);
    }

    /**
     * Page url
     *
     * @return url
     */
    protected function get_page_url_for_dynamic_submission(): url {
        return new url('/reportbuilder/edit.php', ['id' => $this->optional_param('id', 0, PARAM_INT)]);
    }
}
