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
* Instance add/edit form
*
* @package    mod_iomadcertificate
* @copyright  Mark Nelson <markn@moodle.com>
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once ($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/iomadcertificate/locallib.php');

class mod_iomadcertificate_mod_form extends moodleform_mod {

    function definition() {
        global $CFG;

        $mform =& $this->_form;

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('iomadcertificatename', 'iomadcertificate'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', null, 'required', null, 'client');

        $this->standard_intro_elements(get_string('intro', 'iomadcertificate'));

        // Issue options
        $mform->addElement('header', 'issueoptions', get_string('issueoptions', 'iomadcertificate'));
        $ynoptions = array( 0 => get_string('no'), 1 => get_string('yes'));
        $mform->addElement('select', 'emailteachers', get_string('emailteachers', 'iomadcertificate'), $ynoptions);
        $mform->setDefault('emailteachers', 0);
        $mform->addHelpButton('emailteachers', 'emailteachers', 'iomadcertificate');

        $mform->addElement('text', 'emailothers', get_string('emailothers', 'iomadcertificate'), array('size'=>'40', 'maxsize'=>'200'));
        $mform->setType('emailothers', PARAM_TEXT);
        $mform->addHelpButton('emailothers', 'emailothers', 'iomadcertificate');

        $deliveryoptions = array( 0 => get_string('openbrowser', 'iomadcertificate'), 1 => get_string('download', 'iomadcertificate'), 2 => get_string('emailiomadcertificate', 'iomadcertificate'));
        $mform->addElement('select', 'delivery', get_string('delivery', 'iomadcertificate'), $deliveryoptions);
        $mform->setDefault('delivery', 0);
        $mform->addHelpButton('delivery', 'delivery', 'iomadcertificate');

        $mform->addElement('select', 'savecert', get_string('savecert', 'iomadcertificate'), $ynoptions);
        $mform->setDefault('savecert', 0);
        $mform->addHelpButton('savecert', 'savecert', 'iomadcertificate');

        $reportfile = "$CFG->dirroot/iomadcertificates/index.php";
        if (file_exists($reportfile)) {
            $mform->addElement('select', 'reportcert', get_string('reportcert', 'iomadcertificate'), $ynoptions);
            $mform->setDefault('reportcert', 0);
            $mform->addHelpButton('reportcert', 'reportcert', 'iomadcertificate');
        }

        $mform->addElement('text', 'requiredtime', get_string('coursetimereq', 'iomadcertificate'), array('size'=>'3'));
        $mform->setType('requiredtime', PARAM_INT);
        $mform->addHelpButton('requiredtime', 'coursetimereq', 'iomadcertificate');

        // Text Options
        $mform->addElement('header', 'textoptions', get_string('textoptions', 'iomadcertificate'));

        $modules = iomadcertificate_get_mods();
        $dateoptions = iomadcertificate_get_date_options() + $modules;
        $mform->addElement('select', 'printdate', get_string('printdate', 'iomadcertificate'), $dateoptions);
        $mform->setDefault('printdate', 'N');
        $mform->addHelpButton('printdate', 'printdate', 'iomadcertificate');

        $dateformatoptions = array( 1 => 'January 1, 2000', 2 => 'January 1st, 2000', 3 => '1 January 2000',
            4 => 'January 2000', 5 => get_string('userdateformat', 'iomadcertificate'));
        $mform->addElement('select', 'datefmt', get_string('datefmt', 'iomadcertificate'), $dateformatoptions);
        $mform->setDefault('datefmt', 0);
        $mform->addHelpButton('datefmt', 'datefmt', 'iomadcertificate');

        $mform->addElement('select', 'printnumber', get_string('printnumber', 'iomadcertificate'), $ynoptions);
        $mform->setDefault('printnumber', 0);
        $mform->addHelpButton('printnumber', 'printnumber', 'iomadcertificate');

        $gradeoptions = iomadcertificate_get_grade_options() + iomadcertificate_get_grade_categories($this->current->course) + $modules;
        $mform->addElement('select', 'printgrade', get_string('printgrade', 'iomadcertificate'),$gradeoptions);
        $mform->setDefault('printgrade', 0);
        $mform->addHelpButton('printgrade', 'printgrade', 'iomadcertificate');

        $gradeformatoptions = array( 1 => get_string('gradepercent', 'iomadcertificate'), 2 => get_string('gradepoints', 'iomadcertificate'),
            3 => get_string('gradeletter', 'iomadcertificate'));
        $mform->addElement('select', 'gradefmt', get_string('gradefmt', 'iomadcertificate'), $gradeformatoptions);
        $mform->setDefault('gradefmt', 0);
        $mform->addHelpButton('gradefmt', 'gradefmt', 'iomadcertificate');

        $outcomeoptions = iomadcertificate_get_outcomes();
        $mform->addElement('select', 'printoutcome', get_string('printoutcome', 'iomadcertificate'),$outcomeoptions);
        $mform->setDefault('printoutcome', 0);
        $mform->addHelpButton('printoutcome', 'printoutcome', 'iomadcertificate');

        $mform->addElement('text', 'printhours', get_string('printhours', 'iomadcertificate'), array('size'=>'5', 'maxlength' => '255'));
        $mform->setType('printhours', PARAM_TEXT);
        $mform->addHelpButton('printhours', 'printhours', 'iomadcertificate');

        $mform->addElement('select', 'printteacher', get_string('printteacher', 'iomadcertificate'), $ynoptions);
        $mform->setDefault('printteacher', 0);
        $mform->addHelpButton('printteacher', 'printteacher', 'iomadcertificate');

        $mform->addElement('textarea', 'customtext', get_string('customtext', 'iomadcertificate'), array('cols'=>'40', 'rows'=>'4', 'wrap'=>'virtual'));
        $mform->setType('customtext', PARAM_RAW);
        $mform->addHelpButton('customtext', 'customtext', 'iomadcertificate');

        // Design Options
        $mform->addElement('header', 'designoptions', get_string('designoptions', 'iomadcertificate'));
        $mform->addElement('select', 'iomadcertificatetype', get_string('iomadcertificatetype', 'iomadcertificate'), iomadcertificate_types());
        $mform->setDefault('iomadcertificatetype', 'A4_non_embedded');
        $mform->addHelpButton('iomadcertificatetype', 'iomadcertificatetype', 'iomadcertificate');

        $orientation = array( 'L' => get_string('landscape', 'iomadcertificate'), 'P' => get_string('portrait', 'iomadcertificate'));
        $mform->addElement('select', 'orientation', get_string('orientation', 'iomadcertificate'), $orientation);
        $mform->setDefault('orientation', 'L');
        $mform->addHelpButton('orientation', 'orientation', 'iomadcertificate');

        $mform->addElement('select', 'borderstyle', get_string('borderstyle', 'iomadcertificate'), iomadcertificate_get_images(CERT_IMAGE_BORDER));
        $mform->setDefault('borderstyle', '0');
        $mform->addHelpButton('borderstyle', 'borderstyle', 'iomadcertificate');

        $printframe = array( 0 => get_string('no'), 1 => get_string('borderblack', 'iomadcertificate'), 2 => get_string('borderbrown', 'iomadcertificate'),
            3 => get_string('borderblue', 'iomadcertificate'), 4 => get_string('bordergreen', 'iomadcertificate'));
        $mform->addElement('select', 'bordercolor', get_string('bordercolor', 'iomadcertificate'), $printframe);
        $mform->setDefault('bordercolor', '0');
        $mform->addHelpButton('bordercolor', 'bordercolor', 'iomadcertificate');

        $mform->addElement('select', 'printwmark', get_string('printwmark', 'iomadcertificate'), iomadcertificate_get_images(CERT_IMAGE_WATERMARK));
        $mform->setDefault('printwmark', '0');
        $mform->addHelpButton('printwmark', 'printwmark', 'iomadcertificate');

        $mform->addElement('select', 'printsignature', get_string('printsignature', 'iomadcertificate'), iomadcertificate_get_images(CERT_IMAGE_SIGNATURE));
        $mform->setDefault('printsignature', '0');
        $mform->addHelpButton('printsignature', 'printsignature', 'iomadcertificate');

        $mform->addElement('select', 'printseal', get_string('printseal', 'iomadcertificate'), iomadcertificate_get_images(CERT_IMAGE_SEAL));
        $mform->setDefault('printseal', '0');
        $mform->addHelpButton('printseal', 'printseal', 'iomadcertificate');

        $this->standard_coursemodule_elements();

        $this->add_action_buttons();
    }

    /**
     * Some basic validation
     *
     * @param $data
     * @param $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Check that the required time entered is valid
        if ((!is_number($data['requiredtime']) || $data['requiredtime'] < 0)) {
            $errors['requiredtime'] = get_string('requiredtimenotvalid', 'iomadcertificate');
        }

        return $errors;
    }
}
