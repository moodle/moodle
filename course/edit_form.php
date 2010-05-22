<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/formslib.php');

class course_edit_form extends moodleform {

    function definition() {
        global $USER, $CFG, $DB;

        $courseconfig = get_config('moodlecourse');
        $mform    =& $this->_form;

        $course   = $this->_customdata['course'];
        $category = $this->_customdata['category'];
        $editoroptions = $this->_customdata['editoroptions'];

        $systemcontext = get_context_instance(CONTEXT_SYSTEM);
        $categorycontext = get_context_instance(CONTEXT_COURSECAT, $category->id);

        $disable_meta = false; // basic meta course state protection; server-side security checks not needed
        if (!empty($course->id)) {
            $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
            $context = $coursecontext;

            if (course_in_meta($course)) {
                $disable_meta = get_string('metaalreadyinmeta');

            } else if ($course->metacourse) {
                if ($DB->count_records('course_meta', array('parent_course'=>$course->id)) > 0) {
                    $disable_meta = get_string('metaalreadyhascourses');
                }

            } else {
                // if users already enrolled directly into coures, do not allow switching to meta,
                // users with metacourse manage permission are exception
                // please note that we do not need exact results - anything unexpected here prevents metacourse
                $managers = get_users_by_capability($coursecontext, 'moodle/course:managemetacourse', 'u.id');
                $enrolroles = get_roles_with_capability('moodle/course:participate', CAP_ALLOW, $coursecontext);
                if ($users = get_role_users(array_keys($enrolroles), $coursecontext, false, 'u.id', 'u.id ASC')) {
                    foreach($users as $user) {
                        if (!isset($managers[$user->id])) {
                            $disable_meta = get_string('metaalreadyhasenrolments');
                            break;
                        }
                    }
                }
                unset($managers);
                unset($users);
                unset($enrolroles);
            }
        } else {
            $coursecontext = null;
            $context = $categorycontext;
        }

/// form definition with new course defaults
//--------------------------------------------------------------------------------
        $mform->addElement('header','general', get_string('general', 'form'));

        // Must have create course capability in both categories in order to move course
        if (has_capability('moodle/course:create', $categorycontext)) {
            $displaylist = array();
            $parentlist = array();
            make_categories_list($displaylist, $parentlist, 'moodle/course:create');
            $mform->addElement('select', 'category', get_string('category'), $displaylist);
        } else {
            $mform->addElement('hidden', 'category', null);
            $mform->setType('category', PARAM_INT);
        }
        $mform->addHelpButton('category', 'category');
        $mform->setDefault('category', $category->id);
        $mform->setType('category', PARAM_INT);

        if (!empty($course->id) and !has_capability('moodle/course:changecategory', $coursecontext)) {
            $mform->hardFreeze('category');
            $mform->setConstant('category', $category->id);
        }

        $mform->addElement('text','fullname', get_string('fullnamecourse'),'maxlength="254" size="50"');
        $mform->addHelpButton('fullname', 'fullnamecourse');
        $mform->addRule('fullname', get_string('missingfullname'), 'required', null, 'client');
        $mform->setType('fullname', PARAM_MULTILANG);
        if (!empty($course->id) and !has_capability('moodle/course:changefullname', $coursecontext)) {
            $mform->hardFreeze('fullname');
            $mform->setConstant('fullname', $course->fullname);
        }

        $mform->addElement('text','shortname', get_string('shortnamecourse'),'maxlength="100" size="20"');
        $mform->addHelpButton('shortname', 'shortnamecourse');
        $mform->addRule('shortname', get_string('missingshortname'), 'required', null, 'client');
        $mform->setType('shortname', PARAM_MULTILANG);
        if (!empty($course->id) and !has_capability('moodle/course:changeshortname', $coursecontext)) {
            $mform->hardFreeze('shortname');
            $mform->setConstant('shortname', $course->shortname);
        }

        $mform->addElement('text','idnumber', get_string('idnumbercourse'),'maxlength="100"  size="10"');
        $mform->addHelpButton('idnumber', 'idnumbercourse');
        $mform->setType('idnumber', PARAM_RAW);
        if (!empty($course->id) and !has_capability('moodle/course:changeidnumber', $coursecontext)) {
            $mform->hardFreeze('idnumber');
            $mform->setConstants('idnumber', $course->idnumber);
        }


        $mform->addElement('editor','summary_editor', get_string('coursesummary'), null, $editoroptions);
        $mform->addHelpButton('summary_editor', 'coursesummary');
        $mform->setType('summary_editor', PARAM_RAW);

        if (!empty($course->id) and !has_capability('moodle/course:changesummary', $coursecontext)) {
            $mform->hardFreeze('summary_editor');
        }

        $courseformats = get_plugin_list('format');
        $formcourseformats = array();
        foreach ($courseformats as $courseformat => $formatdir) {
            $formcourseformats[$courseformat] = get_string('pluginname', "format_$courseformat");
        }
        $mform->addElement('select', 'format', get_string('format'), $formcourseformats);
        $mform->addHelpButton('format', 'format');
        $mform->setDefault('format', $courseconfig->format);

        for ($i=1; $i<=52; $i++) {
          $sectionmenu[$i] = "$i";
        }
        $mform->addElement('select', 'numsections', get_string('numberweeks'), $sectionmenu);
        $mform->setDefault('numsections', $courseconfig->numsections);

        $mform->addElement('date_selector', 'startdate', get_string('startdate'));
        $mform->addHelpButton('startdate', 'startdate');
        $mform->setDefault('startdate', time() + 3600 * 24);

        $choices = array();
        $choices['0'] = get_string('hiddensectionscollapsed');
        $choices['1'] = get_string('hiddensectionsinvisible');
        $mform->addElement('select', 'hiddensections', get_string('hiddensections'), $choices);
        $mform->addHelpButton('hiddensections', 'hiddensections');
        $mform->setDefault('hiddensections', $courseconfig->hiddensections);

        $options = range(0, 10);
        $mform->addElement('select', 'newsitems', get_string('newsitemsnumber'), $options);
        $mform->addHelpButton('newsitems', 'newsitemsnumber');
        $mform->setDefault('newsitems', $courseconfig->newsitems);

        $mform->addElement('selectyesno', 'showgrades', get_string('showgrades'));
        $mform->addHelpButton('showgrades', 'showgrades');
        $mform->setDefault('showgrades', $courseconfig->showgrades);

        $mform->addElement('selectyesno', 'showreports', get_string('showreports'));
        $mform->addHelpButton('showreports', 'showreports');
        $mform->setDefault('showreports', $courseconfig->showreports);

        $choices = get_max_upload_sizes($CFG->maxbytes);
        $mform->addElement('select', 'maxbytes', get_string('maximumupload'), $choices);
        $mform->addHelpButton('maxbytes', 'maximumupload');
        $mform->setDefault('maxbytes', $courseconfig->maxbytes);

        if (!empty($course->legacyfiles)) {
            $choices = array('1'=>get_string('no'), '2'=>get_string('yes'));
            $mform->addElement('select', 'legacyfiles', get_string('courselegacyfiles'), $choices);
            $mform->addHelpButton('legacyfiles', 'courselegacyfiles');
        }

        if (!empty($CFG->allowcoursethemes)) {
            $themeobjects = get_list_of_themes();
            $themes=array();
            $themes[''] = get_string('forceno');
            foreach ($themeobjects as $key=>$theme) {
                $themes[$key] = $theme->name;
            }
            $mform->addElement('select', 'theme', get_string('forcetheme'), $themes);
        }

        $meta=array();
        $meta[0] = get_string('no');
        $meta[1] = get_string('yes');
        if ($disable_meta === false) {
            $mform->addElement('select', 'metacourse', get_string('managemeta'), $meta);
            $mform->addHelpButton('metacourse', 'managemeta');
            $mform->setDefault('metacourse', $courseconfig->metacourse);
        } else {
            // no metacourse element - we do not want to change it anyway!
            $mform->addElement('static', 'nometacourse', get_string('managemeta'),
                ((empty($course->metacourse)) ? $meta[0] : $meta[1]) . " - $disable_meta ");
            $mform->addHelpButton('nometacourse', 'managemeta');
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
        $mform->addHelpButton('enrol', 'enrolmentplugins');
        $mform->setDefault('enrol', $courseconfig->enrol);


        $roles = get_assignable_roles($context);
        if (!empty($course->id)) {
            // add current default role, so that it is selectable even when user can not assign it
            if ($current_role = $DB->get_record('role', array('id'=>$course->defaultrole))) {
                $roles[$current_role->id] = strip_tags(format_string($current_role->name, true));
            }
        }
        $choices = array();
        if ($sitedefaultrole = $DB->get_record('role', array('id'=>$CFG->defaultcourseroleid))) {
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
        $mform->addHelpButton('enrollable', 'enrollable');
        $mform->setDefault('enrollable', $courseconfig->enrollable);

        $mform->addElement('date_selector', 'enrolstartdate', get_string('enrolstartdate'), array('optional' => true));
        $mform->setDefault('enrolstartdate', 0);
        $mform->disabledIf('enrolstartdate', 'enrollable', 'neq', 2);

        $mform->addElement('date_selector', 'enrolenddate', get_string('enrolenddate'), array('optional' => true));
        $mform->setDefault('enrolenddate', 0);
        $mform->disabledIf('enrolenddate', 'enrollable', 'neq', 2);

        $mform->addElement('duration', 'enrolperiod', get_string('enrolperiod'), array('optional' => true, 'defaultunit' => 86400));
        $mform->setDefault('enrolperiod', $courseconfig->enrolperiod);


//--------------------------------------------------------------------------------
        $mform->addElement('header','expirynotifyhdr', get_string('expirynotify'));

        $choices = array();
        $choices['0'] = get_string('no');
        $choices['1'] = get_string('yes');
        $mform->addElement('select', 'expirynotify', get_string('notify'), $choices);
        $mform->addHelpButton('expirynotify', 'notify');
        $mform->setDefault('expirynotify', $courseconfig->expirynotify);

        $mform->addElement('select', 'notifystudents', get_string('expirynotifystudents'), $choices);
        $mform->addHelpButton('notifystudents', 'expirynotifystudents');
        $mform->setDefault('notifystudents', $courseconfig->notifystudents);

        $thresholdmenu=array();
        for ($i=1; $i<=30; $i++) {
            $seconds = $i * 86400;
            $thresholdmenu[$seconds] = get_string('numdays', '', $i);
        }
        $mform->addElement('select', 'expirythreshold', get_string('expirythreshold'), $thresholdmenu);
        $mform->addHelpButton('expirythreshold', 'expirythreshold');
        $mform->setDefault('expirythreshold', $courseconfig->expirythreshold);

//--------------------------------------------------------------------------------
        $mform->addElement('header','', get_string('groups', 'group'));

        $choices = array();
        $choices[NOGROUPS] = get_string('groupsnone', 'group');
        $choices[SEPARATEGROUPS] = get_string('groupsseparate', 'group');
        $choices[VISIBLEGROUPS] = get_string('groupsvisible', 'group');
        $mform->addElement('select', 'groupmode', get_string('groupmode', 'group'), $choices);
        $mform->addHelpButton('groupmode', 'groupmode', 'group');
        $mform->setDefault('groupmode', $courseconfig->groupmode);

        $choices = array();
        $choices['0'] = get_string('no');
        $choices['1'] = get_string('yes');
        $mform->addElement('select', 'groupmodeforce', get_string('groupmodeforce', 'group'), $choices);
        $mform->addHelpButton('groupmodeforce', 'groupmodeforce', 'group');
        $mform->setDefault('groupmodeforce', $courseconfig->groupmodeforce);

        //default groupings selector
        $options = array();
        $options[0] = get_string('none');
        $mform->addElement('select', 'defaultgroupingid', get_string('defaultgrouping', 'group'), $options);

//--------------------------------------------------------------------------------
        $mform->addElement('header','', get_string('availability'));

        $choices = array();
        $choices['0'] = get_string('courseavailablenot');
        $choices['1'] = get_string('courseavailable');
        $mform->addElement('select', 'visible', get_string('availability'), $choices);
        $mform->addHelpButton('visible', 'availability');
        $mform->setDefault('visible', $courseconfig->visible);
        if (!empty($course->id) and !has_capability('moodle/course:visibility', $coursecontext)) {
            $mform->hardFreeze('visible');
            $mform->setConstant('visible', $course->visible);
        }

        $mform->addElement('passwordunmask', 'enrolpassword', get_string('enrolmentkey'), 'size="25"');
        $mform->setDefault('enrolpassword', '');
        $mform->setDefault('enrolpassword', $courseconfig->enrolpassword);
        $mform->setType('enrolpassword', PARAM_RAW);

        if (empty($course->id) or ($course->password !== '' and $course->id != SITEID)) {
            // do not require password in existing courses that do not have password yet - backwards compatibility ;-)
            if (!empty($CFG->enrol_manual_requirekey)) {
                $mform->addRule('enrolpassword', get_string('required'), 'required', null, 'client');
            }
        }

        $choices = array();
        $choices['0'] = get_string('guestsno');
        $choices['1'] = get_string('guestsyes');
        $choices['2'] = get_string('guestskey');
        $mform->addElement('select', 'guest', get_string('opentoguests'), $choices);
        $mform->setDefault('guest', $courseconfig->guest);

        // If we are creating a course, its enrol method isn't yet chosen, BUT the site has a default enrol method which we can use here
        $enrol_object = $CFG;
        if (!empty($course->id)) {
            $enrol_object = $course;
        }
        // If the print_entry method exists and the course enrol method isn't manual (both set or inherited from site), show cost
        if (method_exists(enrolment_factory::factory($enrol_object->enrol), 'print_entry') && !($enrol_object->enrol == 'manual' || (empty($enrol_object->enrol) && $CFG->enrol == 'manual'))) {
            $costgroup=array();
            $currencies = get_string_manager()->get_list_of_currencies();
            $costgroup[]= &MoodleQuickForm::createElement('text','cost', '', 'maxlength="6" size="6"');
            $costgroup[]= &MoodleQuickForm::createElement('select', 'currency', '', $currencies);
            $mform->addGroup($costgroup, 'costgrp', get_string('cost'), '&nbsp;', false);
            //defining a rule for a form element within a group :
            $costgrprules=array();
            //set the message to null to tell Moodle to use a default message
            //available for most rules, fetched from language pack (err_{rulename}).
            $costgrprules['cost'][]=array(null, 'numeric', null, 'client');
            $mform->addGroupRule('costgrp',$costgrprules);
            $mform->setDefault('cost', '');
            $mform->setDefault('currency', empty($CFG->enrol_currency) ? 'USD' : $CFG->enrol_currency);

        }

//--------------------------------------------------------------------------------
        $mform->addElement('header','', get_string('language'));

        $languages=array();
        $languages[''] = get_string('forceno');
        $languages += get_string_manager()->get_list_of_translations();
        $mform->addElement('select', 'lang', get_string('forcelanguage'), $languages);
        $mform->setDefault('lang', $courseconfig->lang);

//--------------------------------------------------------------------------------
        require_once($CFG->libdir.'/completionlib.php');
        if(completion_info::is_enabled_for_site()) {
            $mform->addElement('header','', get_string('progress','completion'));
            $mform->addElement('select', 'enablecompletion', get_string('completion','completion'),
                array(0=>get_string('completiondisabled','completion'), 1=>get_string('completionenabled','completion')));
            $mform->setDefault('enablecompletion', $courseconfig->enablecompletion);

            $mform->addElement('checkbox', 'completionstartonenrol', get_string('completionstartonenrol', 'completion'));
            $mform->setDefault('completionstartonenrol', $courseconfig->completionstartonenrol);
            $mform->disabledIf('completionstartonenrol', 'enablecompletion', 'eq', 0);
        } else {
            $mform->addElement('hidden', 'enablecompletion');
            $mform->setType('enablecompletion', PARAM_INT);
            $mform->setDefault('enablecompletion',0);

            $mform->addElement('hidden', 'completionstartonenrol');
            $mform->setType('completionstartonenrol', PARAM_INT);
            $mform->setDefault('completionstartonenrol',0);
        }

//--------------------------------------------------------------------------------
        if (has_capability('moodle/site:config', $systemcontext) && ((!empty($course->requested) && $CFG->restrictmodulesfor == 'requested') || $CFG->restrictmodulesfor == 'all')) {
            $mform->addElement('header', '', get_string('restrictmodules'));

            $options = array();
            $options['0'] = get_string('no');
            $options['1'] = get_string('yes');
            $mform->addElement('select', 'restrictmodules', get_string('restrictmodules'), $options);
            $mods = array(0=>get_string('allownone'));
            $mods += $DB->get_records_menu('modules', array(), 'name', 'id, name');


            $mform->addElement('select', 'allowedmods', get_string('to'), $mods,
                            array('multiple'=>'multiple', 'size'=>'10'));
            $mform->disabledIf('allowedmods', 'restrictmodules', 'eq', 0);
        } else {
            $mform->addElement('hidden', 'restrictmodules', null);
            $mform->setType('restrictmodules', PARAM_INT);
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
        $mform->addElement('header','rolerenaming', get_string('rolerenaming'));
        $mform->addHelpButton('rolerenaming', 'rolerenaming');

        if ($roles = get_all_roles()) {
            if ($coursecontext) {
                $roles = role_fix_names($roles, $coursecontext, ROLENAME_ALIAS_RAW);
            }
            $assignableroles = get_roles_for_contextlevels(CONTEXT_COURSE);
            foreach ($roles as $role) {
                $mform->addElement('text', 'role_'.$role->id, get_string('yourwordforx', '', $role->name));
                if (isset($role->localname)) {
                    $mform->setDefault('role_'.$role->id, $role->localname);
                }
                $mform->setType('role_'.$role->id, PARAM_TEXT);
                if (!in_array($role->id, $assignableroles)) {
                    $mform->setAdvanced('role_'.$role->id);
                }
            }
        }

//--------------------------------------------------------------------------------
        $this->add_action_buttons();
//--------------------------------------------------------------------------------
        $mform->addElement('hidden', 'id', null);
        $mform->setType('id', PARAM_INT);
    }

    function definition_after_data() {
        global $DB;

        $mform =& $this->_form;

        // add availabe groupings
        if ($courseid = $mform->getElementValue('id') and $mform->elementExists('defaultgroupingid')) {
            $options = array();
            if ($groupings = $DB->get_records('groupings', array('courseid'=>$courseid))) {
                foreach ($groupings as $grouping) {
                    $options[$grouping->id] = format_string($grouping->name);
                }
            }
            $gr_el =& $mform->getElement('defaultgroupingid');
            $gr_el->load($options);
        }
    }


/// perform some extra moodle validation
    function validation($data, $files) {
        global $DB, $CFG;

        $errors = parent::validation($data, $files);
        if ($foundcourses = $DB->get_records('course', array('shortname'=>$data['shortname']))) {
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

        if (!empty($data['enrolstartdate']) && !empty($data['enrolenddate']) &&
                $data['enrolenddate'] <= $data['enrolstartdate']){
            $errors['enrolenddate'] = get_string('enrolenddaterror');
        }

        if (!empty($CFG->enrol_manual_usepasswordpolicy) and isset($data['enrolpassword']) and $data['enrolpassword'] != '') {
            $course = $this->_customdata['course'];
            if ($course->password !== $data['enrolpassword']) {
                // enforce password policy only if changing password - backwards compatibility
                $errmsg = '';
                if (!check_password_policy($data['enrolpassword'], $errmsg)) {
                    $errors['enrolpassword'] = $errmsg;
                }
            }
        }

        return $errors;
    }
}

