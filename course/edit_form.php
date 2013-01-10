<?php

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->libdir.'/completionlib.php');

class course_edit_form extends moodleform {
    protected $course;
    protected $context;

    function definition() {
        global $USER, $CFG, $DB, $PAGE;

        $mform    = $this->_form;
        $PAGE->requires->yui_module('moodle-course-formatchooser', 'M.course.init_formatchooser',
                array(array('formid' => $mform->getAttribute('id'))));

        $course        = $this->_customdata['course']; // this contains the data of this form
        $category      = $this->_customdata['category'];
        $editoroptions = $this->_customdata['editoroptions'];
        $returnto = $this->_customdata['returnto'];

        $systemcontext   = context_system::instance();
        $categorycontext = context_coursecat::instance($category->id);

        if (!empty($course->id)) {
            $coursecontext = context_course::instance($course->id);
            $context = $coursecontext;
        } else {
            $coursecontext = null;
            $context = $categorycontext;
        }

        $courseconfig = get_config('moodlecourse');

        $this->course  = $course;
        $this->context = $context;

/// form definition with new course defaults
//--------------------------------------------------------------------------------
        $mform->addElement('header','general', get_string('general', 'form'));

        $mform->addElement('hidden', 'returnto', null);
        $mform->setType('returnto', PARAM_ALPHANUM);
        $mform->setConstant('returnto', $returnto);

        // verify permissions to change course category or keep current
        if (empty($course->id)) {
            if (has_capability('moodle/course:create', $categorycontext)) {
                $displaylist = array();
                $parentlist = array();
                make_categories_list($displaylist, $parentlist, 'moodle/course:create');
                $mform->addElement('select', 'category', get_string('category'), $displaylist);
                $mform->addHelpButton('category', 'category');
                $mform->setDefault('category', $category->id);
            } else {
                $mform->addElement('hidden', 'category', null);
                $mform->setType('category', PARAM_INT);
                $mform->setConstant('category', $category->id);
            }
        } else {
            if (has_capability('moodle/course:changecategory', $coursecontext)) {
                $displaylist = array();
                $parentlist = array();
                make_categories_list($displaylist, $parentlist, 'moodle/course:create');
                if (!isset($displaylist[$course->category])) {
                    //always keep current
                    $displaylist[$course->category] = format_string($DB->get_field('course_categories', 'name', array('id'=>$course->category)));
                }
                $mform->addElement('select', 'category', get_string('category'), $displaylist);
                $mform->addHelpButton('category', 'category');
            } else {
                //keep current
                $mform->addElement('hidden', 'category', null);
                $mform->setType('category', PARAM_INT);
                $mform->setConstant('category', $course->category);
            }
        }

        $mform->addElement('text','fullname', get_string('fullnamecourse'),'maxlength="254" size="50"');
        $mform->addHelpButton('fullname', 'fullnamecourse');
        $mform->addRule('fullname', get_string('missingfullname'), 'required', null, 'client');
        $mform->setType('fullname', PARAM_TEXT);
        if (!empty($course->id) and !has_capability('moodle/course:changefullname', $coursecontext)) {
            $mform->hardFreeze('fullname');
            $mform->setConstant('fullname', $course->fullname);
        }

        $mform->addElement('text', 'shortname', get_string('shortnamecourse'), 'maxlength="100" size="20"');
        $mform->addHelpButton('shortname', 'shortnamecourse');
        $mform->addRule('shortname', get_string('missingshortname'), 'required', null, 'client');
        $mform->setType('shortname', PARAM_TEXT);
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

        $courseformats = get_sorted_course_formats(true);
        $formcourseformats = array();
        foreach ($courseformats as $courseformat) {
            $formcourseformats[$courseformat] = get_string('pluginname', "format_$courseformat");
        }
        if (isset($course->format)) {
            $course->format = course_get_format($course)->get_format(); // replace with default if not found
            if (!in_array($course->format, $courseformats)) {
                // this format is disabled. Still display it in the dropdown
                $formcourseformats[$course->format] = get_string('withdisablednote', 'moodle',
                        get_string('pluginname', 'format_'.$course->format));
            }
        }

        $mform->addElement('select', 'format', get_string('format'), $formcourseformats);
        $mform->addHelpButton('format', 'format');
        $mform->setDefault('format', $courseconfig->format);

        // button to update format-specific options on format change (will be hidden by JavaScript)
        $mform->registerNoSubmitButton('updatecourseformat');
        $mform->addElement('submit', 'updatecourseformat', get_string('courseformatudpate'));

        $mform->addElement('date_selector', 'startdate', get_string('startdate'));
        $mform->addHelpButton('startdate', 'startdate');
        $mform->setDefault('startdate', time() + 3600 * 24);

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

        // Handle non-existing $course->maxbytes on course creation.
        $coursemaxbytes = !isset($course->maxbytes) ? null : $course->maxbytes;

        // Let's prepare the maxbytes popup.
        $choices = get_max_upload_sizes($CFG->maxbytes, 0, 0, $coursemaxbytes);
        $mform->addElement('select', 'maxbytes', get_string('maximumupload'), $choices);
        $mform->addHelpButton('maxbytes', 'maximumupload');
        $mform->setDefault('maxbytes', $courseconfig->maxbytes);

        if (!empty($course->legacyfiles) or !empty($CFG->legacyfilesinnewcourses)) {
            if (empty($course->legacyfiles)) {
                //0 or missing means no legacy files ever used in this course - new course or nobody turned on legacy files yet
                $choices = array('0'=>get_string('no'), '2'=>get_string('yes'));
            } else {
                $choices = array('1'=>get_string('no'), '2'=>get_string('yes'));
            }
            $mform->addElement('select', 'legacyfiles', get_string('courselegacyfiles'), $choices);
            $mform->addHelpButton('legacyfiles', 'courselegacyfiles');
            if (!isset($courseconfig->legacyfiles)) {
                // in case this was not initialised properly due to switching of $CFG->legacyfilesinnewcourses
                $courseconfig->legacyfiles = 0;
            }
            $mform->setDefault('legacyfiles', $courseconfig->legacyfiles);
        }

        if (!empty($CFG->allowcoursethemes)) {
            $themeobjects = get_list_of_themes();
            $themes=array();
            $themes[''] = get_string('forceno');
            foreach ($themeobjects as $key=>$theme) {
                if (empty($theme->hidefromselector)) {
                    $themes[$key] = get_string('pluginname', 'theme_'.$theme->name);
                }
            }
            $mform->addElement('select', 'theme', get_string('forcetheme'), $themes);
        }

//--------------------------------------------------------------------------------
        $mform->addElement('hidden', 'addcourseformatoptionshere');

//--------------------------------------------------------------------------------
        enrol_course_edit_form($mform, $course, $context);

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
        if (!has_capability('moodle/course:visibility', $context)) {
            $mform->hardFreeze('visible');
            if (!empty($course->id)) {
                $mform->setConstant('visible', $course->visible);
            } else {
                $mform->setConstant('visible', $courseconfig->visible);
            }
        }

//--------------------------------------------------------------------------------
        $mform->addElement('header','', get_string('language'));

        $languages=array();
        $languages[''] = get_string('forceno');
        $languages += get_string_manager()->get_list_of_translations();
        $mform->addElement('select', 'lang', get_string('forcelanguage'), $languages);
        $mform->setDefault('lang', $courseconfig->lang);

//--------------------------------------------------------------------------------
        if (completion_info::is_enabled_for_site()) {
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

/// customizable role names in this course
//--------------------------------------------------------------------------------
        $mform->addElement('header','rolerenaming', get_string('rolerenaming'));
        $mform->addHelpButton('rolerenaming', 'rolerenaming');

        if ($roles = get_all_roles()) {
            $roles = role_fix_names($roles, null, ROLENAME_ORIGINAL);
            $assignableroles = get_roles_for_contextlevels(CONTEXT_COURSE);
            foreach ($roles as $role) {
                $mform->addElement('text', 'role_'.$role->id, get_string('yourwordforx', '', $role->localname));
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

/// finally set the current form data
//--------------------------------------------------------------------------------
        $this->set_data($course);
    }

    function definition_after_data() {
        global $DB;

        $mform = $this->_form;

        // add available groupings
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

        // add course format options
        $formatvalue = $mform->getElementValue('format');
        if (is_array($formatvalue) && !empty($formatvalue)) {
            $courseformat = course_get_format((object)array('format' => $formatvalue[0]));
            $newel = $mform->createElement('header', '', get_string('courseformatoptions', 'moodle',
                    $courseformat->get_format_name()));
            $mform->insertElementBefore($newel, 'addcourseformatoptionshere');

            $elements = $courseformat->create_edit_form_elements($mform);
            for ($i = 0; $i < count($elements); $i++) {
                $mform->insertElementBefore($mform->removeElement($elements[$i]->getName(), false),
                        'addcourseformatoptionshere');
            }
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

        $errors = array_merge($errors, enrol_course_edit_validation($data, $this->context));

        $courseformat = course_get_format((object)array('format' => $data['format']));
        $formaterrors = $courseformat->edit_form_validation($data, $files, $errors);
        if (!empty($formaterrors) && is_array($formaterrors)) {
            $errors = array_merge($errors, $formaterrors);
        }

        return $errors;
    }
}

