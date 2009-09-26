<?php
require_once ($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/scorm/locallib.php');

class mod_scorm_mod_form extends moodleform_mod {

    function definition() {

        global $CFG, $COURSE;
        $mform    =& $this->_form;
        if (isset($CFG->slasharguments) && !$CFG->slasharguments) {
            $mform->addElement('static', '', '',notify(get_string('slashargs', 'scorm'), 'notifyproblem', 'center', true));
        }
        $zlib = ini_get('zlib.output_compression'); //check for zlib compression - if used, throw error because of IE bug. - SEE MDL-16185
        if (isset($zlib) && $zlib) {
            $mform->addElement('static', '', '',notify(get_string('zlibwarning', 'scorm'), 'notifyproblem', 'center', true));
        }
//-------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

// Name
        $mform->addElement('text', 'name', get_string('name'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', null, 'required', null, 'client');

// Summary
        $mform->addElement('htmleditor', 'summary', get_string('summary'));
        $mform->setType('summary', PARAM_RAW);
        $mform->addRule('summary', get_string('required'), 'required', null, 'client');
        $mform->setHelpButton('summary', array('writing', 'questions', 'richtext'), false, 'editorhelpbutton');

// Reference
        $mform->addElement('choosecoursefileorimsrepo', 'reference', get_string('package','scorm'));
        $mform->setType('reference', PARAM_RAW);  // We need to find a better PARAM
        $mform->addRule('reference', get_string('required'), 'required');
        $mform->setHelpButton('reference',array('package', get_string('package', 'scorm'), 'scorm'));

//-------------------------------------------------------------------------------
// Other Settings
        $mform->addElement('header', 'advanced', get_string('othersettings', 'form'));

// Grade Method
        $mform->addElement('select', 'grademethod', get_string('grademethod', 'scorm'), scorm_get_grade_method_array());
        $mform->setHelpButton('grademethod', array('grademethod',get_string('grademethod', 'scorm'),'scorm'));
        $mform->setDefault('grademethod', $CFG->scorm_grademethod);

// Maximum Grade
        for ($i=0; $i<=100; $i++) {
          $grades[$i] = "$i";
        }
        $mform->addElement('select', 'maxgrade', get_string('maximumgrade'), $grades);
        $mform->setDefault('maxgrade', $CFG->scorm_maxgrade);
        $mform->disabledIf('maxgrade', 'grademethod','eq',GRADESCOES);

// Attempts
        $mform->addElement('static', '', '' ,'<hr />');

// Max Attempts
        $mform->addElement('select', 'maxattempt', get_string('maximumattempts', 'scorm'), scorm_get_attempts_array());
        $mform->setHelpButton('maxattempt', array('maxattempt',get_string('maximumattempts', 'scorm'), 'scorm'));
        $mform->setDefault('maxattempt', $CFG->scorm_maxattempts);

// What Grade
        $mform->addElement('select', 'whatgrade', get_string('whatgrade', 'scorm'), scorm_get_what_grade_array());
        $mform->disabledIf('whatgrade', 'maxattempt','eq',1);
        $mform->setHelpButton('whatgrade', array('whatgrade',get_string('whatgrade', 'scorm'), 'scorm'));
        $mform->setDefault('whatgrade', $CFG->scorm_whatgrade);
        $mform->setAdvanced('whatgrade');

// Activation period
/*        $mform->addElement('static', '', '' ,'<hr />');
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
        $mform->disabledIf('dateendgrp', 'enddisabled', 'checked');
*/
// Stage Size
        $mform->addElement('static', '', '' ,'<hr />');
        $mform->addElement('static', 'stagesize', get_string('stagesize','scorm'));
        $mform->setHelpButton('stagesize', array('stagesize',get_string('stagesize', 'scorm'), 'scorm'));
// Width
        $mform->addElement('text', 'width', get_string('width','scorm'),'maxlength="5" size="5"');
        $mform->setDefault('width', $CFG->scorm_framewidth);
        $mform->setType('width', PARAM_INT);
        
// Height
        $mform->addElement('text', 'height', get_string('height','scorm'),'maxlength="5" size="5"');
        $mform->setDefault('height', $CFG->scorm_frameheight);
        $mform->setType('height', PARAM_INT);

// Framed / Popup Window
        $mform->addElement('select', 'popup', get_string('display','scorm'), scorm_get_popup_display_array());
        $mform->setDefault('popup', $CFG->scorm_popup);
        $mform->setAdvanced('popup');

// Window Options
        $winoptgrp = array();
        foreach(scorm_get_popup_options_array() as $key => $value){
            $winoptgrp[] = &$mform->createElement('checkbox', $key, '', get_string($key, 'scorm'));
            $mform->setDefault($key, $value);
        }
        $mform->addGroup($winoptgrp, 'winoptgrp', get_string('options','scorm'), '<br />', false);
        $mform->setAdvanced('winoptgrp');
        $mform->disabledIf('winoptgrp', 'popup', 'eq', 0);

// Skip view page
        $mform->addElement('select', 'skipview', get_string('skipview', 'scorm'), scorm_get_skip_view_array());
        $mform->setHelpButton('skipview', array('skipview',get_string('skipview', 'scorm'), 'scorm'));
        $mform->setDefault('skipview', $CFG->scorm_skipview);
        $mform->setAdvanced('skipview');

// Hide Browse
        $mform->addElement('selectyesno', 'hidebrowse', get_string('hidebrowse', 'scorm'));
        $mform->setHelpButton('hidebrowse', array('hidebrowse',get_string('hidebrowse', 'scorm'), 'scorm'));
        $mform->setDefault('hidebrowse', $CFG->scorm_hidebrowse);
        $mform->setAdvanced('hidebrowse');

// Toc display
        $mform->addElement('select', 'hidetoc', get_string('hidetoc', 'scorm'), scorm_get_hidetoc_array());
        $mform->setDefault('hidetoc', $CFG->scorm_hidetoc);
        $mform->setAdvanced('hidetoc');

// Hide Navigation panel
        $mform->addElement('selectyesno', 'hidenav', get_string('hidenav', 'scorm'));
        $mform->setDefault('hidenav', $CFG->scorm_hidenav);
        $mform->setAdvanced('hidenav');

// Autocontinue
        $mform->addElement('selectyesno', 'auto', get_string('autocontinue', 'scorm'));
        $mform->setHelpButton('auto', array('autocontinue',get_string('autocontinue', 'scorm'), 'scorm'));
        $mform->setDefault('auto', $CFG->scorm_auto);
        $mform->setAdvanced('auto');

// Update packages timing
        $mform->addElement('select', 'updatefreq', get_string('updatefreq', 'scorm'), scorm_get_updatefreq_array());
        $mform->setDefault('updatefreq', $CFG->scorm_updatefreq);
        $mform->setAdvanced('updatefreq');

//-------------------------------------------------------------------------------
// Hidden Settings
        $mform->addElement('hidden', 'datadir', null);
        $mform->setType('datadir', PARAM_RAW);
        $mform->addElement('hidden', 'pkgtype', null);
        $mform->setType('pkgtype', PARAM_RAW);
        $mform->addElement('hidden', 'launch', null);
        $mform->setType('launch', PARAM_RAW);
        $mform->addElement('hidden', 'redirect', null);
        $mform->setType('redirect', PARAM_RAW);
        $mform->addElement('hidden', 'redirecturl', null);
        $mform->setType('redirecturl', PARAM_RAW);


//-------------------------------------------------------------------------------
        $features = new stdClass;
        $features->groups = false;
        $features->groupings = true;
        $features->groupmembersonly = true;
        $this->standard_coursemodule_elements($features);
//-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons();

    }

    function data_preprocessing(&$default_values) {
        global $COURSE;

        if (isset($default_values['popup']) && ($default_values['popup'] == 1) && isset($default_values['options'])) {
            if (!empty($default_values['options'])) {
                $options = explode(',',$default_values['options']);
                foreach ($options as $option) {
                    list($element,$value) = explode('=',$option);
                    $element = trim($element);
                    $default_values[$element] = trim($value); 
                }
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

    function validation($data, $files) {
        $errors = parent::validation($data, $files);

        $validate = scorm_validate($data);

        if (!$validate->result) {
            $errors = $errors + $validate->errors;
        }

        return $errors;
    }
    //need to translate the "options" field.
    function set_data($default_values) {
        if (is_object($default_values)) {
            if (!empty($default_values->options)) {
                $options = explode(',', $default_values->options);
                foreach ($options as $option) {
                    $opt = explode('=', $option);
                    if (isset($opt[1])) {
                        $default_values->$opt[0] = $opt[1];
                    }
                }
            }
            $default_values = (array)$default_values;
        }
        $this->data_preprocessing($default_values);
        parent::set_data($default_values); //never slashed for moodleform_mod
    }
}
?>