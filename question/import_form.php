<?php

require_once($CFG->libdir.'/formslib.php');

class question_import_form extends moodleform {

    function definition() {
        global $COURSE;
        $mform    =& $this->_form;

        $defaultcategory   = $this->_customdata['defaultcategory'];
        $contexts   = $this->_customdata['contexts'];

//--------------------------------------------------------------------------------
        $mform->addElement('header','fileformat', get_string('fileformat','quiz'));
        $fileformatnames = get_import_export_formats('import');
        $i = 0 ;
        foreach ($fileformatnames as $shortname => $fileformatname) {
            $currentgrp1 = array();
            $currentgrp1[] = &$mform->createElement('radio','format','',$fileformatname,$shortname); 
            $mform->addGroup($currentgrp1,"formathelp[$i]",'',array('<br />'),false);
            $mform->setHelpButton("formathelp[$i]", array("$shortname",$fileformatname,"qformat_$shortname"));          
            $i++ ;
        }
        $mform->addRule("formathelp[0]", null, 'required', null, 'client' );
//--------------------------------------------------------------------------------
        $mform->addElement('header','general', get_string('general', 'form'));

        $mform->addElement('questioncategory', 'category', get_string('category','quiz'), compact('contexts'));
        $mform->setDefault('category', $defaultcategory);
        $mform->setHelpButton('category', array('importcategory', get_string('importcategory','quiz'), 'quiz'));

        $categorygroup = array();
        $categorygroup[] =& $mform->createElement('checkbox', 'catfromfile', '', get_string('getcategoryfromfile', 'question'));
        $categorygroup[] =& $mform->createElement('checkbox', 'contextfromfile', '', get_string('getcontextfromfile', 'question'));
        $mform->addGroup($categorygroup, 'categorygroup', '', '', false);
        $mform->disabledIf('categorygroup', 'catfromfile', 'notchecked');
        $mform->setDefault('catfromfile', 1);
        $mform->setDefault('contextfromfile', 1);


        $matchgrades = array();
        $matchgrades['error'] = get_string('matchgradeserror','quiz');
        $matchgrades['nearest'] = get_string('matchgradesnearest','quiz');
        $mform->addElement('select', 'matchgrades', get_string('matchgrades','quiz'), $matchgrades);
        $mform->setHelpButton('matchgrades', array('matchgrades', get_string('matchgrades','quiz'), 'quiz'));
        $mform->setDefault('matchgrades', 'error');

        $mform->addElement('selectyesno', 'stoponerror', get_string('stoponerror', 'quiz'));
        $mform->setDefault('stoponerror', 1);
        $mform->setHelpButton('stoponerror', array('stoponerror', get_string('stoponerror', 'quiz'), 'quiz'));

//--------------------------------------------------------------------------------
        $mform->addElement('header', 'importfileupload', get_string('importfileupload','quiz'));

        $mform->addElement('file', 'newfile', get_string('upload'));
//--------------------------------------------------------------------------------
        $mform->addElement('submit', 'submitbutton', get_string('uploadthisfile'));

//--------------------------------------------------------------------------------
        if (has_capability('moodle/course:managefiles', get_context_instance(CONTEXT_COURSE, $COURSE->id))){
            $mform->addElement('header', 'importfilearea', get_string('importfilearea','quiz'));

            $mform->addElement('choosecoursefile', 'choosefile', get_string('choosefile','quiz'));
//--------------------------------------------------------------------------------
            $mform->addElement('submit', 'submitbutton', get_string('importfromthisfile','quiz'));
        }
//--------------------------------------------------------------------------------
        $mform->addElement('static', 'dummy', '');
        $mform->closeHeaderBefore('dummy');

        // set a template for the format select elements
        $renderer =& $mform->defaultRenderer();
        $template = "{help} {element}\n";
        $renderer->setGroupElementTemplate($template, 'format');
    }
    function get_importfile_name(){
        if ($this->is_submitted() and $this->is_validated()) {
            // return the temporary filename to process
            // TODO change this to use the files API properly.
            return $_FILES['newfile']['tmp_name'];
//            return $this->_upload_manager->files['newfile']['tmp_name'];
        }else{
            return  NULL;
        }
    }

    function get_importfile_realname(){
        if ($this->is_submitted() and $this->is_validated()) {
            // return the temporary filename to process
            // TODO change this to use the files API properly.
            return $_FILES['newfile']['name'];
        }else{
            return  NULL;
        }
    }
}

