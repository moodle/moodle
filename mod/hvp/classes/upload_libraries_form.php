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

/**
 * \mod_hvp\upload_libraries_form class
 *
 * @package    mod_hvp
 * @copyright  2016 Joubel AS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_hvp;

defined('MOODLE_INTERNAL') || die();

// Load moodleform class.
require_once("$CFG->libdir/formslib.php");

/**
 * Form to upload new H5P libraries and upgrade existing once
 *
 * @package    mod_hvp
 * @copyright  2016 Joubel AS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class upload_libraries_form extends \moodleform {

    /**
     * Define form elements
     */
    public function definition() {
        global $CFG, $OUTPUT;

        // Get form.
        $mform = $this->_form;

        // Add File Picker.
        $mform->addElement('filepicker', 'h5pfile', get_string('h5pfile', 'hvp'), null,
                   array('maxbytes' => $CFG->maxbytes, 'accepted_types' => '*.h5p'));

        // Add options.
        $mform->addElement('checkbox',
            'onlyupdate',
            get_string('options', 'hvp'),
            get_string('onlyupdate', 'hvp'),
            array('group' => 1)
        );
        $mform->setType('onlyupdate', PARAM_BOOL);
        $mform->setDefault('onlyupdate', false);

        $mform->addElement('checkbox',
            'disablefileextensioncheck',
            '',
            get_string('disablefileextensioncheck', 'hvp'),
            array('group' => 1)
        );
        $mform->setType('disablefileextensioncheck', PARAM_BOOL);
        $mform->setDefault('disablefileextensioncheck', false);

        $notification = $OUTPUT->notification(
            get_string('disablefileextensioncheckwarning', 'hvp'),
            'notifymessage'
        );
        $mform->addElement('static', '', '', $notification);

        // Upload button.
        $this->add_action_buttons(false, get_string('upload', 'hvp'));
    }

    /**
     * Preprocess incoming data
     *
     * @param array $defaultvalues default values for form
     */
    public function data_preprocessing(&$defaultvalues) {
        // Aaah.. we meet again h5pfile!.
        $draftitemid = file_get_submitted_draft_itemid('h5pfile');
        file_prepare_draft_area($draftitemid, $this->context->id, 'mod_hvp', 'package', 0);
        $defaultvalues['h5pfile'] = $draftitemid;
    }

    /**
     * Validate incoming data
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files) {
        global $CFG;
        $errors = array();

        // Check for file.
        if (empty($data['h5pfile'])) {
            $errors['h5pfile'] = get_string('required');
            return $errors;
        }

        $files = $this->get_draft_files('h5pfile');
        if (count($files) < 1) {
            $errors['h5pfile'] = get_string('required');
            return $errors;
        }

        // Add file so that core framework can find it.
        $file = reset($files);
        $interface = \mod_hvp\framework::instance('interface');

        $path = $CFG->tempdir . uniqid('/hvp-');
        $interface->getUploadedH5pFolderPath($path);
        $path .= '.h5p';
        $interface->getUploadedH5pPath($path);
        $file->copy_content_to($path);

        // Validate package.
        $h5pvalidator = \mod_hvp\framework::instance('validator');
        if (!$h5pvalidator->isValidPackage(true, isset($data['onlyupdate']))) {
            // Errors while validating the package.
            $errors = array_map(function ($message) {
                return $message->message;
            }, framework::messages('error'));

            $messages = array_merge(framework::messages('info'), $errors);
            $errors['h5pfile'] = implode('<br/>', $messages);
        }
        return $errors;
    }
}
