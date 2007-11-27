<?php
require_once ($CFG->dirroot.'/course/moodleform_mod.php');

class mod_wiki_mod_form extends moodleform_mod {

    function definition() {

        global $CFG, $COURSE, $WIKI_TYPES;
        $mform    =& $this->_form;

        if (!empty($this->_instance)) {
            $queryobject = new stdClass();
            $queryobject->id = $this->_instance;
            $wikihasentries = wiki_has_entries($queryobject);
        } else {
            $wikihasentries=false;
        }
//-------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));
        $mform->addElement('text', 'name', get_string('name'), array('size'=>'64'));
        $mform->setType('name', PARAM_NOTAGS);
        $mform->addRule('name', null, 'required', null, 'client');

        $mform->addElement('htmleditor', 'summary', get_string('summary'));
        $mform->setType('summary', PARAM_RAW);
        $mform->setHelpButton('summary', array('writing', 'questions', 'richtext'), false, 'editorhelpbutton');
        $mform->addRule('summary', get_string('required'), 'required', null, 'client');

        if (!$wikihasentries){
            asort($WIKI_TYPES);
            $mform->addElement('select', 'wtype', get_string('wikitype', 'wiki'), $WIKI_TYPES);
            $mform->setHelpButton('wtype', array('wikitype', get_string('wikitype', 'wiki'), 'wiki'));
            $mform->setDefault('wtype', 'group');
        } else {
            $mform->addElement('static', 'wtype', get_string('wikitype', 'wiki'));
        }

        $mform->addElement('selectyesno', 'ewikiprinttitle', get_string('ewikiprinttitle', 'wiki'));
        $mform->setDefault('ewikiprinttitle', 1);
        $mform->setAdvanced('ewikiprinttitle');

        $htmlmodes = array(0 => get_string('nohtml', 'wiki'),
            1 => get_string('safehtml', 'wiki'),
            2 => get_string('htmlonly', 'wiki'));
        $mform->addElement('select', 'htmlmode', get_string('htmlmode', 'wiki'), $htmlmodes);
        $mform->setDefault('htmlmode', 2);
        $mform->setAdvanced('htmlmode');

        $mform->addElement('selectyesno', 'ewikiacceptbinary', get_string('ewikiacceptbinary', 'wiki'));
        $mform->setDefault('ewikiacceptbinary', 0);
        $mform->setHelpButton('ewikiacceptbinary', array('ewikiacceptbinary', get_string('ewikiacceptbinary', 'wiki'), 'wiki'));
        $mform->setAdvanced('ewikiacceptbinary');

        $mform->addElement('advcheckbox', 'disablecamelcase', get_string('wikilinkoptions', 'wiki'), get_string('disablecamel', 'wiki'));
        $mform->setDefault('disablecamelcase', 0);
        $mform->setHelpButton('disablecamelcase', array('wikilinkoptions', get_string('wikilinkoptions', 'wiki'), 'wiki'));
        $mform->setAdvanced('disablecamelcase');

        $studentadminoptionsgrp = array();
        $studentadminoptionsgrp[] =& $mform->createElement('advcheckbox', 'setpageflags', '', get_string('allowsetpage', 'wiki'));
        $mform->setDefault('setpageflags', 0);
        $studentadminoptionsgrp[] =& $mform->createElement('advcheckbox', 'strippages', '', get_string('allowstrippages', 'wiki'));
        $mform->setDefault('strippages', 0);
        $studentadminoptionsgrp[] =& $mform->createElement('advcheckbox', 'removepages', '', get_string('allowremovepages', 'wiki'));
        $mform->setDefault('removepages', 0);
        $studentadminoptionsgrp[] =& $mform->createElement('advcheckbox', 'revertchanges', '', get_string('allowrevertchanges', 'wiki'));
        $mform->setDefault('revertchanges', 0);
        $mform->addGroup($studentadminoptionsgrp, 'studentadminoptions', get_string('studentadminoptions', 'wiki'), null, false);
        $mform->setAdvanced('studentadminoptions');

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'optional', get_string('optional', 'form'));
        $mform->addElement('text', 'pagename', get_string('wikiname', 'wiki'));
        if ($wikihasentries) {
            $mform->hardFreeze('pagename');
        }
        $mform->setHelpButton('pagename', array('wikiname', get_string('wikiname', 'wiki'), 'wiki'));
        $mform->setType('pagename', PARAM_NOTAGS);
        $mform->setAdvanced('pagename');

        $mform->addElement('choosecoursefile', 'initialcontent', get_string('initialcontent', 'wiki'));
        $mform->setHelpButton('initialcontent', array('initialcontent', get_string('initialcontent', 'wiki'), 'wiki'));
        $mform->setAdvanced('initialcontent');

//-------------------------------------------------------------------------------
        $features = new stdClass;
        $features->groups = true;
        $features->groupings = true;
        $features->groupmembersonly = true;
        $this->standard_coursemodule_elements($features);
//-------------------------------------------------------------------------------
// buttons
        $this->add_action_buttons();

    }
}
?>
