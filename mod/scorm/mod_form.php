<?php
require_once ($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/scorm/locallib.php');

class mod_scorm_mod_form extends moodleform_mod {

    function definition() {

        global $CFG, $COURSE, $SCORM_GRADE_METHOD, $SCORM_WHAT_GRADE;
        $mform    =& $this->_form;

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

// Name
        $mform->addElement('text', 'name', get_string('name'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

// Summary
        $mform->addElement('htmleditor', 'summary', get_string('summary'));
        $mform->setType('summary', PARAM_RAW);
        $mform->addRule('summary', get_string('required'), 'required', null, 'client');
        $mform->setHelpButton('summary', array('writing', 'questions', 'richtext'), false, 'editorhelpbutton');

// Reference
        $mform->addElement('choosecoursefile', 'reference', get_string('package','scorm'));
        $mform->setType('reference', PARAM_RAW);  // We need to find a better PARAM
        $mform->addRule('reference', get_string('required'), 'required', null, 'client');
        $mform->setHelpButton('reference',array('package', get_string('package', 'scorm')), 'scorm');

//-------------------------------------------------------------------------------
// Other Settings
        $mform->addElement('header', 'advanced', get_string('othersettings'));

// Grading
        $mform->addElement('static', 'grade', get_string('grades'));
// Grade Method
        $mform->addElement('select', 'grademethod', get_string('grademethod', 'scorm'), $SCORM_GRADE_METHOD);
        $mform->setHelpButton('grademethod', array('grademethod',get_string('grademethod', 'scorm')), 'scorm');
        $mform->setDefault('grademethod', 0);

// Maximum Grade
        for ($i=0; $i<=100; $i++) {
          $grades[$i] = "$i";
        }
        $mform->addElement('select', 'maxgrade', get_string('maximumgrade'), $grades);
        $mform->setDefault('maxgrade', 0);
        $mform->disabledIf('maxgrade', 'grademethod','eq',GRADESCOES);

// Attempts
        $mform->addElement('static', '', '' ,'<hr />');
        $mform->addElement('static', 'attempts', get_string('attempts','scorm'));

// Max Attempts
        $attempts = array(0 => get_string('nolimit','scorm'));
        for ($i=1; $i<=$CFG->scorm_maxattempts; $i++) {
            if ($i == 1) {
                $attempts[$i] = $i . ' ' . get_string('attempt','scorm');
            } else {
                $attempts[$i] = $i . ' ' . get_string('attempts','scorm');
            }
        }
        $mform->addElement('select', 'maxattempt', get_string('maximumattempts', 'scorm'), $attempts);
        $mform->setHelpButton('maxattempt', array('maxattempt',get_string('maximumattempts', 'scorm')), 'scorm');
        $mform->setDefault('maxattempt', 1);

// What Grade
        $mform->addElement('select', 'whatgrade', get_string('whatgrade', 'scorm'), $SCORM_WHAT_GRADE);
        $mform->disabledIf('whatgrade', 'maxattempt','eq',1);
        $mform->setHelpButton('whatgrade', array('whatgrade',get_string('whatgrade', 'scorm')), 'scorm');
        $mform->setDefault('whatgrade', 0);
        $mform->setAdvanced('whatgrade');

// Activation period
        /*$mform->addElement('static', '', '' ,'<hr />');
        $mform->addElement('static', 'activation', get_string('activation','scorm'));
        $datestartgrp = array();
        $datestartgrp[] = &$mform->createElement('date_time_selector', 'startdate');
        $datestartgrp[] = &$mform->createElement('checkbox', 'startdisabled', null, get_string('disable'));
        $mform->addGroup($datestartgrp, 'startdategrp', get_string('from'), ' ', false);
        $mform->setDefault('startdate', 0);
        $mform->setDefault('startdisabled', 1);
        $mform->disabledIf('startdategrp', 'startdisabled', 'checked');

        $dateendgrp = array();
        $dateendgrp[] = &$mform->createElement('date_time_selector', 'enddate');
        $dateendgrp[] = &$mform->createElement('checkbox', 'enddisabled', null, get_string('disable'));
        $mform->addGroup($dateendgrp, 'dateendgrp', get_string('to'), ' ', false);
        $mform->setDefault('enddate', 0);
        $mform->setDefault('enddisabled', 1);
        $mform->disabledIf('dateendgrp', 'enddisabled', 'checked'); */

// Stage Size
        $mform->addElement('static', '', '' ,'<hr />');
        $mform->addElement('static', 'stagesize', get_string('stagesize','scorm'));
        $mform->setHelpButton('stagesize', array('stagesize',get_string('stagesize', 'scorm')), 'scorm');
// Width
        $mform->addElement('text', 'width', get_string('width','scorm'),'maxlength="5" size="5"');
        $mform->setDefault('width', $CFG->scorm_framewidth);
        $mform->setType('width', PARAM_INT);
        
// Height
        $mform->addElement('text', 'height', get_string('height','scorm'),'maxlength="5" size="5"');
        $mform->setDefault('height', $CFG->scorm_frameheight);
        $mform->setType('height', PARAM_INT);

// Framed / Popup Window
        $options = array();
        $options[0] = get_string('iframe', 'scorm');
        $options[1] = get_string('popup', 'scorm');
        $mform->addElement('select', 'popup', get_string('display','scorm'), $options);
        $mform->setDefault('popup', 0);
        $mform->setAdvanced('popup');

// Window Options
        $winoptgrp = array();
        $winoptgrp[] = &$mform->createElement('checkbox', 'resizable', '', get_string('resizable', 'scorm'));
        $winoptgrp[] = &$mform->createElement('checkbox', 'scrollbars', '', get_string('scrollbars', 'scorm'));
        $winoptgrp[] = &$mform->createElement('checkbox', 'directories', '', get_string('directories', 'scorm'));
        $winoptgrp[] = &$mform->createElement('checkbox', 'location', '', get_string('location', 'scorm'));
        $winoptgrp[] = &$mform->createElement('checkbox', 'menubar', '', get_string('menubar', 'scorm'));
        $winoptgrp[] = &$mform->createElement('checkbox', 'toolbar', '', get_string('toolbar', 'scorm'));
        $winoptgrp[] = &$mform->createElement('checkbox', 'status', '', get_string('status', 'scorm'));
        $mform->addGroup($winoptgrp, 'winoptgrp', get_string('options'), '<br />', false);
        $mform->setDefault('resizable', 1);
        $mform->setDefault('scrollbars', 1);
        $mform->setDefault('directories', 0);
        $mform->setDefault('location', 0);
        $mform->setDefault('menubar', 0);
        $mform->setDefault('toolbar', 0);
        $mform->setDefault('status', 0);
        $mform->setAdvanced('winoptgrp');
        $mform->disabledIf('winoptgrp', 'popup', 'eq', 0);

// Skip view page
        $options = array();
        $options[0]=get_string('never');
        $options[1]=get_string('firstaccess','scorm');
        $options[2]=get_string('always');
        $mform->addElement('select', 'skipview', get_string('skipview', 'scorm'), $options);
        $mform->setHelpButton('skipview', array('skipview',get_string('skipview', 'scorm')), 'scorm');
        $mform->setDefault('skipview', 1);
        $mform->setAdvanced('skipview');

// Hide Browse
        $mform->addElement('selectyesno', 'hidebrowse', get_string('hidebrowse', 'scorm'));
        $mform->setHelpButton('hidebrowse', array('hidebrowse',get_string('hidebrowse', 'scorm')), 'scorm');
        $mform->setDefault('hidebrowse', 0);
        $mform->setAdvanced('hidebrowse');

// Toc display
        $options = array();
        $options[1]=get_string('hidden','scorm');
        $options[0]=get_string('sided','scorm');
        $options[2]=get_string('popupmenu','scorm');
        $mform->addElement('select', 'hidetoc', get_string('hidetoc', 'scorm'), $options);
        $mform->setDefault('hidetoc', 0);
        $mform->setAdvanced('hidetoc');

// Hide Navigation panel
        $mform->addElement('selectyesno', 'hidenav', get_string('hidenav', 'scorm'));
        $mform->setDefault('hidenav', 0);
        $mform->setAdvanced('hidenav');

// Autocontinue
        $mform->addElement('selectyesno', 'auto', get_string('autocontinue', 'scorm'));
        $mform->setHelpButton('auto', array('autocontinue',get_string('autocontinue', 'scorm')), 'scorm');
        $mform->setDefault('auto', 0);
        $mform->setAdvanced('auto');

// Update packages timing
        $options = array();
        $options[0]=get_string('never','scorm');
        $options[1]=get_string('onchanges','scorm');
        $options[2]=get_string('everyday','scorm');
        $options[3]=get_string('everytime','scorm');
        $mform->addElement('select', 'updatefreq', get_string('updatefreq', 'scorm'), $options);
        $mform->setDefault('updatefreq', 0);
        $mform->setAdvanced('updatefreq');

//-------------------------------------------------------------------------------
// Hidden Settings
        $mform->addElement('hidden', 'datadir', null);
        $mform->addElement('hidden', 'pkgtype', null);
        $mform->addElement('hidden', 'launch', null);
        $mform->addElement('hidden', 'redirect', null);
        $mform->addElement('hidden', 'redirecturl', null);


//-------------------------------------------------------------------------------
        $this->standard_coursemodule_elements();
//-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons();

    }

    function defaults_preprocessing(&$default_values) {
        global $COURSE;

        if (isset($default_values['popup']) && ($default_values['popup'] == 1) && isset($default_values['options'])) {
            $options = explode(',',$default_values['options']);
            foreach ($options as $option) {
                list($element,$value) = explode('=',$option);
                $element = trim($element);
                $default_values[$element] = trim($value); 
            }
        }
        if (isset($default_values['grademethod'])) {
            $default_values['whatgrade'] = intval($default_values['grademethod'] / 10);
            $default_values['grademethod'] = $default_values['grademethod'] % 10;
        }
        if (isset($default_value['width']) && (strpos($default_value['width'],'%') === false) && ($default_value['width'] <= 100)) {
            $default_value['width'] .= '%';
        }
        if (isset($default_value['width']) && (strpos($default_value['height'],'%') === false) && ($default_value['height'] <= 100)) {
            $default_value['height'] .= '%';
        }
        $scorms = get_all_instances_in_course('scorm', $COURSE);
        $coursescorm = current($scorms);
        if (($COURSE->format == 'scorm') && ((count($scorms) == 0) || ($default_values['instance'] == $coursescorm->id))) {
            $default_values['redirect'] = 'yes';
            $default_values['redirecturl'] = '../course/view.php?id='.$default_values['course'];    
        } else {
            $default_values['redirect'] = 'no';
            $default_values['redirecturl'] = '../mod/scorm/view.php?id='.$default_values['coursemodule'];
        }
        if (isset($default_values['version'])) {
            $default_values['pkgtype'] = (substr($default_values['version'],0,5) == 'SCORM') ? 'scorm':'aicc';
        }
        if (isset($default_values['instance'])) {
            $default_values['datadir'] = $default_values['instance'];
        }
    }

    function validation($data) {
        $validate = scorm_validate($data);

        if ($validate->result) {
            return true;
        } else {
            return $validate->errors;
        }
    }

}
?>
