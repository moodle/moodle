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

namespace core_contentbank\form;

use core\output\notification;

/**
 * Upload files to content bank form
 *
 * @package    core_contentbank
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class upload_files extends \core_form\dynamic_form {

    /**
     * Add elements to this form.
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('hidden', 'contextid');
        $mform->setType('contextid', PARAM_INT);

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('filepicker', 'file', get_string('file', 'core_contentbank'), null, $this->get_options());
        $mform->addHelpButton('file', 'file', 'core_contentbank');
        $mform->addRule('file', null, 'required');
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
        $draftitemid = $data['file'];
        $options = $this->get_options();
        if (file_is_draft_area_limit_reached($draftitemid, $options['areamaxbytes'])) {
            $errors['file'] = get_string('userquotalimit', 'error');
        }
        return $errors;
    }

    /**
     * Check if current user has access to this form, otherwise throw exception
     *
     * Sometimes permission check may depend on the action and/or id of the entity.
     * If necessary, form data is available in $this->_ajaxformdata or
     * by calling $this->optional_param()
     */
    protected function check_access_for_dynamic_submission(): void {
        require_capability('moodle/contentbank:upload', $this->get_context_for_dynamic_submission());

        // Check the context used by the content bank is allowed.
        $cb = new \core_contentbank\contentbank();
        if (!$cb->is_context_allowed($this->get_context_for_dynamic_submission())) {
            throw new \moodle_exception('contextnotallowed', 'core_contentbank');
        }

        // If $id is defined, the file content will be replaced (instead of uploading a new one).
        // Check that the user has the right permissions to replace this content file.
        $id = $this->optional_param('id', null, PARAM_INT);
        if ($id) {
            $content = $cb->get_content_from_id($id);
            $contenttype = $content->get_content_type_instance();
            if (!$contenttype->can_manage($content) || !$contenttype->can_upload()) {
                throw new \moodle_exception('nopermissions', 'error', '', null, get_string('replacecontent', 'contentbank'));
            }
        }
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
        $contextid = $this->optional_param('contextid', null, PARAM_INT);
        return \context::instance_by_id($contextid, MUST_EXIST);
    }

    /**
     * File upload options
     *
     * @return array
     * @throws \coding_exception
     */
    protected function get_options(): array {
        global $CFG;

        $maxbytes = $CFG->userquota;
        $maxareabytes = $CFG->userquota;
        if (has_capability('moodle/user:ignoreuserquota', $this->get_context_for_dynamic_submission())) {
            $maxbytes = USER_CAN_IGNORE_FILE_SIZE_LIMITS;
            $maxareabytes = FILE_AREA_MAX_BYTES_UNLIMITED;
        }

        $cb = new \core_contentbank\contentbank();
        $id = $this->optional_param('id', null, PARAM_INT);
        if ($id) {
            $content = $cb->get_content_from_id($id);
            $contenttype = $content->get_content_type_instance();
            $extensions = $contenttype->get_manageable_extensions();
            $acceptedtypes = implode(',', $extensions);
        } else {
            $acceptedtypes = $cb->get_supported_extensions_as_string($this->get_context_for_dynamic_submission());
        }

        return ['subdirs' => 1, 'maxbytes' => $maxbytes, 'maxfiles' => -1, 'accepted_types' => $acceptedtypes,
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
        global $USER;

        // Get the file and create the content based on it.
        $usercontext = \context_user::instance($USER->id);
        $fs = get_file_storage();
        $files = $fs->get_area_files($usercontext->id, 'user', 'draft', $this->get_data()->file, 'itemid, filepath,
            filename', false);
        if (!empty($files)) {
            $file = reset($files);
            $cb = new \core_contentbank\contentbank();
            try {
                if ($this->get_data()->id) {
                    $content = $cb->get_content_from_id($this->get_data()->id);
                    $contenttype = $content->get_content_type_instance();
                    $content = $contenttype->replace_content($file, $content);
                } else {
                    $content = $cb->create_content_from_file($this->get_context_for_dynamic_submission(), $USER->id, $file);
                }
                $params = ['id' => $content->get_id(), 'contextid' => $this->get_context_for_dynamic_submission()->id];
                $url = new \moodle_url('/contentbank/view.php', $params);
            } catch (\moodle_exception $e) {
                // Redirect to the right page (depending on if content is new or existing) and display an error.
                if ($this->get_data()->id) {
                    $content = $cb->get_content_from_id($this->get_data()->id);
                    $params = [
                        'id' => $content->get_id(),
                        'contextid' => $this->get_context_for_dynamic_submission()->id,
                        'errormsg' => $e->errorcode,
                    ];
                    $url = new \moodle_url('/contentbank/view.php', $params);
                } else {
                    $url = new \moodle_url('/contentbank/index.php', [
                        'contextid' => $this->get_context_for_dynamic_submission()->id,
                        'errormsg' => $e->errorcode],
                    );
                }
            }

            return ['returnurl' => $url->out(false)];
        }

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
        $data = (object)[
            'contextid' => $this->optional_param('contextid', null, PARAM_INT),
            'id' => $this->optional_param('id', null, PARAM_INT),
        ];
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
        $params = ['contextid' => $this->get_context_for_dynamic_submission()->id];

        $id = $this->optional_param('id', null, PARAM_INT);
        if ($id) {
            $url = '/contentbank/view.php';
            $params['id'] = $id;
        } else {
            $url = '/contentbank/index.php';
        }

        return new \moodle_url($url, $params);
    }
}
