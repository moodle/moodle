<?php  // $Id$

require_once($CFG->libdir.'/formslib.php');

class forum_post_form extends moodleform {

	function definition() {

		global $CFG;
		$mform    =& $this->_form;
		$renderer =& $mform->defaultRenderer();

        $course        = $this->_customdata['course'];
		$coursecontext = $this->_customdata['coursecontext'];
        $modcontext    = $this->_customdata['modcontext'];
        $forum         = $this->_customdata['forum'];
        $post          = $this->_customdata['post'];


        // the upload manager is used directly in post precessing, moodleform::save_files() is not used yet
        $this->_upload_manager = new upload_manager('attachment', true, false, $course, false, $forum->maxbytes, true, true);

        // set file max size to enable proper server side validation
        $maxbytes = get_max_upload_file_size($CFG->maxbytes, $course->maxbytes, $forum->maxbytes);
        $mform->setMaxFileSize($maxbytes);

        $mform->addElement('header', 'general', '');//fill in the data depending on page params
                                                    //later using set_defaults
		$mform->addElement('text', 'subject', get_string('subject', 'forum'), 'size="60"');
		$mform->setType('subject', PARAM_TEXT);
		$mform->addRule('subject', get_string('required'), 'required', null, 'client');

		$mform->addElement('htmleditor', 'message', get_string('message', 'forum'));
		$mform->setType('message', PARAM_RAW);
		$mform->addRule('message', get_string('required'), 'required', null, 'client');

        $mform->addElement('format', 'format', get_string('format'));


		if (isset($forum->id) && forum_is_forcesubscribed($forum->id)) {

			$mform->addElement('static', 'subscribemessage', get_string('subscription', 'forum'), get_string('everyoneissubscribed', 'forum'));
            $mform->addElement('hidden', 'subscribe');
            $mform->setHelpButton('subscribemessage', array('subscription', get_string('subscription', 'forum'), 'forum'));

		} else if (isset($forum->forcesubscribe)&& $forum->forcesubscribe != FORUM_DISALLOWSUBSCRIBE ||
		              has_capability('moodle/course:manageactivities', $coursecontext)) {
			$options = array();
			$options[0] = get_string('subscribestop', 'forum');
			$options[1] = get_string('subscribestart', 'forum');

			$mform->addElement('select', 'subscribe', get_string('subscription', 'forum'), $options);
            $mform->setHelpButton('subscribe', array('subscription', get_string('subscription', 'forum'), 'forum'));
		} else if ($forum->forcesubscribe == FORUM_DISALLOWSUBSCRIBE) {
			$mform->addElement('static', 'subscribemessage', get_string('subscription', 'forum'), get_string('disallowsubscribe', 'forum'));
            $mform->addElement('hidden', 'subscribe');
            $mform->setHelpButton('subscribemessage', array('subscription', get_string('subscription', 'forum'), 'forum'));
		}

        if ($forum->maxbytes != 1 && has_capability('mod/forum:createattachment', $modcontext))  {  //  1 = No attachments at all
            $mform->addElement('file', 'attachment', get_string('attachment', 'forum'));
            $mform->setHelpButton('attachment', array('attachment', get_string('attachemnt', 'forum'), 'forum'));
        
        }

        if (empty($post->id) && has_capability('moodle/course:manageactivities', $coursecontext)) {
            $mform->addElement('checkbox', 'mailnow', get_string('mailnow', 'forum'));
        }

		if (!isset($discussion->timestart)) {
			$discussion->timestart = 0;
		}
		if (!isset($discussion->timeend)) {
			$discussion->timeend = 0;
		}
		if (!empty($CFG->forum_enabletimedposts) && !$post->parent) {
            $mform->addElement('header', '', get_string('displayperiod', 'forum'));

		    $timestartgroup = array();
		    $timestartgroup[] = &MoodleQuickForm::createElement('date_selector', 'timestart', get_string('timestartday', 'forum'));
		    $timestartgroup[] = &MoodleQuickForm::createElement('checkbox', 'timestartdisabled', '', get_string('disable'));
            $mform->addGroup($timestartgroup, 'timestartgroup', get_string('displaystart', 'forum'), '&nbsp;', false);
			$mform->setHelpButton('timestartgroup', array('displayperiod', get_string('displayperiod', 'forum'), 'forum'));

			$timeendgroup = array();
		    $timeendgroup[] = &MoodleQuickForm::createElement('date_selector', 'timeend', get_string('timeendday', 'forum'));
			$timeendgroup[] = &MoodleQuickForm::createElement('checkbox', 'timeenddisabled', '', get_string('disable'));
            $mform->addGroup($timeendgroup, 'timeendgroup', get_string('displayend', 'forum'), '&nbsp;', false);
			$mform->setHelpButton('timeendgroup', array('displayperiod', get_string('displayperiod', 'forum'), 'forum'));

		} else {
			$mform->addElement('hidden', 'timestartdisabled', '1');
			$mform->setType('timestartdisabled', PARAM_INT);
			$mform->addElement('hidden', 'timeenddisabled', '1');
			$mform->setType('timeenddisabled', PARAM_INT);

		}
		if (isset($post->edit)) {
			$submit_string = get_string('savechanges');
		} else {
			$submit_string = get_string('posttoforum', 'forum');
		}
		$mform->addElement('submit', 'submit', $submit_string);
		$renderer->addStopFieldsetElements('submit');

		$mform->addElement('hidden', 'course');
		$mform->setType('course', PARAM_INT);

		$mform->addElement('hidden', 'forum');
		$mform->setType('forum', PARAM_INT);

		$mform->addElement('hidden', 'discussion');
		$mform->setType('discussion', PARAM_INT);

		$mform->addElement('hidden', 'parent');
		$mform->setType('parent', PARAM_INT);

		$mform->addElement('hidden', 'userid');
		$mform->setType('userid', PARAM_INT);

		$mform->addElement('hidden', 'groupid');
		$mform->setType('groupid', PARAM_INT);

		$mform->addElement('hidden', 'edit');
		$mform->setType('edit', PARAM_INT);

		$mform->addElement('hidden', 'reply');
		$mform->setType('reply', PARAM_INT);

	}

	function validation($data) {
	    $error = array();
        if (empty($data['timeenddisabled']) && empty($data['timestartdisabled'])
                     && $data['timeend'] <= $data['timestart']) {
             $error['timeendgroup'] = get_string('timestartenderror', 'forum');
        }
        return (count($error)==0) ? true : $error;
	}

}
?>
