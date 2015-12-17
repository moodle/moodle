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
 * This file defines a base class for all assessment forms
 *
 * @package    mod_workshop
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php'); // parent class definition

/**
 * Base class for all assessment forms
 *
 * This defines the common fields that all assessment forms need.
 * Strategies should define their own class that inherits from this one, and
 * implements the definition_inner() method.
 *
 * @uses moodleform
 */
class workshop_assessment_form extends moodleform {

    /**
     * Add the fields that are common for all grading strategies.
     *
     * If the strategy does not support all these fields, then you can override
     * this method and remove the ones you don't want with
     * $mform->removeElement().
     * Strategy subclassess should define their own fields in definition_inner()
     *
     * @return void
     */
    public function definition() {
        global $CFG;

        $mform          = $this->_form;
        $this->mode     = $this->_customdata['mode'];       // influences the save buttons
        $this->strategy = $this->_customdata['strategy'];   // instance of the strategy api class
        $this->workshop = $this->_customdata['workshop'];   // instance of the workshop api class
        $this->options  = $this->_customdata['options'];    // array with addiotional options

        // Disable shortforms
        $mform->setDisableShortforms();

        // add the strategy-specific fields
        $this->definition_inner($mform);

        // add the data common for all subplugins
        $mform->addElement('hidden', 'strategy', $this->workshop->strategy);
        $mform->setType('strategy', PARAM_PLUGIN);

        if ($this->workshop->overallfeedbackmode and $this->is_editable()) {
            $mform->addElement('header', 'overallfeedbacksection', get_string('overallfeedback', 'mod_workshop'));
            $mform->addElement('editor', 'feedbackauthor_editor', get_string('feedbackauthor', 'mod_workshop'), null,
                $this->workshop->overall_feedback_content_options());
            if ($this->workshop->overallfeedbackmode == 2) {
                $mform->addRule('feedbackauthor_editor', null, 'required', null, 'client');
            }
            if ($this->workshop->overallfeedbackfiles) {
                $mform->addElement('filemanager', 'feedbackauthorattachment_filemanager',
                    get_string('feedbackauthorattachment', 'mod_workshop'), null,
                    $this->workshop->overall_feedback_attachment_options());
            }
        }

        if (!empty($this->options['editableweight']) and $this->is_editable()) {
            $mform->addElement('header', 'assessmentsettings', get_string('assessmentweight', 'workshop'));
            $mform->addElement('select', 'weight',
                    get_string('assessmentweight', 'workshop'), workshop::available_assessment_weights_list());
            $mform->setDefault('weight', 1);
        }

        $buttonarray = array();
        if ($this->mode == 'preview') {
            $buttonarray[] = $mform->createElement('cancel', 'backtoeditform', get_string('backtoeditform', 'workshop'));
        }
        if ($this->mode == 'assessment') {
            if (!empty($this->options['pending'])) {
                $buttonarray[] = $mform->createElement('submit', 'saveandshownext', get_string('saveandshownext', 'workshop'));
            }
            $buttonarray[] = $mform->createElement('submit', 'saveandclose', get_string('saveandclose', 'workshop'));
            $buttonarray[] = $mform->createElement('submit', 'saveandcontinue', get_string('saveandcontinue', 'workshop'));
            $buttonarray[] = $mform->createElement('cancel');
        }
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
    }

    /**
     * Add any strategy specific form fields.
     *
     * @param stdClass $mform the form being built.
     */
    protected function definition_inner(&$mform) {
        // By default, do nothing.
    }

    /**
     * Is the form frozen (read-only)?
     *
     * @return boolean
     */
    public function is_editable() {
        return !$this->_form->isFrozen();
    }

    /**
     * Validate incoming data.
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (isset($data['feedbackauthorattachment_filemanager'])) {
            $draftitemid = $data['feedbackauthorattachment_filemanager'];

            // If we have draft files, then make sure they are the correct ones.
            if ($draftfiles = file_get_drafarea_files($draftitemid)) {

                if (!$validfileextensions = workshop::get_array_of_file_extensions($this->workshop->overallfeedbackfiletypes)) {
                    return $errors;
                }
                $wrongfileextensions = null;
                $bigfiles = null;

                // Check the size of each file.
                foreach ($draftfiles->list as $file) {
                    $a = new stdClass();
                    $a->maxbytes = $this->workshop->overallfeedbackmaxbytes;
                    $a->currentbytes = $file->size;
                    $a->filename = $file->filename;
                    $a->validfileextensions = implode(',', $validfileextensions);

                    // Check whether the extension of uploaded file is in the list.
                    $thisextension = substr(strrchr($file->filename, '.'), 1);
                    if (!in_array($thisextension, $validfileextensions)) {
                        $wrongfileextensions .= get_string('err_wrongfileextension', 'workshop', $a) . '<br/>';
                    }
                    if ($file->size > $this->workshop->overallfeedbackmaxbytes) {
                        $bigfiles .= get_string('err_maxbytes', 'workshop', $a) . '<br/>';
                    }
                }
                if ($bigfiles || $wrongfileextensions) {
                    $errors['feedbackauthorattachment_filemanager'] = $bigfiles . $wrongfileextensions;
                }
            }
        }
        return $errors;
    }

}
