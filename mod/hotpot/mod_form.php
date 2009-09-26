<?php
require_once ($CFG->dirroot.'/course/moodleform_mod.php');
require_once ($CFG->dirroot.'/mod/hotpot/lib.php');

$HOTPOT_TEXTSOURCE = array(
    HOTPOT_TEXTSOURCE_QUIZ => get_string('textsourcequiz', 'hotpot'),
    HOTPOT_TEXTSOURCE_FILENAME => get_string('textsourcefilename', 'hotpot'),
    HOTPOT_TEXTSOURCE_FILEPATH => get_string('textsourcefilepath', 'hotpot'),
    HOTPOT_TEXTSOURCE_SPECIFIC => get_string('textsourcespecific', 'hotpot')
);

class mod_hotpot_mod_form extends moodleform_mod {
    // documentation on formslib.php here:
    // http://docs.moodle.org/en/Development:lib/formslib.php_Form_Definition

    function definition() {
        // TO DO
        // =====
        // $mform->setType('name', PARAM_xxx);
        // $mform->setDefault('name', array('elementhelpfilename', get_string('helpicontitlestring', 'hotpot'), 'hotpot'));

        global $CFG, $COURSE;
        global $HOTPOT_FEEDBACK, $HOTPOT_GRADEMETHOD, $HOTPOT_LOCATION;
        global $HOTPOT_NAVIGATION, $HOTPOT_OUTPUTFORMAT, $HOTPOT_TEXTSOURCE;

        $mform =&$this->_form;

        // initialize values for $hours, $minutes and $seconds
        $hours = array();
        $minutes = array();
        $seconds = array();
        for ($i=0; $i<60; $i++) {
            $str = sprintf('%02d', $i);
            if ($i<24) {
                $hours[$i] = $str;
            }
            $minutes[$i] = $str;
            $seconds[$i] = $str;
        }

//-----------------------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));
//-----------------------------------------------------------------------------------------------

// Name
        global $form;
        if (isset($form->add)) {
            // new HotPot
            $elements = array();
            $elements[] = &$mform->createElement('select', 'namesource', '', $HOTPOT_TEXTSOURCE);
            $elements[] = &$mform->createElement('text', 'name', '', array('size' => '40'));
            $mform->addGroup($elements, 'name_elements', get_string('name'), array(' '), false);
            $mform->disabledIf('name_elements', 'namesource', 'ne', HOTPOT_TEXTSOURCE_SPECIFIC);
            // $mform->setAdvanced('name_elements');
        } else {
            // existing HotPot
            $mform->addElement('hidden', 'namesource', HOTPOT_TEXTSOURCE_SPECIFIC);
            $mform->setType('namesource', PARAM_RAW);
            $mform->addElement('text', 'name', get_string('name'), array('size' => '40'));
        }
        $mform->setType('namesource', PARAM_INT);
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }

// Location
        $sitecontext = get_context_instance(CONTEXT_SYSTEM);
        if (has_capability('moodle/course:managefiles', $sitecontext)) {
            $site = get_site();
            if ($COURSE->id==$site->id) {
                $id = $site->id;
                $location = HOTPOT_LOCATION_SITEFILES;
            } else {
                $id = "'+(getObjValue(this.form.location)==".HOTPOT_LOCATION_SITEFILES."?".$site->id.":".$COURSE->id.")+'";
                $location = '';
            }
        } else { // ordinary teacher or content creator
                $id = $COURSE->id;
                $location = HOTPOT_LOCATION_COURSEFILES;
        }
        if (array_key_exists($location, $HOTPOT_LOCATION)) {
            $mform->addElement('hidden', 'location', $location);
            $mform->setType('location', PARAM_RAW);
        } else { // admin can select from "site" or "course" files
           $mform->addElement('select', 'location', get_string('location', 'hotpot'), $HOTPOT_LOCATION);
        }
        $mform->setType('location', PARAM_INT);

// Reference
        // create "Choose file" button
        $choosefile_button = $mform->createElement('button', 'popup', get_string('chooseafile', 'resource') .' ...');

        // create a 'reference' group of form elements, comprising text box + buttons
        $elements = array();
        $elements[] = $mform->createElement('text', 'reference', '', array('size'=>'60'));
        $elements[] = &$choosefile_button;
        $mform->addGroup($elements, 'reference_elements', get_string('filename', 'resource'), ' ' , false);

