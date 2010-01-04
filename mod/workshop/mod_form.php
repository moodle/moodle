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
 * The main workshop configuration form
 *
 * The UI mockup has been proposed in MDL-18688
 * It uses the standard core Moodle formslib. For more info about them, please 
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package   mod-workshop
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

class mod_workshop_mod_form extends moodleform_mod {

    function definition() {

        global $CFG, $COURSE;
        $workshopconfig = get_config('workshop');
        $mform =& $this->_form;

/// General --------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

    /// Workshop name
        $label = get_string('workshopname', 'workshop');
        $mform->addElement('text', 'name', $label, array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->setHelpButton('name', array('workshopname', $label, 'workshop'));

    /// Introduction
        $this->add_intro_editor(false, get_string('introduction', 'workshop'));

/// Workshop features ----------------------------------------------------------
        $mform->addElement('header', 'workshopfeatures', get_string('workshopfeatures', 'workshop'));

        $label = get_string('useexamples', 'workshop');
        $text = get_string('useexamplesdesc', 'workshop');
        $mform->addElement('advcheckbox', 'useexamples', $label, $text);
        $mform->setHelpButton('useexamples', array('useexamples', $label, 'workshop'));

        $label = get_string('usepeerassessment', 'workshop');
        $text = get_string('usepeerassessmentdesc', 'workshop');
        $mform->addElement('advcheckbox', 'usepeerassessment', $label, $text);
        $mform->setHelpButton('usepeerassessment', array('usepeerassessment', $label, 'workshop'));

        $label = get_string('useselfassessment', 'workshop');
        $text = get_string('useselfassessmentdesc', 'workshop');
        $mform->addElement('advcheckbox', 'useselfassessment', $label, $text);
        $mform->setHelpButton('useselfassessment', array('useselfassessment', $label, 'workshop'));

/// Grading settings -----------------------------------------------------------
        $mform->addElement('header', 'gradingsettings', get_string('gradingsettings', 'workshop'));

        $grades = workshop_get_maxgrades();

        $label = get_string('gradeforsubmission', 'workshop');
        $mform->addElement('select', 'grade', $label, $grades);
        $mform->setDefault('grade', $workshopconfig->grade);
        $mform->setHelpButton('grade', array('grade', $label, 'workshop'));

        $label = get_string('gradeforassessment', 'workshop');
        $mform->addElement('select', 'gradinggrade', $label , $grades);
        $mform->setDefault('gradinggrade', $workshopconfig->gradinggrade);
        $mform->setHelpButton('gradinggrade', array('gradinggrade', $label, 'workshop'));

        $label = get_string('strategy', 'workshop');
        $mform->addElement('select', 'strategy', $label, workshop_get_strategies());
        $mform->setDefault('strategy', $workshopconfig->strategy);
        $mform->setHelpButton('strategy', array('strategy', $label, 'workshop'));

/// Submission settings --------------------------------------------------------
        $mform->addElement('header', 'submissionsettings', get_string('submissionsettings', 'workshop'));

        $label = get_string('latesubmissions', 'workshop');
        $text = get_string('latesubmissionsdesc', 'workshop');
        $mform->addElement('advcheckbox', 'latesubmissions', $label, $text);
        $mform->setHelpButton('latesubmissions', array('latesubmissions', $label, 'workshop'));
        $mform->setAdvanced('latesubmissions');

        $options = array();
        for ($i=7; $i>=0; $i--) {
            $options[$i] = $i;
        }
        $label = get_string('nattachments', 'workshop');
        $mform->addElement('select', 'nattachments', $label, $options);
        $mform->setDefault('nattachments', 1);
        $mform->setHelpButton('nattachments', array('nattachments', $label, 'workshop'));
        $mform->setAdvanced('nattachments');

        $options = get_max_upload_sizes($CFG->maxbytes, $COURSE->maxbytes); 
        $options[0] = get_string('courseuploadlimit') . ' ('.display_size($COURSE->maxbytes).')';
        $mform->addElement('select', 'maxbytes', get_string('maximumsize', 'assignment'), $options);
        $mform->setDefault('maxbytes', $workshopconfig->maxbytes);
        $mform->setHelpButton('maxbytes', array('maxbytes', $label, 'workshop'));
        $mform->setAdvanced('maxbytes');

/// Assessment settings
        $mform->addElement('header', 'assessmentsettings', get_string('assessmentsettings', 'workshop'));

        $options = workshop_get_anonymity_modes();
        $label = get_string('anonymity', 'workshop');
        $mform->addElement('select', 'anonymity', $label, $options);
        $mform->setDefault('anonymity', $workshopconfig->anonymity);
        $mform->setHelpButton('anonymity', array('anonymity', $label, 'workshop'));
        $mform->disabledIf('anonymity', 'usepeerassessment');

        $label = get_string('nsassessments', 'workshop');
        $options = workshop_get_numbers_of_assessments();
        $mform->addElement('select', 'nsassessments', $label, $options);
        $mform->setDefault('nsassessments', $workshopconfig->nsassessments);
        $mform->setHelpButton('nsassessments', array('nsassessments', $label, 'workshop'));

        $label = get_string('nexassessments', 'workshop');
        $options = workshop_get_numbers_of_assessments();
        $options[0] = get_string('assessallexamples', 'workshop');
        $mform->addElement('select', 'nexassessments', $label, $options);
        $mform->setDefault('nexassessments', $workshopconfig->nexassessments);
        $mform->setHelpButton('nexassessments', array('nexassessments', $label, 'workshop'));
        $mform->disabledIf('nexassessments', 'useexamples');

        $label = get_string('assesswosubmission', 'workshop');
        $text = get_string('assesswosubmissiondesc', 'workshop');
        $mform->addElement('advcheckbox', 'assesswosubmission', $label, $text);
        $mform->setHelpButton('assesswosubmission', array('assesswosubmission', $label, 'workshop'));
        $mform->setAdvanced('assesswosubmission');

        $label = get_string('examplesmode', 'workshop');
        $options = workshop_get_example_modes();
        $mform->addElement('select', 'examplesmode', $label, $options);
        $mform->setDefault('examplesmode', $workshopconfig->examplesmode);
        $mform->setHelpButton('examplesmode', array('examplesmode', $label, 'workshop'));
        $mform->setAdvanced('examplesmode');

        $label = get_string('teacherweight', 'workshop');
        $options = workshop_get_teacher_weights();
        $mform->addElement('select', 'teacherweight', $label, $options);
        $mform->setDefault('teacherweight', 1);
        $mform->setHelpButton('teacherweight', array('teacherweight', $label, 'workshop'));
        $mform->setAdvanced('teacherweight');

        $label = get_string('agreeassessments', 'workshop');
        $text = get_string('agreeassessmentsdesc', 'workshop');
        $mform->addElement('advcheckbox', 'agreeassessments', $label, $text);
        $mform->setHelpButton('agreeassessments', array('agreeassessments', $label, 'workshop'));
        $mform->setAdvanced('agreeassessments');

        $label = get_string('hidegrades', 'workshop');
        $text = get_string('hidegradesdesc', 'workshop');
        $mform->addElement('advcheckbox', 'hidegrades', $label, $text);
        $mform->setHelpButton('hidegrades', array('hidegrades', $label, 'workshop'));
        $mform->setAdvanced('hidegrades');

        $label = get_string('assessmentcomps', 'workshop');
        $levels = array();
        foreach (workshop_get_comparison_levels() as $code => $level) {
            $levels[$code] = $level->name;
        }
        $mform->addElement('select', 'assessmentcomps', $label, $levels);
        $mform->setDefault('assessmentcomps', $workshopconfig->assessmentcomps);
        $mform->setHelpButton('assessmentcomps', array('assessmentcomps', $label, 'workshop'));
        $mform->setAdvanced('assessmentcomps');

/// Access control
        $mform->addElement('header', 'accesscontrol', get_string('accesscontrol', 'workshop'));

        $label = get_string('submissionstart', 'workshop');
        $mform->addElement('date_selector', 'submissionstart', $label, array('optional' => true));
        $mform->setHelpButton('submissionstart', array('submissionstart', $label, 'workshop'));
        $mform->setAdvanced('submissionstart');

        $label = get_string('submissionend', 'workshop');
        $mform->addElement('date_selector', 'submissionend', $label, array('optional' => true));
        $mform->setHelpButton('submissionend', array('submissionend', $label, 'workshop'));
        $mform->setAdvanced('submissionend');

        $label = get_string('assessmentstart', 'workshop');
        $mform->addElement('date_selector', 'assessmentstart', $label, array('optional' => true));
        $mform->setHelpButton('assessmentstart', array('assessmentstart', $label, 'workshop'));
        $mform->setAdvanced('assessmentstart');

        $label = get_string('assessmentend', 'workshop');
        $mform->addElement('date_selector', 'assessmentend', $label, array('optional' => true));
        $mform->setHelpButton('assessmentend', array('assessmentend', $label, 'workshop'));
        $mform->setAdvanced('assessmentend');

        $label = get_string('releasegrades', 'workshop');
        $mform->addElement('date_selector', 'releasegrades', $label, array('optional' => true));
        $mform->setHelpButton('releasegrades', array('releasegrades', $label, 'workshop'));
        $mform->setAdvanced('releasegrades');

        $label = get_string('requirepassword', 'workshop');
        $mform->addElement('passwordunmask', 'password', $label);
        $mform->setType('quizpassword', PARAM_TEXT);
        $mform->setHelpButton('password', array('requirepassword', $label, 'workshop'));
        $mform->setAdvanced('password');


/// Common module settinga, Restrict availability, Activity completion etc. ----
        // add standard elements, common to all modules
        $this->standard_coursemodule_elements();

/// Save and close, Save, Cancel -----------------------------------------------
        // add standard buttons, common to all modules
        $this->add_action_buttons();

    }
}
