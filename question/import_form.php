<?php  // $Id$

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

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
        $radioarray = array();
        foreach ($fileformatnames as $id => $fileformatname) {
            $radioarray[] = &MoodleQuickForm::createElement('radio','format','',$fileformatname,$id );
        }
        $mform->addGroup($radioarray,'format', '', array('<br />'), false);
        $mform->addRule('format', null, 'required', null, 'client' );
        $mform->setHelpButton('format', array('import', get_string('importquestions', 'quiz'), 'quiz'));

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

        $this->set_upload_manager(new upload_manager('newfile', true, false, $COURSE, false, 0, false, true, false));
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
    }
    function get_importfile_name(){
        if ($this->is_submitted() and $this->is_validated()) {
            // return the temporary filename to process
            return $this->_upload_manager->files['newfile']['tmp_name'];
        }else{
            return  NULL;
        }
    }
    
    function get_importfile_realname(){
        if ($this->is_submitted() and $this->is_validated()) {
            // return the temporary filename to process
            return $this->_upload_manager->files['newfile']['name'];
        }else{
            return  NULL;
        }
    }
}
?>
