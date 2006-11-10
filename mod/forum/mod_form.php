<?php
require_once ($CFG->libdir.'/formslib.php');
class forum_mod_form extends moodleform_mod {

	function definition() {

		global $CFG, $FORUM_TYPES;
		$mform    =& $this->_form;
		$renderer =& $mform->defaultRenderer();
		$course=$this->_customdata['course'];

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('forumname', 'forum'));
		$mform->setType('name', PARAM_TEXT);
		$mform->addRule('name', null, 'required', null, 'client');

        asort($FORUM_TYPES);
        $mform->addElement('select', 'type', get_string('forumtype', 'forum'), $FORUM_TYPES);
		$mform->setHelpButton('type', array('forumtype', get_string('forumtype', 'forum'), 'forum'));
		$mform->setDefault('type', 'general');

		$mform->addElement('htmleditor', 'intro', get_string('forumintro', 'forum'));
		$mform->setType('intro', PARAM_RAW);
		$mform->addRule('intro', get_string('required'), 'required', null, 'client');

        $options = array();
        $options[0] = get_string('no');
        $options[1] = get_string('yesforever', 'forum');
        $options[FORUM_INITIALSUBSCRIBE] = get_string('yesinitially', 'forum');
        $options[FORUM_DISALLOWSUBSCRIBE] = get_string('disallowsubscribe','forum');
        $mform->addElement('select', 'forcesubscribe', get_string('forcesubscribeq', 'forum'), $options);
		$mform->setHelpButton('forcesubscribe', array('subscription2', get_string('forcesubscribeq', 'forum'), 'forum'));

        $options = array();
        $options[FORUM_TRACKING_OPTIONAL] = get_string('trackingoptional', 'forum');
        $options[FORUM_TRACKING_OFF] = get_string('trackingoff', 'forum');
        $options[FORUM_TRACKING_ON] = get_string('trackingon', 'forum');
        $mform->addElement('select', 'trackingtype', get_string('trackingtype', 'forum'), $options);
		$mform->setHelpButton('trackingtype', array('trackingtype', get_string('trackingtype', 'forum'), 'forum'));

        $choices = get_max_upload_sizes($CFG->maxbytes, $course->maxbytes);
        $choices[1] = get_string('uploadnotallowed');
        $choices[0] = get_string('courseuploadlimit') . ' ('.display_size($course->maxbytes).')';
        $mform->addElement('select', 'maxbytes', get_string('maxattachmentsize', 'forum'), $choices);
		$mform->setHelpButton('maxbytes', array('maxattachmentsize', get_string('maxattachmentsize', 'forum'), 'forum'));
		$mform->setDefault('maxbytes', $CFG->forum_maxbytes);

        if ($CFG->enablerssfeeds && isset($CFG->forum_enablerssfeeds) && $CFG->forum_enablerssfeeds) {
            $mform->addElement('header', '', get_string('rss'));
            $choices = array();
            $choices[0] = get_string('none');
            $choices[1] = get_string('discussions', 'forum');
            $choices[2] = get_string('posts', 'forum');
            $mform->addElement('select', 'rsstype', get_string('rsstype'), $choices);
            $mform->setHelpButton('rsstype', array('rsstype', get_string('rsstype'), 'forum'));

            $choices = array();
            $choices[0] = '0';
            $choices[1] = '1';
            $choices[2] = '2';
            $choices[3] = '3';
            $choices[4] = '4';
            $choices[5] = '5';
            $choices[10] = '10';
            $choices[15] = '15';
            $choices[20] = '20';
            $choices[25] = '25';
            $choices[30] = '30';
            $choices[40] = '40';
            $choices[50] = '50';
            $mform->addElement('select', 'rssarticles', get_string('rssarticles'), $choices);
            $mform->setHelpButton('rssarticles', array('rssarticles', get_string('rssarticles'), 'forum'));
        }

        $mform->addElement('header', '', get_string('grade'));
        $mform->addElement('checkbox', 'assessed', get_string('allowratings', 'data') , get_string('ratingsuse', 'data'));

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

        $mform->addElement('date_time_selector', 'assesstimestart', get_string('from'));

        $mform->addElement('date_time_selector', 'assesstimefinish', get_string('to'));

        $mform->addElement('header', '', get_string('blockafter', 'forum'));
        $mform->addElement('text', 'warnafter', get_string('warnafter', 'forum'));
		$mform->setType('warnafter', PARAM_INT);
		$mform->setDefault('warnafter', '0');
		$mform->addRule('warnafter', null, 'required', null, 'client');
		$mform->addRule('warnafter', null, 'numeric', null, 'client');
		$mform->setHelpButton('warnafter', array('manageposts', get_string('warnafter', 'forum'),'forum'));

        $mform->addElement('text', 'blockafter', get_string('blockafter', 'forum'));
		$mform->setType('blockafter', PARAM_INT);
		$mform->setDefault('blockafter', '0');
		$mform->addRule('blockafter', null, 'required', null, 'client');
		$mform->addRule('blockafter', null, 'numeric', null, 'client');
		$mform->setHelpButton('blockafter', array('manageposts', get_string('blockafter', 'forum'),'forum'));

		$options = array();
        $options[0] = get_string('blockperioddisabled','forum');
        $options[60*60*24]   = '1 '.get_string('day');
        $options[60*60*24*2] = '2 '.get_string('days');
        $options[60*60*24*3] = '3 '.get_string('days');
        $options[60*60*24*4] = '4 '.get_string('days');
        $options[60*60*24*5] = '5 '.get_string('days');
        $options[60*60*24*6] = '6 '.get_string('days');
        $options[60*60*24*7] = '1 '.get_string('week');
        $mform->addElement('select', 'blockperiod', get_string("blockperiod", "forum") , $options);
		$mform->setHelpButton('blockperiod', array('manageposts', get_string('blockperiod', 'forum'),'forum'));

		$this->standard_coursemodule_elements();

        $buttonarray=array();
        $buttonarray[] = &MoodleQuickForm::createElement('submit', 'submit', get_string('savechanges'));
        $buttonarray[] = &MoodleQuickForm::createElement('submit', 'cancel', get_string('cancel'));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
		$renderer->addStopFieldsetElements('buttonar');
	}

	function definition_after_data(){
		$mform    =& $this->_form;
	    $type=&$mform->getElement('type');
        $typevalue=$mform->exportValue('type');
        if ($typevalue=='news' || $typevalue=='social'){
            $type->freeze();
            $type->setPersistantFreeze(true);
        }
	}



}
?>