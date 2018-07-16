<?php

// This file is part of the Certificate module for Moodle - http://moodle.org/
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
 * Handles uploading files
 *
 * @package    mod_iomadcertificate
 * @copyright  Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/mod/iomadcertificate/locallib.php');

class mod_iomadcertificate_upload_image_form extends moodleform {

    function definition() {
        global $CFG;

        $mform =& $this->_form;

        $imagetypes = array(
            CERT_IMAGE_BORDER => get_string('border', 'iomadcertificate'),
            CERT_IMAGE_WATERMARK => get_string('watermark', 'iomadcertificate'),
            CERT_IMAGE_SIGNATURE => get_string('signature', 'iomadcertificate'),
            CERT_IMAGE_SEAL => get_string('seal', 'iomadcertificate')
        );

        $mform->addElement('select', 'imagetype', get_string('imagetype', 'iomadcertificate'), $imagetypes);

        $mform->addElement('filepicker', 'iomadcertificateimage', '');
        $mform->addRule('iomadcertificateimage', null, 'required', null, 'client');

        $this->add_action_buttons();
    }

    /**
     * Some validation - Michael Avelar <michaela@moodlerooms.com>
     */
    function validation($data, $files) {
        $errors = parent::validation($data, $files);

        $supportedtypes = array('jpe' => 'image/jpeg',
                                'jpeIE' => 'image/pjpeg',
                                'jpeg' => 'image/jpeg',
                                'jpegIE' => 'image/pjpeg',
                                'jpg' => 'image/jpeg',
                                'jpgIE' => 'image/pjpeg',
                                'png' => 'image/png',
                                'pngIE' => 'image/x-png');

        $files = $this->get_draft_files('iomadcertificateimage');
        if ($files) {
            foreach ($files as $file) {
                if (!in_array($file->get_mimetype(), $supportedtypes)) {
                    $errors['iomadcertificateimage'] = get_string('unsupportedfiletype', 'iomadcertificate');
                }
            }
        } else {
            $errors['iomadcertificateimage'] = get_string('nofileselected', 'iomadcertificate');
        }

        return $errors;
    }
}
