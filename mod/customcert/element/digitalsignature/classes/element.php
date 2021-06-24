<?php
// This file is part of the customcert module for Moodle - http://moodle.org/
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
 * This file contains the customcert element digitial signature's core interaction API.
 *
 * @package    customcertelement_digitalsignature
 * @copyright  2017 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace customcertelement_digitalsignature;

defined('MOODLE_INTERNAL') || die();

/**
 * The customcert element digital signature's core interaction API.
 *
 * @package    customcertelement_digitalsignature
 * @copyright  2017 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class element extends \customcertelement_image\element {

    /**
     * @var array The file manager options for the certificate.
     */
    protected $signaturefilemanageroptions = array();

    /**
     * Constructor.
     *
     * @param \stdClass $element the element data
     */
    public function __construct($element) {
        global $COURSE;

        $this->signaturefilemanageroptions = [
            'maxbytes' => $COURSE->maxbytes,
            'subdirs' => 1,
            'accepted_types' => ['.crt']
        ];

        parent::__construct($element);
    }

    /**
     * This function renders the form elements when adding a customcert element.
     *
     * @param \MoodleQuickForm $mform the edit_form instance
     */
    public function render_form_elements($mform) {
        $mform->addElement('select', 'fileid', get_string('image', 'customcertelement_image'), self::get_images());

        $mform->addElement('select', 'signaturefileid', get_string('digitalsignature', 'customcertelement_digitalsignature'),
            self::get_signatures());

        $mform->addElement('text', 'signaturename', get_string('signaturename', 'customcertelement_digitalsignature'));
        $mform->setType('signaturename', PARAM_TEXT);
        $mform->setDefault('signaturename', '');

        $mform->addElement('passwordunmask', 'signaturepassword',
            get_string('signaturepassword', 'customcertelement_digitalsignature'));
        $mform->setType('signaturepassword', PARAM_TEXT);
        $mform->setDefault('signaturepassword', '');

        $mform->addElement('text', 'signaturelocation', get_string('signaturelocation', 'customcertelement_digitalsignature'));
        $mform->setType('signaturelocation', PARAM_TEXT);
        $mform->setDefault('signaturelocation', '');

        $mform->addElement('text', 'signaturereason', get_string('signaturereason', 'customcertelement_digitalsignature'));
        $mform->setType('signaturereason', PARAM_TEXT);
        $mform->setDefault('signaturereason', '');

        $mform->addElement('text', 'signaturecontactinfo',
            get_string('signaturecontactinfo', 'customcertelement_digitalsignature'));
        $mform->setType('signaturecontactinfo', PARAM_TEXT);
        $mform->setDefault('signaturecontactinfo', '');

        $mform->addElement('text', 'width', get_string('width', 'customcertelement_image'), array('size' => 10));
        $mform->setType('width', PARAM_INT);
        $mform->setDefault('width', 0);
        $mform->addHelpButton('width', 'width', 'customcertelement_image');

        $mform->addElement('text', 'height', get_string('height', 'customcertelement_image'), array('size' => 10));
        $mform->setType('height', PARAM_INT);
        $mform->setDefault('height', 0);
        $mform->addHelpButton('height', 'height', 'customcertelement_image');

        if (get_config('customcert', 'showposxy')) {
            \mod_customcert\element_helper::render_form_element_position($mform);
        }

        $mform->addElement('filemanager', 'customcertimage', get_string('uploadimage', 'customcert'), '',
            $this->filemanageroptions);

        $mform->addElement('filemanager', 'digitalsignature',
            get_string('uploaddigitalsignature', 'customcertelement_digitalsignature'), '',
            $this->signaturefilemanageroptions);
    }

    /**
     * Handles saving the form elements created by this element.
     * Can be overridden if more functionality is needed.
     *
     * @param \stdClass $data the form data
     * @return bool true of success, false otherwise.
     */
    public function save_form_elements($data) {
        global $COURSE, $SITE;

        // Set the context.
        if ($COURSE->id == $SITE->id) {
            $context = \context_system::instance();
        } else {
            $context = \context_course::instance($COURSE->id);
        }

        // Handle file uploads.
        \mod_customcert\certificate::upload_files($data->customcertimage, $context->id);

        // Handle file certificate uploads.
        \mod_customcert\certificate::upload_files($data->digitalsignature, $context->id, 'signature');

        return parent::save_form_elements($data);
    }

    /**
     * This will handle how form data will be saved into the data column in the
     * customcert_elements table.
     *
     * @param \stdClass $data the form data
     * @return string the json encoded array
     */
    public function save_unique_data($data) {
        $arrtostore = [
            'signaturename' => $data->signaturename,
            'signaturepassword' => $data->signaturepassword,
            'signaturelocation' => $data->signaturelocation,
            'signaturereason' => $data->signaturereason,
            'signaturecontactinfo' => $data->signaturecontactinfo,
            'width' => !empty($data->width) ? (int) $data->width : 0,
            'height' => !empty($data->height) ? (int) $data->height : 0
        ];

        // Array of data we will be storing in the database.
        $fs = get_file_storage();

        if (!empty($data->fileid)) {
            if ($file = $fs->get_file_by_id($data->fileid)) {
                $arrtostore += [
                    'contextid' => $file->get_contextid(),
                    'filearea' => $file->get_filearea(),
                    'itemid' => $file->get_itemid(),
                    'filepath' => $file->get_filepath(),
                    'filename' => $file->get_filename(),
                ];
            }
        }

        if (!empty($data->signaturefileid)) {
            if ($signaturefile = $fs->get_file_by_id($data->signaturefileid)) {
                $arrtostore += [
                    'signaturecontextid' => $signaturefile->get_contextid(),
                    'signaturefilearea' => $signaturefile->get_filearea(),
                    'signatureitemid' => $signaturefile->get_itemid(),
                    'signaturefilepath' => $signaturefile->get_filepath(),
                    'signaturefilename' => $signaturefile->get_filename()
                ];
            }
        }

        return json_encode($arrtostore);
    }

    /**
     * Handles rendering the element on the pdf.
     *
     * @param \pdf $pdf the pdf object
     * @param bool $preview true if it is a preview, false otherwise
     * @param \stdClass $user the user we are rendering this for
     */
    public function render($pdf, $preview, $user) {
        // If there is no element data, we have nothing to display.
        if (empty($this->get_data())) {
            return;
        }

        $imageinfo = json_decode($this->get_data());

        // If there is no file, we have nothing to display.
        if (empty($imageinfo->filename)) {
            return;
        }

        // If there is no signature file, we have nothing to display.
        if (empty($imageinfo->signaturefilename)) {
            return;
        }

        if ($file = $this->get_file()) {
            $location = make_request_directory() . '/target';
            $file->copy_content_to($location);

            $mimetype = $file->get_mimetype();
            if ($mimetype == 'image/svg+xml') {
                $pdf->ImageSVG($location, $this->get_posx(), $this->get_posy(), $imageinfo->width, $imageinfo->height);
            } else {
                $pdf->Image($location, $this->get_posx(), $this->get_posy(), $imageinfo->width, $imageinfo->height);
            }
        }

        if ($signaturefile = $this->get_signature_file()) {
            $location = make_request_directory() . '/target';
            $signaturefile->copy_content_to($location);
            $info = [
                'Name' => $imageinfo->signaturename,
                'Location' => $imageinfo->signaturelocation,
                'Reason' => $imageinfo->signaturereason,
                'ContactInfo' => $imageinfo->signaturecontactinfo
            ];
            $pdf->setSignature('file://' . $location, '', $imageinfo->signaturepassword, '', 2, $info);
            $pdf->setSignatureAppearance($this->get_posx(), $this->get_posy(), $imageinfo->width, $imageinfo->height);
        }
    }

    /**
     * Sets the data on the form when editing an element.
     *
     * @param \MoodleQuickForm $mform the edit_form instance
     */
    public function definition_after_data($mform) {
        global $COURSE, $SITE;

        // Set the context.
        if ($COURSE->id == $SITE->id) {
            $context = \context_system::instance();
        } else {
            $context = \context_course::instance($COURSE->id);
        }

        if (!empty($this->get_data())) {
            $imageinfo = json_decode($this->get_data());

            $element = $mform->getElement('signaturename');
            $element->setValue($imageinfo->signaturename);

            $element = $mform->getElement('signaturepassword');
            $element->setValue($imageinfo->signaturepassword);

            $element = $mform->getElement('signaturelocation');
            $element->setValue($imageinfo->signaturelocation);

            $element = $mform->getElement('signaturereason');
            $element->setValue($imageinfo->signaturereason);

            $element = $mform->getElement('signaturecontactinfo');
            $element->setValue($imageinfo->signaturecontactinfo);

            if (!empty($imageinfo->signaturefilename)) {
                if ($signaturefile = $this->get_signature_file()) {
                    $element = $mform->getElement('signaturefileid');
                    $element->setValue($signaturefile->get_id());
                }
            }
        }

        // Editing existing instance - copy existing files into draft area.
        $draftitemid = file_get_submitted_draft_itemid('digitalsignature');
        file_prepare_draft_area($draftitemid, $context->id, 'mod_customcert', 'signature', 0,
            $this->signaturefilemanageroptions);
        $element = $mform->getElement('digitalsignature');
        $element->setValue($draftitemid);

        parent::definition_after_data($mform);
    }

    /**
     * Return the list of possible images to use.
     *
     * @return array the list of images that can be used
     */
    public static function get_signatures() {
        global $COURSE;

        // Create file storage object.
        $fs = get_file_storage();

        // The array used to store the digital signatures.
        $arrfiles = array();
        // Loop through the files uploaded in the system context.
        if ($files = $fs->get_area_files(\context_system::instance()->id, 'mod_customcert', 'signature', false,
                'filename', false)) {
            foreach ($files as $hash => $file) {
                $arrfiles[$file->get_id()] = $file->get_filename();
            }
        }
        // Loop through the files uploaded in the course context.
        if ($files = $fs->get_area_files(\context_course::instance($COURSE->id)->id, 'mod_customcert', 'signature', false,
                'filename', false)) {
            foreach ($files as $hash => $file) {
                $arrfiles[$file->get_id()] = $file->get_filename();
            }
        }

        \core_collator::asort($arrfiles);
        $arrfiles = array('0' => get_string('nosignature', 'customcertelement_digitalsignature')) + $arrfiles;

        return $arrfiles;
    }

    /**
     * Fetch stored file.
     *
     * @return \stored_file|bool stored_file instance if exists, false if not
     */
    public function get_signature_file() {
        $imageinfo = json_decode($this->get_data());

        $fs = get_file_storage();

        return $fs->get_file($imageinfo->signaturecontextid, 'mod_customcert', $imageinfo->signaturefilearea,
            $imageinfo->signatureitemid, $imageinfo->signaturefilepath, $imageinfo->signaturefilename);
    }
}