        // set attributes on the button
        if ($choosefile_button) {
            $wdir = "'+getDir(this.form.reference.value)+'";
            $url="/files/index.php?id=$id&wdir=$wdir&choose=id_reference";
            $options = 'menubar=0,location=0,scrollbars,resizable,width=750,height=500';
            $attributes = array(
                'title'=>get_string('chooseafile', 'resource'),
                'onclick'=>"return openpopup('$url', '".$choosefile_button->getName()."', '$options', 0);"
            );
            $choosefile_button->updateAttributes($attributes);
        }
        $mform->setType('reference', PARAM_TEXT);

// Summary
        if (isset($form->add)) {
            // new HotPot
            $elements = array();
            $elements[] = &$mform->createElement('select', 'summarysource', '', $HOTPOT_TEXTSOURCE);
            $elements[] = &$mform->createElement('htmleditor', 'summary', '');
            $mform->addGroup($elements, 'summary_elements', get_string('summary'), array(' '), false);
            $mform->setAdvanced('summary_elements');
        } else {
            // existing HotPot
            $mform->addElement('hidden', 'summarysource', HOTPOT_TEXTSOURCE_SPECIFIC);
            $mform->setType('summarysource', PARAM_RAW);
            $mform->addElement('htmleditor', 'summary', get_string('summary'));
            $mform->setType('summary', PARAM_RAW);
            $mform->setHelpButton('summary', array('writing', 'questions', 'richtext'), false, 'editorhelpbutton');
            $mform->setAdvanced('summary');
        }
        $mform->setType('summarysource', PARAM_INT);
        $mform->setType('summary', PARAM_RAW);

// Add/Update quiz chain?
        if (isset($form->add)) {
            $quizchain = 'addquizchain';
        } else {
            $quizchain = 'updatequizchain';
        }
        $mform->addElement('selectyesno', 'quizchain', get_string($quizchain, 'hotpot'));
        $mform->setDefault('quizchain', get_user_preferences("hotpot_$quizchain", HOTPOT_NO));
        $mform->setHelpButton('quizchain', array($quizchain, get_string($quizchain, 'hotpot'), 'hotpot'));
        // $mform->setAdvanced('quizchain');

//-----------------------------------------------------------------------------------------------
        $mform->addElement('header', 'displayhdr', get_string('display', 'form'));
//-----------------------------------------------------------------------------------------------

// Output format
        $mform->addElement('select', 'outputformat', get_string('outputformat', 'hotpot'), $HOTPOT_OUTPUTFORMAT);
        $mform->setDefault('outputformat', get_user_preferences('hotpot_outputformat', HOTPOT_OUTPUTFORMAT_BEST));
        $mform->setHelpButton('outputformat', array('outputformat', get_string('outputformat', 'hotpot'), 'hotpot'));

// Navigation
        $mform->addElement('select', 'navigation', get_string('navigation', 'hotpot'), $HOTPOT_NAVIGATION);
        $mform->setDefault('navigation', get_user_preferences('hotpot_navigation', HOTPOT_NAVIGATION_BAR));
        $mform->setHelpButton('navigation', array('navigation', get_string('navigation', 'hotpot'), 'hotpot'));

// Use Moode player ?
        $mform->addElement('selectyesno', 'forceplugins', get_string('forceplugins', 'hotpot'));
        $mform->setDefault('forceplugins', get_user_preferences('hotpot_forceplugins', HOTPOT_NO));
        $mform->setHelpButton('forceplugins', array('forceplugins', get_string('forceplugins', 'hotpot'), 'hotpot'));
        // $mform->setAdvanced('forceplugins');

// Student feedback
        $elements = array();
        $elements[] = &$mform->createElement('select', 'studentfeedback', '', $HOTPOT_FEEDBACK);
        $elements[] = &$mform->createElement('text', 'studentfeedbackurl', '', array('size'=>'50'));
        $mform->addGroup($elements, 'studentfeedback_elements', get_string('studentfeedback', 'hotpot'), array(' '), false);
        $mform->setHelpButton('studentfeedback_elements', array('studentfeedback', get_string('studentfeedback', 'hotpot'), 'hotpot'));
        $mform->disabledIf('studentfeedback_elements', 'studentfeedback', 'eq', HOTPOT_FEEDBACK_NONE);
        $mform->disabledIf('studentfeedback_elements', 'studentfeedback', 'eq', HOTPOT_FEEDBACK_MOODLEFORUM);
        $mform->disabledIf('studentfeedback_elements', 'studentfeedback', 'eq', HOTPOT_FEEDBACK_MOODLEMESSAGING);
        // $mform->setAdvanced('studentfeedback_elements');
        $mform->setType('studentfeedbackurl', PARAM_URL);

// Show next quiz ?
        $mform->addElement('selectyesno', 'shownextquiz', get_string('shownextquiz', 'hotpot'));
        $mform->setDefault('shownextquiz', get_user_preferences('hotpot_shownextquiz', HOTPOT_NO));
        $mform->setHelpButton('shownextquiz', array('shownextquiz', get_string('shownextquiz', 'hotpot'), 'hotpot'));
        // $mform->setAdvanced('shownextquiz');

//-----------------------------------------------------------------------------------------------
        $mform->addElement('header', 'accesscontrolhdr', get_string('accesscontrol', 'lesson'));
//-----------------------------------------------------------------------------------------------

// Open time
        $mform->addElement('date_time_selector', 'timeopen', get_string('quizopen', 'quiz'), array('optional'=>true));
        $mform->setHelpButton('timeopen', array('timeopen', get_string('quizopen', 'quiz'), 'quiz'));

// Close time
        $mform->addElement('date_time_selector', 'timeclose', get_string('quizclose', 'quiz'), array('optional'=>true));
        $mform->setHelpButton('timeclose', array('timeopen', get_string('quizclose', 'quiz'), 'quiz'));

// Password
        $mform->addElement('text', 'password', get_string('requirepassword', 'quiz'));
        $mform->setType('password', PARAM_TEXT);
        $mform->setHelpButton('password', array('requirepassword', get_string('requirepassword', 'quiz'), 'quiz'));
        // $mform->setAdvanced('password');

// Subnet
        $mform->addElement('text', 'subnet', get_string('requiresubnet', 'quiz'));
        $mform->setType('subnet', PARAM_TEXT);
        $mform->setHelpButton('subnet', array('requiresubnet', get_string('requiresubnet', 'quiz'), 'quiz'));
        $mform->setDefault('subnet', get_user_preferences('hotpot_subnet'));
        // $mform->setAdvanced('subnet');

// Allow review?
        $mform->addElement('selectyesno', 'review', get_string('allowreview', 'quiz'));
        $mform->setDefault('review', get_user_preferences('hotpot_review', HOTPOT_YES));
        $mform->setHelpButton('review', array('review', get_string('allowreview', 'quiz'), 'quiz'));
        // $mform->setAdvanced('review');

// Maximum number of attempts
        $options = array(
            0 => get_string("attemptsunlimited", "quiz"),
            1 => '1 '.strtolower(get_string("attempt", "quiz"))
        );
        for ($i=2; $i<=10; $i++) {
            $options[$i] = "$i ".strtolower(get_string("attempts", "quiz"));
        }
        $mform->addElement('select', 'attempts', get_string('attemptsallowed', 'quiz'), $options);
        $mform->setDefault('attempts', get_user_preferences('hotpot_attempts', 0)); // 0=unlimited
        $mform->setHelpButton('attempts', array('attempts', get_string('attemptsallowed', 'quiz'), 'quiz'));
        // $mform->setAdvanced('attempts');

//-----------------------------------------------------------------------------------------------
        $mform->addElement('header', 'gradeshdr', get_string('grades', 'grades'));
//-----------------------------------------------------------------------------------------------

// Maximum grading method
        $mform->addElement('select', 'grademethod', get_string('grademethod', 'quiz'), $HOTPOT_GRADEMETHOD);
        $mform->setDefault('grademethod', get_user_preferences('hotpot_grademethod', HOTPOT_GRADEMETHOD_HIGHEST));
        $mform->setHelpButton('grademethod', array('grademethod', get_string('grademethod', 'quiz'), 'quiz'));
        // $mform->setAdvanced('grademethod');

// Maximum grade
        $options = array();
        for ($i=100; $i>=1; $i--) {
            $options[$i] = $i;
        }
        $options[0] = get_string("nograde");
        $mform->addElement('select', 'grade', get_string('maximumgrade'), $options);
        $mform->setDefault('grade', get_user_preferences('hotpot_grade', 100));
        $mform->setHelpButton('grade', array('maxgrade', get_string('maximumgrade'), 'quiz'));
        // $mform->setAdvanced('grade');

// Remove grade item
        if (empty($this->_instance) || ! record_exists('grade_items', 'itemtype', 'mod', 'itemmodule', 'hotpot', 'iteminstance', $this->_instance)) {
            $mform->addElement('hidden', 'removegradeitem', 0);
            $mform->setType('removegradeitem', PARAM_INT);
        } else {
            $mform->addElement('selectyesno', 'removegradeitem', get_string('removegradeitem', 'hotpot'));
            $mform->setHelpButton('removegradeitem', array('removegradeitem', get_string('removegradeitem', 'hotpot'), 'hotpot'));
            //$mform->setAdvanced('removegradeitem');
            $mform->setType('removegradeitem', PARAM_INT);
            // element is only available if grade==0
            $mform->disabledIf('removegradeitem', 'grade', 'selected', 0);
        }

//-----------------------------------------------------------------------------------------------
        $mform->addElement('header', 'reportshdr', get_string('reports'));
//-----------------------------------------------------------------------------------------------

// Enable click reporting?
        $mform->addElement('selectyesno', 'clickreporting', get_string('clickreporting', 'hotpot'));
        $mform->setDefault('clickreporting', get_user_preferences('hotpot_clickreporting', HOTPOT_NO));
        $mform->setHelpButton('clickreporting', array('clickreporting', get_string('clickreporting', 'hotpot'), 'hotpot'));
        // $mform->setAdvanced('clickreporting');

//----------------------------------------------
        $this->standard_coursemodule_elements();
//----------------------------------------------

