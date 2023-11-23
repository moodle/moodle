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
use core_reportbuilder\permission;
use moodle_url;
use core_form\dynamic_form;
use core_reportbuilder\datasource;
use core_reportbuilder\manager;
use core_reportbuilder\local\helpers\report as reporthelper;
use core_tag_tag;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("$CFG->libdir/formslib.php");

/**
 * Report details form
 *
 * @package     core_reportbuilder
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report extends dynamic_form {

    /**
     * Return instance of the custom report we are editing, or null when creating a new report
     *
     * @return datasource|null
     */
    protected function get_custom_report(): ?datasource {
        if ($reportid = $this->optional_param('id', 0, PARAM_INT)) {
            /** @var datasource $customreport */
            $customreport = manager::get_report_from_id($reportid);

            return $customreport;
        }

        return null;
    }

    /**
     * Return the context for the form, it should be that of the custom report itself, or system when creating a new report
     *
     * @return context
     */
    public function get_context_for_dynamic_submission(): context {
        if ($report = $this->get_custom_report()) {
            return $report->get_context();
        } else {
            return context_system::instance();
        }
    }

    /**
     * Ensure current user is able to use this form
     *
     * A {@see \core_reportbuilder\report_access_exception} will be thrown if they can't
     */
    protected function check_access_for_dynamic_submission(): void {
        $report = $this->get_custom_report();

        if ($report) {
            permission::require_can_edit_report($report->get_report_persistent());
        } else {
            permission::require_can_create_report();
        }
    }

    /**
     * Form definition
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('text', 'name', get_string('name'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255);

        // Allow user to select report source if creating a new report.
        if (!$this->get_custom_report()) {
            $default = ['' => ['' => get_string('selectareportsource', 'core_reportbuilder')]];
            $mform->addElement('selectgroups', 'source', get_string('reportsource', 'core_reportbuilder'),
                array_merge($default, manager::get_report_datasources()));
            $mform->addRule('source', null, 'required', null, 'client');
            $mform->addHelpButton('source', 'reportsource', 'core_reportbuilder');

            $mform->addElement('advcheckbox', 'includedefaultsetup', get_string('includedefaultsetup', 'core_reportbuilder'));
            $mform->setDefault('includedefaultsetup', 1);
            $mform->addHelpButton('includedefaultsetup', 'includedefaultsetup', 'core_reportbuilder');
        }

        $mform->addElement('advcheckbox', 'uniquerows', get_string('uniquerows', 'core_reportbuilder'));
        $mform->addHelpButton('uniquerows', 'uniquerows', 'core_reportbuilder');

        $mform->addElement('tags', 'tags', get_string('tags'), [
            'component' => 'core_reportbuilder', 'itemtype' => 'reportbuilder_report',
        ]);
    }

    /**
     * Process the form submission
     *
     * @return string The URL to advance to upon completion
     */
    public function process_dynamic_submission() {
        $data = $this->get_data();

        if ($data->id) {
            $reportpersistent = reporthelper::update_report($data);
        } else {
            $reportpersistent = reporthelper::create_report($data, (bool)$data->includedefaultsetup);
        }

        return (new moodle_url('/reportbuilder/edit.php', ['id' => $reportpersistent->get('id')]))->out(false);
    }

    /**
     * Load in existing data as form defaults
     */
    public function set_data_for_dynamic_submission(): void {
        if ($persistent = $this->get_custom_report()?->get_report_persistent()) {
            $tags = core_tag_tag::get_item_tags_array('core_reportbuilder', 'reportbuilder_report', $persistent->get('id'));
            $this->set_data(array_merge((array) $persistent->to_record(), ['tags' => $tags]));
        }
    }

    /**
     * URL of the page using this form
     *
     * @return moodle_url
     */
    public function get_page_url_for_dynamic_submission(): moodle_url {
        return new moodle_url('/reportbuilder/index.php');
    }

    /**
     * Perform some extra moodle validation
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files): array {
        $errors = [];

        if (trim($data['name']) === '') {
            $errors['name'] = get_string('required');
        }

        return $errors;
    }
}
