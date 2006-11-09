<?php
require_once ($CFG->libdir.'/formslib.php');
class data_mod_form extends moodleform_mod {

	function definition() {

		global $CFG;
		$mform    =& $this->_form;
		$renderer =& $mform->defaultRenderer();
		$course=$this->_customdata['course'];

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('name'));
		$mform->setType('name', PARAM_TEXT);
		$mform->addRule('name', null, 'required', null, 'client');

		$mform->addElement('htmleditor', 'intro', get_string('intro', 'data'));
		$mform->setType('intro', PARAM_RAW);
		$mform->addRule('intro', get_string('required'), 'required', null, 'client');

        $availablefromgroup=array();
	    $availablefromgroup[]=&MoodleQuickForm::createElement('date_selector', 'availablefrom', '');
	    $availablefromgroup[]=&MoodleQuickForm::createElement('checkbox', 'availablefromenabled', '', get_string('enable'));
        $mform->addGroup($availablefromgroup, 'availablefromgroup', get_string('availablefromdate', 'data'), '&nbsp;', false);

        $availabletogroup=array();
	    $availabletogroup[]=&MoodleQuickForm::createElement('date_selector', 'availableto', '');
	    $availabletogroup[]=&MoodleQuickForm::createElement('checkbox', 'availabletoenabled', '', get_string('enable'));
        $mform->addGroup($availabletogroup, 'availabletogroup', get_string('availabletodate', 'data'), '&nbsp;', false);

        $viewfromgroup=array();
	    $viewfromgroup[]=&MoodleQuickForm::createElement('date_selector', 'viewfrom', '');
	    $viewfromgroup[]=&MoodleQuickForm::createElement('checkbox', 'viewfromenabled', '', get_string('enable'));
        $mform->addGroup($viewfromgroup, 'viewfromgroup', get_string('viewfromdate', 'data'), '&nbsp;', false);

        $viewtogroup=array();
	    $viewtogroup[]=&MoodleQuickForm::createElement('date_selector', 'viewto', '');
	    $viewtogroup[]=&MoodleQuickForm::createElement('checkbox', 'viewtoenabled', '', get_string('enable'));
        $mform->addGroup($viewtogroup, 'viewtogroup', get_string('viewtodate', 'data'), '&nbsp;', false);


        $countoptions=  array(0=>get_string('none'))+
                        (array_combine(range(1, DATA_MAX_ENTRIES),//keys
                                        range(1, DATA_MAX_ENTRIES)));//values
        $mform->addElement('select', 'requiredentries', get_string('requiredentries', 'data'), $countoptions);
		$mform->setHelpButton('requiredentries', array('requiredentries', get_string('requiredentries', 'data'), 'data'));

        $mform->addElement('select', 'requiredentriestoview', get_string('requiredentriestoview', 'data'), $countoptions);
		$mform->setHelpButton('requiredentriestoview', array('requiredentriestoview', get_string('requiredentriestoview', 'data'), 'data'));

        $mform->addElement('select', 'maxentries', get_string('maxentries', 'data'), $countoptions);
		$mform->setHelpButton('maxentries', array('maxentries', get_string('maxentries', 'data'), 'data'));

        $ynoptions = array( 0 => get_string('no'), 1 => get_string('yes'));
        $mform->addElement('select', 'comments', get_string('comments', 'data'), $ynoptions);
		$mform->setHelpButton('comments', array('comments', get_string('allowcomments', 'data'), 'data'));

        $mform->addElement('select', 'approval', get_string('requireapproval', 'data'), $ynoptions);
		$mform->setHelpButton('approval', array('requireapproval', get_string('requireapproval', 'data'), 'data'));

        $mform->addElement('select', 'numberrssarticles', get_string('numberrssarticles', 'data') , $countoptions);
		$mform->setHelpButton('approval', array('requireapproval', get_string('requireapproval', 'data'), 'data'));

        $mform->addElement('checkbox', 'assessed', get_string("allowratings", "data") , get_string('ratingsuse', 'data'));

        $strscale = get_string('scale');
        $strscales = get_string('scales');
        $scales = get_scales_menu($course->id);
        foreach ($scales as $i => $scalename) {
            $grades[-$i] = $strscale .': '. $scalename;
        }
        for ($i=100; $i>=1; $i--) {
            $grades[$i] = $i;
        }
        $mform->addElement('select', 'scale', get_string('grade') , $grades);


        $this->standard_coursemodule_elements();

        $buttonarray=array();
        $buttonarray[] = &MoodleQuickForm::createElement('submit', 'submit', get_string('savechanges'));
        $buttonarray[] = &MoodleQuickForm::createElement('submit', 'cancel', get_string('cancel'));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
		$renderer->addStopFieldsetElements('buttonar');
	}



}
?>