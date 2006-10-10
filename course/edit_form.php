<?php
/// For moodleform we reconstruct all the data about the form after submission before we
/// extract data submitted. So we can then tell for select, checkbox and radio fields what
/// options could have been submitted by the form eg submitted value for a single selection
/// select field must be one of the options of the select field.

// $toform will be used for the default value of a field if no data has been submitted.

    if (!empty($course)) {
        //this is what we do if we are editing a record from the db
        $toform = $course;
        $toform->allowedmods = array();
        if ($am = get_records("course_allowed_modules","course",$course->id)) {
            foreach ($am as $m) {
                $toform->allowedmods[] = $m->module;
            }
        } else {
            if (empty($course->restrictmodules)) {
                $toform->allowedmods = explode(',',$CFG->defaultallowedmodules);
            } // it'll be greyed out but we want these by default anyway.
        }        
    } else {
        //this is a brand new course!
        $toform->startdate = time() + 3600 * 24;
        $toform->fullname = get_string("defaultcoursefullname");
        $toform->shortname = get_string("defaultcourseshortname");
        $toform->summary = get_string("defaultcoursesummary");
        $toform->format = "weeks";
        $toform->password = "";
        $toform->guest = 0;
        $toform->numsections = 10;
        $toform->idnumber = '';
        $toform->cost = '';
        $toform->currency = empty($CFG->enrol_currency) ? 'USD' : $CFG->enrol_currency;
        $toform->newsitems = 5;
        $toform->showgrades = 1;
        $toform->groupmode = 0;
        $toform->groupmodeforce = 0;
        $toform->category = $category;
        $toform->id = "";
        $toform->visible = 1;
        $toform->allowedmods = array();
        if ($CFG->restrictmodulesfor == 'all') {
            $toform->allowedmods = explode(',',$CFG->defaultallowedmodules);
            if (!empty($CFG->restrictbydefault)) {
                $toform->restrictmodules = 1;
            }
        }


    }

    $mform->addElement('header','general', get_string("general"));
    if (has_capability('moodle/course:create', get_context_instance(CONTEXT_COURSECAT, $toform->category))) { 
        $displaylist = array();
        $parentlist = array();
        make_categories_list($displaylist, $parentlist);
        $mform->addElement('select', "category", get_string("category"), $displaylist );
    }
    $mform->setDefault('fullname', $toform->fullname);
    $mform->addElement('text','fullname', get_string("fullname"),'maxlength="254" size="50"');
    $mform->setDefault('shortname', $toform->shortname);
    $mform->addElement('text','shortname', get_string("shortname"),'maxlength="15" size="10"');
    $mform->setDefault('idnumber', $toform->idnumber);
    $mform->addElement('text','idnumber', get_string("idnumbercourse"),'maxlength="100"  size="10"');
    $mform->setDefault('summary', $toform->summary);
    $mform->addElement('htmleditor','summary', get_string("summary"), array('rows'=>'10', 'cols'=>'65'));

    $mform->setDefault('format', $toform->format);
    $courseformats = get_list_of_plugins("course/format");
    $formcourseformats = array();

    foreach ($courseformats as $courseformat) {
        $formcourseformats["$courseformat"] = get_string("format$courseformat");
    }

    $mform->addElement('select', 'format', get_string("format"), $formcourseformats );

    $mform->setDefault('numsections', $toform->numsections);
    for ($i=1; $i<=52; $i++) {
      $sectionmenu[$i] = "$i";
    } 
    $mform->addElement('select', 'numsections', get_string("numberweeks"), $sectionmenu);
    $mform->setDefault('startdate', $toform->startdate);
    $mform->addElement('date_selector', 'startdate', get_string('startdate'));

    $mform->setDefault('hiddensections',(isset($toform->hiddensections))?$toform->hiddensections:0);
    unset($choices);
    $choices["0"] = get_string("hiddensectionscollapsed");
    $choices["1"] = get_string("hiddensectionsinvisible");
    $mform->addElement('select', 'hiddensections', get_string("hiddensections"), $choices);

    $mform->setDefault('newsitems', $toform->newsitems);
    $newsitem = get_string("newsitem");
    $newsitems = get_string("newsitems");
    $options = array("0" => "0 $newsitems",
                    "1" => "1 $newsitem",
                    "2" => "2 $newsitems",
                    "3" => "3 $newsitems",
                    "4" => "4 $newsitems",
                    "5" => "5 $newsitems",
                    "6" => "6 $newsitems",
                    "7" => "7 $newsitems",
                    "8" => "8 $newsitems",
                    "9" => "9 $newsitems",
                    "10" => "10 $newsitems");
    $mform->addElement('select', 'newsitems', get_string("newsitemsnumber"), $options);

    $mform->setDefault('showgrades', $toform->showgrades);
    unset($choices);
    $choices["0"] = get_string("no");
    $choices["1"] = get_string("yes");
    $mform->addElement('select', 'showgrades', get_string("showgrades"), $choices);
    unset($choices);
    $mform->setDefault('showreports',(isset($toform->showreports))?$toform->showreports:0);
    $choices["0"] = get_string("no");
    $choices["1"] = get_string("yes");
    $mform->addElement('select', 'showreports', get_string("showreports"), $choices);
    $mform->setDefault('maxbytes',(isset($toform->maxbytes))?$toform->maxbytes:0);
    $choices = get_max_upload_sizes($CFG->maxbytes);
    $mform->addElement('select', 'maxbytes', get_string("maximumupload"), $choices);
    if (!empty($CFG->allowcoursethemes)) {
        $mform->setDefault('theme',(isset($toform->theme))?$toform->theme:'');
       
        $themes=array();
        $themes[''] = get_string("forceno");
        $themes += get_list_of_themes();
        $mform->addElement('select', 'theme', get_string("forcetheme"), $themes);
    }
    unset($choices);
    $mform->setDefault('metacourse',(isset($toform->metacourse))?$toform->metacourse:0);

    if (empty($disable_meta)) {
        $meta=array();
        $meta[0] = get_string('no');
        $meta[1] = get_string('yes');
        $mform->addElement('select', 'metacourse', get_string("managemeta"), $meta);
    }else{
        $mform->addElement('static', 'metacourse', get_string("managemeta"), 
            ((empty($toform->metacourse)) ? get_string("no") : get_string("yes"))
                . " - $disable_meta ");
        
    }
    
    $mform->setDefault('defaultrole',(isset($toform->defaultrole))?$toform->defaultrole:0);
    $roles = get_assignable_roles($context);
    asort($roles);
    $choices = array();

    if ($sitedefaultrole = get_record('role', 'id', $CFG->defaultcourseroleid)) {
        $choices[0] = get_string('sitedefault').' ('.$sitedefaultrole->name.')';
    } else {
        $choices[0] = get_string('sitedefault');
    }

    $choices = $choices + $roles;    
    $defaultroleelement=$mform->addElement('select', 'defaultrole', get_string('defaultrole', 'role'), $choices );
    if ($mform->exportValue('defaultrole') && !isset($roles[$mform->exportValue('defaultrole')])) {  // Existing setting is one we can't choose
        if ($coursedefaultrole = get_record('role', 'id', $mform->exportValue('defaultrole'))) {
            
            $defaultroleelement->addOption(get_string('currentrole', 'role').' ('.$coursedefaultrole->name.')',-1);
        } else {
            $defaultroleelement->addOption(get_string('currentrole', 'role'),-1);
        }
    }
    

    $mform->addElement('header','enrolhdr', get_string("enrolment"));
    $mform->setDefault('enrol',(isset($toform->enrol))?$toform->enrol:'');
       
    $modules = explode(',', $CFG->enrol_plugins_enabled);
    foreach ($modules as $module) {
        $name = get_string("enrolname", "enrol_$module");
        $plugin = enrolment_factory::factory($module);
        if (method_exists($plugin, 'print_entry')) {
            $choices[$name] = $module;
        }
    }
    asort($choices);
    $choices = array_flip($choices);
    $choices = array_merge(array('' => get_string('sitedefault').' ('.get_string("enrolname", "enrol_$CFG->enrol").')'), $choices);
    $mform->addElement('select', 'enrol', get_string("enrolmentplugins"), $choices );
    
    $mform->setDefault('enrollable',(isset($toform->enrollable))?$toform->enrollable:1);
    $radio = array();
    $radio[] = &moodleform::createElement('radio', 'enrollable', null, get_string("no"), 0);
    $radio[] = &moodleform::createElement('radio', 'enrollable', null, get_string("yes"), 1);
    $radio[] = &moodleform::createElement('radio', 'enrollable', null, get_string("enroldate"), 2);
    $mform->addGroup($radio, 'enrollable', get_string("enrollable"), ' ', false);
    
    $mform->setDefault('enrolstartdate',(isset($toform->enrolstartdate))?$toform->enrolstartdate:0);
    $mform->setDefault('enrolstartdisabled',
                            (!isset($toform->enrolstartdate)||
                                (isset($toform->enrolstartdate)&&!$toform->enrolstartdate)
                            ?1:0));
    $enroldatestartgrp = array();
    $enroldatestartgrp[] = &moodleform::createElement('date_selector', 'enrolstartdate');
    $enroldatestartgrp[] = &moodleform::createElement('checkbox', 'enrolstartdisabled', null, get_string('disable'));
    $mform->addGroup($enroldatestartgrp, '', get_string('enrolstartdate'), ' ', false);
    
    $mform->setDefault('enrolenddate',(isset($toform->enrolenddate))?$toform->enrolenddate:0);
    $mform->setDefault('enrolenddisabled',
                            (!isset($toform->enrolenddate)||
                                (isset($toform->enrolenddate)&&!$toform->enrolenddate)
                            ?1:0));
    $enroldateendgrp = array();
    $enroldateendgrp[] = &moodleform::createElement('date_selector', 'enrolenddate');
    $enroldateendgrp[] = &moodleform::createElement('checkbox', 'enrolenddisabled', null, get_string('disable'));
    $mform->addGroup($enroldateendgrp, 'enroldateendgrp', get_string('enrolenddate'), ' ', false);
    
    $mform->setDefault('enrolperiod',(isset($toform->enrolperiod))?$toform->enrolperiod:0);
    $periodmenu=array();
    $periodmenu[0] = get_string('unlimited');
    for ($i=1; $i<=365; $i++) {
        $seconds = $i * 86400;
        $periodmenu[$seconds] = get_string('numdays', '', $i);
    }
    $mform->addElement('select', 'enrolperiod', get_string("enrolperiod"), $periodmenu);
    
    
    $mform->addElement('header','expirynotifyhdr', get_string("expirynotify"));
    
    $mform->setDefault('expirynotify',(isset($toform->expirynotify))?$toform->expirynotify:0);
    unset($choices);
    $choices["0"] = get_string("no");
    $choices["1"] = get_string("yes");
    $mform->addElement('select', 'expirynotify', get_string("expirynotify"), $choices);
    $mform->setDefault('notifystudents',(isset($toform->notifystudents))?$toform->notifystudents:0);
    $mform->addElement('select', 'notifystudents', get_string("expirynotifystudents"), $choices);
    $mform->setDefault('expirythreshold',(isset($toform->expirythreshold))?$toform->expirythreshold:10 * 86400);
    $thresholdmenu=array();
    for ($i=1; $i<=30; $i++) {
        $seconds = $i * 86400;
        $thresholdmenu[$seconds] = get_string('numdays', '', $i);
    }
    $mform->addElement('select', 'expirythreshold', get_string("expirythreshold"), $thresholdmenu);
    
    
    $mform->addElement('header','', get_string("groupmode"));

    $mform->setDefault('groupmode', $toform->groupmode);
    unset($choices);
    $choices[NOGROUPS] = get_string("groupsnone");
    $choices[SEPARATEGROUPS] = get_string("groupsseparate");
    $choices[VISIBLEGROUPS] = get_string("groupsvisible");
    $mform->addElement('select', 'groupmode', get_string("groupmode"), $choices);
    $mform->setDefault('groupmodeforce', $toform->groupmodeforce);
    unset($choices);
    $choices["0"] = get_string("no");
    $choices["1"] = get_string("yes");
    $mform->addElement('select', 'groupmodeforce', get_string("force"), $choices);

    $mform->addElement('header','', get_string("availability"));

    $mform->setDefault('visible', $toform->visible);
    unset($choices);
    $choices["0"] = get_string("courseavailablenot");
    $choices["1"] = get_string("courseavailable");
    $mform->addElement('select', 'visible', get_string("availability"), $choices);
    $mform->setDefault('password', $toform->password);
    $mform->addElement('text', 'password', get_string("enrolmentkey"), 'size="25"');
    $mform->setDefault('guest', $toform->guest);
    unset($choices);
    $choices["0"] = get_string("guestsno");
    $choices["1"] = get_string("guestsyes");
    $choices["2"] = get_string("guestskey");
    $mform->addElement('select', 'guest', get_string("opentoguests"), $choices);
    if (isset($course) && method_exists(enrolment_factory::factory($course->enrol), 'print_entry') && $course->enrol != 'manual') {
        $mform->setDefault('cost', $toform->cost);
        $mform->setDefault('currency', $toform->currency);
        $costgroup=array();
        $currencies = get_list_of_currencies();
        $costgroup[]= &moodleform::createElement('text','cost', '', 'maxlength="6" size="6"');
        $costgroup[]= &moodleform::createElement('select', 'currency', '', $currencies);
        $mform->addGroup($costgroup, 'costgrp', get_string("cost"), '&nbsp;', false);
    }

    $mform->addElement('header','', get_string("language"));
    $mform->setDefault('lang',(isset($toform->lang))?$toform->lang:'');
    $languages=array();
    $languages[''] = get_string("forceno");
    $languages += get_list_of_languages();
    $mform->addElement('select', 'lang', get_string("forcelanguage"), $languages);

    if(!has_capability('moodle/course:create', get_context_instance(CONTEXT_COURSECAT, $toform->category))) { 
        $mform->addElement('hidden', 'category', null);
    }
    $mform->addElement('hidden', 'id', null);
   
    $mform->setDefault('restrictmodules',(isset($toform->restrictmodules))?$toform->restrictmodules:0);
    if (has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM, SITEID)) && ((!empty($course->requested) && $CFG->restrictmodulesfor == 'requested') || $CFG->restrictmodulesfor == 'all')) {         
        unset($options);
        $options[0] = get_string("no");
        $options[1] = get_string("yes");
        $mform->addElement('header', '', get_string("restrictmodules"));
        $mform->addElement('select', 'restrictmodules', get_string("restrictmodules"), $options,
                array('onChange'=>"document.getElementById('id_allowedmods').disabled=".
                            "((this.selectedIndex==0)?true:false);"));
        $mods = array(0=>get_string('allownone'));
        $mods += get_records_menu("modules", '','','','id, name');
        $disabled=($mform->exportValue('restrictmodules')==1) ? array() :array('disabled' => 'disabled') ;
        
        
        $mform->setDefault('allowedmods', $toform->allowedmods);
        $mform->addElement('select', 'allowedmods', get_string("to"),$mods,
                        array('multiple'=>"multiple", 'size'=>"10", 'id'=>"allowedmods")
                                +$disabled);
    }else {
        $mform->addElement('hidden', 'restrictmodules', $toform->restrictmodules);
    }
    $mform->setHelpButtons(array('category'=>array("coursecategory", get_string("category")),
                             'fullname'=>array("coursefullname", get_string("fullname")),
                             'shortname'=>array("courseshortname", get_string("shortname")),
                             'idnumber'=>array("courseidnumber", get_string("idnumbercourse")),
                             'summary'=>array("text", get_string("helptext")),
                             'format'=>array("courseformats", get_string("courseformats")),
                             'summary'=>array("coursestartdate", get_string("startdate")),
                             'enrol'=>array("courseenrolmentplugins", get_string("enrolmentplugins")),
                             'enrollable'=>array("courseenrollable", get_string("enrollable")),
                             'expirynotify'=>array("expirynotify", get_string("expirynotify")),
                             'notifystudents'=>array("expirynotifystudents", get_string("expirynotifystudents")),
                             'expirythreshold'=>array("expirythreshold", get_string("expirythreshold")),
                             'groupmode'=>array("groupmode", get_string("groupmode")),
                             'groupmodeforce'=>array("groupmodeforce", get_string("groupmodeforce")),
                             'visible'=>array("courseavailability", get_string("availability")),
                             'password'=>array("enrolmentkey", get_string("enrolmentkey")),
                             'guest'=>array("guestaccess", get_string("opentoguests")),
                             'costgrp'=>array("cost", get_string("cost")),
                             'hiddensections'=>array("coursehiddensections", get_string("hiddensections")),
                             'newsitems'=>array("coursenewsitems", get_string("newsitemsnumber")),
                             'showgrades'=>array("coursegrades", get_string("grades")),
                             'showreports'=>array("coursereports", get_string("activityreport")),
                             'maxbytes'=>array("courseuploadsize", get_string("maximumupload")),
                             'metacourse'=>array("metacourse", get_string("metacourse")),
                             'defaultrole'=>array("coursedefaultrole", get_string("defaultrole", 'role'))),
                                    true);
    $mform->addElement('submit', 'submit', get_string("savechanges"));
    
    $mform->addRule('fullname', get_string("missingfullname"), 'required', null, 'client');
    $mform->addRule('shortname', get_string("missingshortname"), 'required', null, 'client');
    $mform->addRule('summary', get_string("missingsummary"), 'required', null, 'client');
    $mform->addRule('category', get_string("missingcategory"), 'required', null, 'client');
    
    /**
     * a Form rule check - if we need to create rules which involve the contents of more than one 
     * field this is the way to do it. We can do it all at once in one function or create several
     * form rules.
     *
     * @param array $fields values from form without slashes
     * @return mixed array of errors or true if passed check
     */
    function form_check($fields){
        global $course;
        $errors= array();
        if ($foundcourses = get_records("course", "shortname", addslashes($fields['shortname']))) {
            if (!empty($course->id)) {
                unset($foundcourses[$course->id]);
            }
            if (!empty($foundcourses)) {
                foreach ($foundcourses as $foundcourse) {
                    $foundcoursenames[] = $foundcourse->fullname;
                }
                $foundcoursenamestring = addslashes(implode(',', $foundcoursenames));
        
                $errors['shortname']= get_string("shortnametaken", "", $foundcoursenamestring);
                
            }
            
            
        }
        if (empty($fields['enrolenddisabled'])){
            $enrolenddate=make_timestamp($fields['enrolenddate']['year'],
                                        $fields['enrolenddate']['month'],
                                        $fields['enrolenddate']['day']);
            $enrolstartdate=make_timestamp($fields['enrolstartdate']['year'],
                                        $fields['enrolstartdate']['month'],
                                        $fields['enrolstartdate']['day']);
            if ($enrolenddate <= $enrolstartdate){
                $errors['enroldateendgrp']=get_string("enrolenddaterror");
            }
        }
        if (0==count($errors)){    
            return true;
        }else {
            return $errors;
        }
    }
    $mform->addFormRule('form_check');

    $renderer =& $mform->defaultRenderer();
    $renderer->addStopFieldsetElements('submit');
?>
