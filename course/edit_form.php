<?php  //$Id$

require_once($CFG->libdir.'/formslib.php');

class course_edit_form extends moodleform {

    function definition() {
        global $USER, $CFG;

        $mform    =& $this->_form;

        $course   = $this->_customdata['course'];
        $category = $this->_customdata['category'];

        $systemcontext = get_context_instance(CONTEXT_SYSTEM);
        $categorycontext = get_context_instance(CONTEXT_COURSECAT, $category->id);

        $disable_meta = false; // basic meta course state protection; server-side security checks not needed

        if (!empty($course)) {
            $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
            $context = $coursecontext;

            if (course_in_meta($course)) {
                $disable_meta = get_string('metaalreadyinmeta');

            } else if ($course->metacourse) {
                if (count_records('course_meta', 'parent_course', $course->id) > 0) {
                    $disable_meta = get_string('metaalreadyhascourses');
                }

            } else {
                $managers = count(get_users_by_capability($coursecontext, 'moodle/course:managemetacourse'));
                $participants = count(get_users_by_capability($coursecontext, 'moodle/course:view'));
                if ($participants > $managers) {
                    $disable_meta = get_string('metaalreadyhasenrolments');
                }
            }
        } else {
            $coursecontext = null;
            $context = $categorycontext;
        }

/// form definition with new course defaults
//--------------------------------------------------------------------------------
        $mform->addElement('header','general', get_string('general', 'form'));

        //must have create course capability in both categories in order to move course
        if (has_capability('moodle/course:create', $categorycontext)) {
            $displaylist = array();
            $parentlist = array();
            make_categories_list($displaylist, $parentlist);
            foreach ($displaylist as $key=>$val) {
                if (!has_capability('moodle/course:create', get_context_instance(CONTEXT_COURSECAT, $key))) {
                    unset($displaylist[$key]);
                }
            }
            $mform->addElement('select', 'category', get_string('category'), $displaylist);
        } else {
            $mform->addElement('hidden', 'category', null);
        }
        $mform->setHelpButton('category', array('coursecategory', get_string('category')));
        $mform->setDefault('category', $category->id);
        $mform->setType('category', PARAM_INT);

        $mform->addElement('text','fullname', get_string('fullname'),'maxlength="254" size="50"');
        $mform->setHelpButton('fullname', array('coursefullname', get_string('fullname')), true);
        $mform->setDefault('fullname', get_string('defaultcoursefullname'));
        $mform->addRule('fullname', get_string('missingfullname'), 'required', null, 'client');
        $mform->setType('fullname', PARAM_MULTILANG);

        $mform->addElement('text','shortname', get_string('shortname'),'maxlength="15" size="10"');
        $mform->setHelpButton('shortname', array('courseshortname', get_string('shortname')), true);
        $mform->setDefault('shortname', get_string('defaultcourseshortname'));
        $mform->addRule('shortname', get_string('missingshortname'), 'required', null, 'client');
        $mform->setType('shortname', PARAM_MULTILANG);

        $mform->addElement('text','idnumber', get_string('idnumbercourse'),'maxlength="100"  size="10"');
        $mform->setHelpButton('idnumber', array('courseidnumber', get_string('idnumbercourse')), true);
        $mform->setType('idnumber', PARAM_RAW);

        $mform->addElement('htmleditor','summary', get_string('summary'), array('rows'=> '10', 'cols'=>'65'));
        $mform->setHelpButton('summary', array('text', get_string('helptext')), true);
        $mform->setDefault('summary', get_string('defaultcoursesummary'));
        $mform->addRule('summary', get_string('missingsummary'), 'required', null, 'client');
        $mform->setType('summary', PARAM_RAW);

        $courseformats = get_list_of_plugins('course/format');
        $formcourseformats = array();
        foreach ($courseformats as $courseformat) {
            $formcourseformats["$courseformat"] = get_string("format$courseformat","format_$courseformat");
            if($formcourseformats["$courseformat"]=="[[format$courseformat]]") {
                $formcourseformats["$courseformat"] = get_string("format$courseformat");
            }
        }
        $mform->addElement('select', 'format', get_string('format'), $formcourseformats);
        $mform->setHelpButton('format', array('courseformats', get_string('courseformats')), true);
        $mform->setDefault('format', 'weeks');

        for ($i=1; $i<=52; $i++) {
          $sectionmenu[$i] = "$i";
        }
        $mform->addElement('select', 'numsections', get_string('numberweeks'), $sectionmenu);
        $mform->setDefault('numsections', 10);

        $mform->addElement('date_selector', 'startdate', get_string('startdate'));
        $mform->setHelpButton('startdate', array('coursestartdate', get_string('startdate')), true);
        $mform->setDefault('startdate', time() + 3600 * 24);

        $choices = array();
        $choices['0'] = get_string('hiddensectionscollapsed');
        $choices['1'] = get_string('hiddensectionsinvisible');
        $mform->addElement('select', 'hiddensections', get_string('hiddensections'), $choices);
        $mform->setHelpButton('hiddensections', array('coursehiddensections', get_string('hiddensections')), true);
        $mform->setDefault('hiddensections', 0);

        $options = range(0, 10);
        $mform->addElement('select', 'newsitems', get_string('newsitemsnumber'), $options);
        $mform->setHelpButton('newsitems', array('coursenewsitems', get_string('newsitemsnumber')), true);
        $mform->setDefault('newsitems', 5);

        $mform->addElement('selectyesno', 'showgrades', get_string('showgrades'));
        $mform->setHelpButton('showgrades', array('coursegrades', get_string('grades')), true);
        $mform->setDefault('showgrades', 1);

        $mform->addElement('selectyesno', 'showreports', get_string('showreports'));
        $mform->setHelpButton('showreports', array('coursereports', get_string('activityreport')), true);
        $mform->setDefault('showreports', 0);

        $choices = get_max_upload_sizes($CFG->maxbytes);
        $mform->addElement('select', 'maxbytes', get_string('maximumupload'), $choices);
        $mform->setHelpButton('maxbytes', array('courseuploadsize', get_string('maximumupload')), true);

        if (!empty($CFG->allowcoursethemes)) {
            $themes=array();
            $themes[''] = get_string('forceno');
            $themes += get_list_of_themes();
            $mform->addElement('select', 'theme', get_string('forcetheme'), $themes);
        }

        $meta=array();
        $meta[0] = get_string('no');
        $meta[1] = get_string('yes');
        if ($disable_meta === false) {
            $mform->addElement('select', 'metacourse', get_string('managemeta'), $meta);
            $mform->setHelpButton('metacourse', array('metacourse', get_string('metacourse')), true);
            $mform->setDefault('metacourse', 0);
        } else {
            // no metacourse element - we do not want to change it anyway!
            $mform->addElement('static', 'nometacourse', get_string('managemeta'),
                ((empty($course->metacourse)) ? $meta[0] : $meta[1]) . " - $disable_meta ");
            $mform->setHelpButton('nometacourse', array('metacourse', get_string('metacourse')), true);
        }

//--------------------------------------------------------------------------------
        $mform->addElement('header','enrolhdr', get_string('enrolments'));

        $choices = array();
        $modules = explode(',', $CFG->enrol_plugins_enabled);
        foreach ($modules as $module) {
            $name = get_string('enrolname', "enrol_$module");
            $plugin = enrolment_factory::factory($module);
            if (method_exists($plugin, 'print_entry')) {
                $choices[$name] = $module;
            }
        }
        asort($choices);
        $choices = array_flip($choices);
        $choices = array_merge(array('' => get_string('sitedefault').' ('.get_string('enrolname', "enrol_$CFG->enrol").')'), $choices);
        $mform->addElement('select', 'enrol', get_string('enrolmentplugins'), $choices);
        $mform->setHelpButton('enrol', array('courseenrolmentplugins', get_string('enrolmentplugins')), true);


        $roles = get_assignable_roles($context);
        if (!empty($course)) {
            // add current default role, so that it is selectable even when user can not assign it
            if ($current_role = get_record('role', 'id', $course->defaultrole)) {
                $roles[$current_role->id] = strip_tags(format_string($current_role->name, true));
            }
        }
        $choices = array();
        if ($sitedefaultrole = get_record('role', 'id', $CFG->defaultcourseroleid)) {
            $choices[0] = get_string('sitedefault').' ('.$sitedefaultrole->name.')';
        } else {
            $choices[0] = get_string('sitedefault');
        }
        $choices = $choices + $roles;

        // fix for MDL-9197
        foreach ($choices as $choiceid => $choice) {
            $choices[$choiceid] = format_string($choice);
        }

        $mform->addElement('select', 'defaultrole', get_string('defaultrole', 'role'), $choices);
        $mform->setDefault('defaultrole', 0);


        $radio = array();
        $radio[] = &MoodleQuickForm::createElement('radio', 'enrollable', null, get_string('no'), 0);
        $radio[] = &MoodleQuickForm::createElement('radio', 'enrollable', null, get_string('yes'), 1);
        $radio[] = &MoodleQuickForm::createElement('radio', 'enrollable', null, get_string('enroldate'), 2);
        $mform->addGroup($radio, 'enrollable', get_string('enrollable'), ' ', false);
        $mform->setHelpButton('enrollable', array('courseenrollable2', get_string('enrollable')), true);
        $mform->setDefault('enrollable', 1);

        $enroldatestartgrp = array();
        $enroldatestartgrp[] = &MoodleQuickForm::createElement('date_selector', 'enrolstartdate');
        $enroldatestartgrp[] = &MoodleQuickForm::createElement('checkbox', 'enrolstartdisabled', null, get_string('disable'));
        $mform->addGroup($enroldatestartgrp, 'enrolstartdategrp', get_string('enrolstartdate'), ' ', false);
        $mform->setDefault('enrolstartdate', 0);
        $mform->setDefault('enrolstartdisabled', 1);
        $mform->disabledIf('enrolstartdategrp', 'enrolstartdisabled', 'checked');

        $enroldateendgrp = array();
        $enroldateendgrp[] = &MoodleQuickForm::createElement('date_selector', 'enrolenddate');
        $enroldateendgrp[] = &MoodleQuickForm::createElement('checkbox', 'enrolenddisabled', null, get_string('disable'));
        $mform->addGroup($enroldateendgrp, 'enroldateendgrp', get_string('enrolenddate'), ' ', false);
        $mform->setDefault('enrolenddate', 0);
        $mform->setDefault('enrolenddisabled', 1);
        $mform->disabledIf('enroldateendgrp', 'enrolenddisabled', 'checked');

        $periodmenu=array();
        $periodmenu[0] = get_string('unlimited');
        for ($i=1; $i<=365; $i++) {
            $seconds = $i * 86400;
            $periodmenu[$seconds] = get_string('numdays', '', $i);
        }
        $mform->addElement('select', 'enrolperiod', get_string('enrolperiod'), $periodmenu);
        $mform->setDefault('enrolperiod', 0);


//--------------------------------------------------------------------------------
        $mform->addElement('header','expirynotifyhdr', get_string('expirynotify'));

        $choices = array();
        $choices['0'] = get_string('no');
        $choices['1'] = get_string('yes');
        $mform->addElement('select', 'expirynotify', get_string('notify'), $choices);
        $mform->setHelpButton('expirynotify', array('expirynotify', get_string('expirynotify')), true);
        $mform->setDefault('expirynotify', 0);

        $mform->addElement('select', 'notifystudents', get_string('expirynotifystudents'), $choices);
        $mform->setHelpButton('notifystudents', array('expirynotifystudents', get_string('expirynotifystudents')), true);
        $mform->setDefault('notifystudents', 0);

        $thresholdmenu=array();
        for ($i=1; $i<=30; $i++) {
            $seconds = $i * 86400;
            $thresholdmenu[$seconds] = get_string('numdays', '', $i);
        }
        $mform->addElement('select', 'expirythreshold', get_string('expirythreshold'), $thresholdmenu);
        $mform->setHelpButton('expirythreshold', array('expirythreshold', get_string('expirythreshold')), true);
        $mform->setDefault('expirythreshold', 10 * 86400);

//--------------------------------------------------------------------------------
        $mform->addElement('header','', get_string('groups', 'group'));

        $choices = array();
        $choices[NOGROUPS] = get_string('no');
        $choices[SEPARATEGROUPS] = get_string('separate');
        $choices[VISIBLEGROUPS] = get_string('visible');
        $mform->addElement('select', 'groupmode', get_string('groupmode'), $choices);
        $mform->setHelpButton('groupmode', array('groupmode', get_string('groupmode')), true);
        $mform->setDefault('groupmode', 0);

        $choices = array();
        $choices['0'] = get_string('no');
        $choices['1'] = get_string('yes');
        $mform->addElement('select', 'groupmodeforce', get_string('force'), $choices);
        $mform->setHelpButton('groupmodeforce', array('groupmodeforce', get_string('groupmodeforce')), true);
        $mform->setDefault('groupmodeforce', 0);

//--------------------------------------------------------------------------------
        $mform->addElement('header','', get_string('availability'));

        $choices = array();
        $choices['0'] = get_string('courseavailablenot');
        $choices['1'] = get_string('courseavailable');
        $mform->addElement('select', 'visible', get_string('availability'), $choices);
        $mform->setHelpButton('visible', array('courseavailability', get_string('availability')), true);
        $mform->setDefault('visible', 1);

        $mform->addElement('passwordunmask', 'enrolpassword', get_string('enrolmentkey'), 'size="25"');
        $mform->setHelpButton('enrolpassword', array('enrolmentkey', get_string('enrolmentkey')), true);
        $mform->setDefault('enrolpassword', '');
        $mform->setType('enrolpassword', PARAM_RAW);

        $choices = array();
        $choices['0'] = get_string('guestsno');
        $choices['1'] = get_string('guestsyes');
        $choices['2'] = get_string('guestskey');
        $mform->addElement('select', 'guest', get_string('opentoguests'), $choices);
        $mform->setHelpButton('guest', array('guestaccess', get_string('opentoguests')), true);
        $mform->setDefault('guest', 0);
        
        // If we are creating a course, its enrol method isn't yet chosen, BUT the site has a default enrol method which we can use here
        $enrol_object = $CFG;
        if (!empty($course)) {
            $enrol_object = $course;
        }
        if (method_exists(enrolment_factory::factory($enrol_object->enrol), 'print_entry') && $enrol_object->enrol != 'manual'){
            $costgroup=array();
            $currencies = get_list_of_currencies();
            $costgroup[]= &MoodleQuickForm::createElement('text','cost', '', 'maxlength="6" size="6"');
            $costgroup[]= &MoodleQuickForm::createElement('select', 'currency', '', $currencies);
            $mform->addGroup($costgroup, 'costgrp', get_string('cost'), '&nbsp;', false);
            //defining a rule for a form element within a group :
            $costgrprules=array();
            //set the message to null to tell Moodle to use a default message
            //available for most rules, fetched from language pack (err_{rulename}).
            $costgrprules['cost'][]=array(null, 'numeric', null, 'client');
            $mform->addGroupRule('costgrp',$costgrprules);
            $mform->setHelpButton('costgrp', array('cost', get_string('cost')), true);
            $mform->setDefault('cost', '');
            $mform->setDefault('currency', empty($CFG->enrol_currency) ? 'USD' : $CFG->enrol_currency);

        }

//--------------------------------------------------------------------------------
        $mform->addElement('header','', get_string('language'));

        $languages=array();
        $languages[''] = get_string('forceno');
        $languages += get_list_of_languages();
        $mform->addElement('select', 'lang', get_string('forcelanguage'), $languages);

//--------------------------------------------------------------------------------
        if (has_capability('moodle/site:config', $systemcontext) && ((!empty($course->requested) && $CFG->restrictmodulesfor == 'requested') || $CFG->restrictmodulesfor == 'all')) {
            $mform->addElement('header', '', get_string('restrictmodules'));

            $options = array();
            $options['0'] = get_string('no');
            $options['1'] = get_string('yes');
            $mform->addElement('select', 'restrictmodules', get_string('restrictmodules'), $options);
            $mods = array(0=>get_string('allownone'));
            $mods += get_records_menu('modules', '','','','id, name');


            $mform->addElement('select', 'allowedmods', get_string('to'), $mods,
                            array('multiple'=>'multiple', 'size'=>'10'));
            $mform->disabledIf('allowedmods', 'restrictmodules', 'eq', 0);
        } else {
            $mform->addElement('hidden', 'restrictmodules', null);
        }
        if ($CFG->restrictmodulesfor == 'all') {
            $mform->setDefault('allowedmods', explode(',',$CFG->defaultallowedmodules));
            if (!empty($CFG->restrictbydefault)) {
                $mform->setDefault('restrictmodules', 1);
            }
        }
        $mform->setType('restrictmodules', PARAM_INT);

/// customizable role names in this course
//--------------------------------------------------------------------------------
        $mform->addElement('header','', get_string('roles'));

        if ($roles = get_records('role')) {
            foreach ($roles as $role) {
                $mform->addElement('text', 'role_'.$role->id, $role->name);
                if ($coursecontext) {
                    if ($rolename = get_record('role_names', 'roleid', $role->id, 'contextid', $coursecontext->id)) {
                        $mform->setDefault('role_'.$role->id, $rolename->text); 
                    }  
                }
            }
        }

//--------------------------------------------------------------------------------
        $this->add_action_buttons();
//--------------------------------------------------------------------------------
        $mform->addElement('hidden', 'id', null);
        $mform->setType('id', PARAM_INT);

        // fill in default teacher and student names to keep backwards compatibility for a while
        $mform->addElement('hidden', 'teacher', get_string('defaultcourseteacher'));
        $mform->addElement('hidden', 'teachers', get_string('defaultcourseteachers'));
        $mform->addElement('hidden', 'student', get_string('defaultcoursestudent'));
        $mform->addElement('hidden', 'students', get_string('defaultcoursestudents'));
    }


/// perform some extra moodle validation
    function validation($data){
        $errors= array();
        if ($foundcourses = get_records('course', 'shortname', $data['shortname'])) {
            if (!empty($data['id'])) {
                unset($foundcourses[$data['id']]);
            }
            if (!empty($foundcourses)) {
                foreach ($foundcourses as $foundcourse) {
                    $foundcoursenames[] = $foundcourse->fullname;
                }
                $foundcoursenamestring = implode(',', $foundcoursenames);
                $errors['shortname']= get_string('shortnametaken', '', $foundcoursenamestring);
            }
        }

        if (empty($data['enrolenddisabled'])){
            if ($data['enrolenddate'] <= $data['enrolstartdate']){
                $errors['enroldateendgrp'] = get_string('enrolenddaterror');
            }
        }

        if (0 == count($errors)){
            return true;
        } else {
            return $errors;
        }
    }
}
?>