        $this->add_action_buttons();

        $js = '<script type="text/javascript" src="'.$CFG->wwwroot.'/mod/hotpot/mod_form.js"></script>';
        $mform->addElement('static', 'hotpot_mod_form_js', '', $js);
    }

    function data_preprocessing(&$defaults){
    }

    function validation(&$data) {
        // http://docs.moodle.org/en/Development:lib/formslib.php_Validation
        global $CFG, $COURSE;
        $errors = array();

// location
        if (empty($data['location'])) {
            // this shouldn't happen
            $data['location'] = $COURSE->id;
        } else {
            if ($data['location']==$COURSE->id) {
                // this is normal
            } else if ($data['location']==SITEID && has_capability('moodle/course:managefiles', get_context_instance(CONTEXT_SYSTEM))) {
                // admin can access site files
            } else {
                // location is invalid or missing, so set to default
                $data['location'] = $COURSE->id;
            }
        }

// reference
        if (isset($data['reference'])) {
            $data['reference'] = trim($data['reference']);
        }
        if (empty($data['reference'])) {
            $errors['reference_elements'] = get_string('error_nofilename', 'hotpot');
        } else {
            if (preg_match('|^https?://|', $data['reference'])) {
                // URL
                $errors['reference_elements'] = 'Sorry, handling of URLs is not implemented yet';
            } else {
                // course files
                $filepath = $CFG->dataroot.'/'.$data['location'].'/'.$data['reference'];
                if (! file_exists($filepath)) {
                    $errors['reference_elements'] = get_string('error_pathdoesnotexist', 'hotpot', $filepath);
                } else if (! $data['quizchain'] && ! is_file($filepath)) {
                    $errors['reference_elements'] = get_string('error_folderwithoutquizchain', 'hotpot');
                }
            }
        }

// studentfeedbackurl
        if (empty($data['studentfeedbackurl']) || $data['studentfeedbackurl']=='http://') {
            $data['studentfeedbackurl'] = '';
            $error = false;
            if ($data['studentfeedback']==HOTPOT_FEEDBACK_WEBPAGE) {
                $error = true;
            }
            if ($data['studentfeedback']==HOTPOT_FEEDBACK_FORMMAIL) {
                $error = true;
            }
            if ($error) {
                $errors['studentfeedback_elements']= get_string('error_nofeedbackurlformmail', 'hotpot');
            }
        }

        return $errors;
    }
}
?>