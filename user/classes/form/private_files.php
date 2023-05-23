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

namespace core_user\form;

use html_writer;
use moodle_url;

/**
 * Manage user private area files form
 *
 * @package    core_user
 * @copyright  2010 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class private_files extends \core_form\dynamic_form {

    /**
     * Add elements to this form.
     */
    public function definition() {
        global $OUTPUT;
        $mform = $this->_form;
        $options = $this->get_options();

        // Show file area space usage.
        $maxareabytes = $options['areamaxbytes'];
        if ($maxareabytes != FILE_AREA_MAX_BYTES_UNLIMITED) {
            $fileareainfo = file_get_file_area_info($this->get_context_for_dynamic_submission()->id, 'user', 'private');
            // Display message only if we have files.
            if ($fileareainfo['filecount']) {
                $a = (object) [
                    'used' => display_size($fileareainfo['filesize_without_references']),
                    'total' => display_size($maxareabytes, 0)
                ];
                $quotamsg = get_string('quotausage', 'moodle', $a);
                $notification = new \core\output\notification($quotamsg, \core\output\notification::NOTIFY_INFO);
                $mform->addElement('static', 'areabytes', '', $OUTPUT->render($notification));
            }
        }

        $mform->addElement('filemanager', 'files_filemanager', get_string('files'), null, $options);
        if ($link = $this->get_emaillink()) {
            $emaillink = html_writer::link(new moodle_url('mailto:' . $link), $link);
            $mform->addElement('static', 'emailaddress', '',
                get_string('emailtoprivatefiles', 'moodle', $emaillink));
        }
        $mform->setType('returnurl', PARAM_LOCALURL);

        // The 'nosubmit' param (default false) determines whether we should show the standard form action buttons (save/cancel).
        // This value is set when the form is displayed within a modal, which adds the action buttons itself.
        if (!$this->optional_param('nosubmit', false, PARAM_BOOL)) {
            $this->add_action_buttons();
        }
    }

    /**
     * Validate incoming data.
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = array();
        $draftitemid = $data['files_filemanager'];
        $options = $this->get_options();
        if (file_is_draft_area_limit_reached($draftitemid, $options['areamaxbytes'])) {
            $errors['files_filemanager'] = get_string('userquotalimit', 'error');
        }

        return $errors;
    }

    /**
     * Link to email private files
     *
     * @return string|null
     * @throws \coding_exception
     */
    protected function get_emaillink() {
        global $USER;

        // Attempt to generate an inbound message address to support e-mail to private files.
        $generator = new \core\message\inbound\address_manager();
        $generator->set_handler('\core\message\inbound\private_files_handler');
        $generator->set_data(-1);
        return $generator->generate($USER->id);
    }

    /**
     * Check if current user has access to this form, otherwise throw exception
     *
     * Sometimes permission check may depend on the action and/or id of the entity.
     * If necessary, form data is available in $this->_ajaxformdata or
     * by calling $this->optional_param()
     */
    public function check_access_for_dynamic_submission(): void {
        require_capability('moodle/user:manageownfiles', $this->get_context_for_dynamic_submission());
    }

    /**
     * Returns form context
     *
     * If context depends on the form data, it is available in $this->_ajaxformdata or
     * by calling $this->optional_param()
     *
     * @return \context
     */
    protected function get_context_for_dynamic_submission(): \context {
        global $USER;
        return \context_user::instance($USER->id);
    }

    /**
     * File upload options
     *
     * @return array
     * @throws \coding_exception
     */
    public function get_options(): array {
        global $CFG;

        $maxbytes = $CFG->userquota;
        $maxareabytes = $CFG->userquota;
        if (has_capability('moodle/user:ignoreuserquota', $this->get_context_for_dynamic_submission())) {
            $maxbytes = USER_CAN_IGNORE_FILE_SIZE_LIMITS;
            $maxareabytes = FILE_AREA_MAX_BYTES_UNLIMITED;
        }

        return ['subdirs' => 1, 'maxbytes' => $maxbytes, 'maxfiles' => -1, 'accepted_types' => '*',
            'areamaxbytes' => $maxareabytes];
    }

    /**
     * Process the form submission, used if form was submitted via AJAX
     *
     * This method can return scalar values or arrays that can be json-encoded, they will be passed to the caller JS.
     *
     * Submission data can be accessed as: $this->get_data()
     *
     * @return mixed
     */
    public function process_dynamic_submission() {
        file_postupdate_standard_filemanager($this->get_data(), 'files',
            $this->get_options(), $this->get_context_for_dynamic_submission(), 'user', 'private', 0);
        return null;
    }

    /**
     * Load in existing data as form defaults
     *
     * Can be overridden to retrieve existing values from db by entity id and also
     * to preprocess editor and filemanager elements
     *
     * Example:
     *     $this->set_data(get_entity($this->_ajaxformdata['id']));
     */
    public function set_data_for_dynamic_submission(): void {
        $data = new \stdClass();
        file_prepare_standard_filemanager($data, 'files', $this->get_options(),
            $this->get_context_for_dynamic_submission(), 'user', 'private', 0);
        $this->set_data($data);
    }

    /**
     * Returns url to set in $PAGE->set_url() when form is being rendered or submitted via AJAX
     *
     * This is used in the form elements sensitive to the page url, such as Atto autosave in 'editor'
     *
     * If the form has arguments (such as 'id' of the element being edited), the URL should
     * also have respective argument.
     *
     * @return \moodle_url
     */
    protected function get_page_url_for_dynamic_submission(): \moodle_url {
        return new moodle_url('/user/files.php');
    }
}
