<?php // $Id$

    require_once("../../config.php");
    include_once $CFG->libdir.'/formslib.php';
    class mod_survey_details_form extends moodleform {

        function definition() {
            $mform    =& $this->_form;
            $lastform   = $this->_customdata['lastform'];
            $mform->addElement('static','namestatic',get_string('name','survey'),$lastform->name);

            if (!$lastform->intro) {
                $tempo = get_field("survey", "intro", "id", $lastform->template);
                $lastform->intro = get_string($tempo, "survey");
            }
            //TODO fix helpbuttons
            //emoticonhelpbutton("form", "intro");
            //helpbutton("writing", get_string("helpwriting"), "moodle", true, true, '', true)
            // helpbutton("text", get_string("helptext"), "moodle", true, true, '', true)

            $mform->setDefault('intro',$lastform->intro);
            $mform->addElement('textarea','intro',get_string("introtext", "survey"), 'wrap="virtual" rows="20" cols="50"');
            $mform->addElement('hidden', 'name', $lastform->name);
            $mform->addElement('hidden', 'template', $lastform->template);
            $mform->addElement('hidden', 'course', $lastform->course);
            $mform->addElement('hidden', 'coursemodule', $lastform->coursemodule);
            $mform->addElement('hidden', 'section', $lastform->section);
            $mform->addElement('hidden', 'module', $lastform->module);
            $mform->addElement('hidden', 'modulename', $lastform->modulename);
            $mform->addElement('hidden', 'instance', $lastform->instance);
            $mform->addElement('hidden', 'mode', $lastform->mode);
            $mform->addElement('hidden', 'visible', $lastform->visible);
            $mform->addElement('hidden', 'groupmode', $lastform->groupmode);
//-------------------------------------------------------------------------------
            // buttons
            $this->add_action_buttons(false);

        }
    }
    if ($lastform = data_submitted($CFG->wwwroot.'/course/mod.php')) {

        if (! $course = get_record("course", "id", $lastform->course)) {
            error("This course doesn't exist");
        }

        require_login($course->id, false);
        require_capability('moodle/course:manageactivities', get_context_instance(CONTEXT_COURSE, $course->id));

        $streditingasurvey = get_string("editingasurvey", "survey");
        $strsurveys = get_string("modulenameplural", "survey");

        print_header_simple("$streditingasurvey", "",
        "<a href=\"index.php?id=$course->id\">$strsurveys</a>".
        " -> ".stripslashes_safe($lastform->name)." ($streditingasurvey)");

        if (!$lastform->name or !$lastform->template) {
            // $_SERVER["HTTP_REFERER"] breaks xhtml, urlencode breaks continue button,
            // removing $_SERVER["HTTP_REFERER"]
            error(get_string("filloutallfields"));
        }
        $mform = new mod_survey_details_form($CFG->wwwroot.'/course/mod.php', array('lastform'=>stripslashes_safe($lastform)));
        $mform->display();
        print_footer($course);

    } else {
        error("You can't use this page like that!");
    }
?>
