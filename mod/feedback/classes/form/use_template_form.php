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

namespace mod_feedback\form;

use core_form\dynamic_form;
use moodle_url;
use context;
use context_module;

/**
 * Prints the confirm use template form
 *
 * @copyright 2021 Peter Dias
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_feedback
 */
class use_template_form extends dynamic_form {
    /**
     * Define the form
     */
    public function definition() {
        $mform =& $this->_form;

        $mform->addElement('static', 'generalheader', '', get_string("whatfor", 'feedback'));
        $mform->addElement('radio', 'deleteolditems', '', get_string('delete_old_items', 'feedback'), 1);
        $mform->addElement('radio', 'deleteolditems', '', get_string('append_new_items', 'feedback'), 0);
        $mform->setType('deleteolditems', PARAM_INT);
        $mform->setDefault('deleteolditems', 1);

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'templateid');
        $mform->setType('templateid', PARAM_INT);
    }

    /**
     * Returns context where this form is used
     *
     * @return context
     */
    protected function get_context_for_dynamic_submission(): context {
        $id = $this->optional_param('id', null, PARAM_INT);
        list($course, $cm) = get_course_and_cm_from_cmid($id, 'feedback');
        return context_module::instance($cm->id);
    }

    /**
     * Checks if current user has access to this form, otherwise throws exception
     *
     * @throws \moodle_exception User does not have capability to access the form
     */
    protected function check_access_for_dynamic_submission(): void {
        if (!has_capability('mod/feedback:edititems', $this->get_context_for_dynamic_submission())) {
            throw new \moodle_exception('nocapabilitytousethisservice');
        }
    }

    /**
     * Process the form submission, used if form was submitted via AJAX
     *
     * @return array Returns the following information
     *               - the template was successfully created/updated from the provided template
     *               - the redirect url.
     */
    public function process_dynamic_submission(): array {
        global $PAGE;
        $formdata = $this->get_data();
        $templateid = $this->optional_param('templateid', null, PARAM_INT);
        $id = $this->optional_param('id', null, PARAM_INT);
        $response = feedback_items_from_template($PAGE->activityrecord, $templateid, $formdata->deleteolditems);
        $url = new moodle_url('/mod/feedback/edit.php', ['id' => $id]);

        if ($response !== false) {
            // Provide a notification on success as the user will be redirected.
            \core\notification::add(get_string('feedbackupdated', 'feedback'), \core\notification::SUCCESS);
        }

        return [
            'result' => $response !== false,
            'url' => $url->out()
        ];
    }

    /**
     * Load in existing data as form defaults
     */
    public function set_data_for_dynamic_submission(): void {
        $this->set_data((object)[
            'id' => $this->optional_param('id', null, PARAM_INT),
            'templateid' => $this->optional_param('templateid', null, PARAM_INT)
        ]);
    }

    /**
     * Returns url to set in $PAGE->set_url() when form is being rendered or submitted via AJAX
     *
     * @return moodle_url
     */
    protected function get_page_url_for_dynamic_submission(): moodle_url {
        $params = [
            'id' => $this->optional_param('id', null, PARAM_INT),
            'templateid' => $this->optional_param('templateid', null, PARAM_INT)
        ];
        return new moodle_url('/mod/feedback/use_templ.php', $params);
    }
}
