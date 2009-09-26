<?php  // $Id$

require_once($CFG->libdir.'/formslib.php');

class mod_forum_post_form extends moodleform {

    function definition() {

        global $CFG;
        $mform    =& $this->_form;

        $course        = $this->_customdata['course'];
        $cm            = $this->_customdata['cm'];
        $coursecontext = $this->_customdata['coursecontext'];
        $modcontext    = $this->_customdata['modcontext'];
        $forum         = $this->_customdata['forum'];
        $post          = $this->_customdata['post']; // hack alert


        // the upload manager is used directly in post precessing, moodleform::save_files() is not used yet
        $this->set_upload_manager(new upload_manager('attachment', true, false, $course, false, $forum->maxbytes, true, true));

        $mform->addElement('header', 'general', '');//fill in the data depending on page params
                                                    //later using set_data
        $mform->addElement('text', 'subject', get_string('subject', 'forum'), 'size="48"');
        $mform->setType('subject', PARAM_TEXT);
        $mform->addRule('subject', get_string('required'), 'required', null, 'client');
        $mform->addRule('subject', get_string('maximumchars', '', 255), 'maxlength', 255, 'client'); 

        $mform->addElement('htmleditor', 'message', get_string('message', 'forum'), array('cols'=>50, 'rows'=>30));
        $mform->setType('message', PARAM_RAW);
        $mform->addRule('message', get_string('required'), 'required', null, 'client');
        $mform->setHelpButton('message', array('reading', 'writing', 'questions', 'richtext'), false, 'editorhelpbutton');

        $mform->addElement('format', 'format', get_string('format'));


        if (isset($forum->id) && forum_is_forcesubscribed($forum)) {

            $mform->addElement('static', 'subscribemessage', get_string('subscription', 'forum'), get_string('everyoneissubscribed', 'forum'));
            $mform->addElement('hidden', 'subscribe');
            $mform->setType('subscribe', PARAM_INT);
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
            $mform->setType('subscribe', PARAM_INT);
            $mform->setHelpButton('subscribemessage', array('subscription', get_string('subscription', 'forum'), 'forum'));
        }

        if ($forum->maxbytes != 1 && has_capability('mod/forum:createattachment', $modcontext))  {  //  1 = No attachments at all
            $mform->addElement('file', 'attachment', get_string('attachment', 'forum'));
            $mform->setHelpButton('attachment', array('attachment', get_string('attachment', 'forum'), 'forum'));

        }

        if (empty($post->id) && has_capability('moodle/course:manageactivities', $coursecontext)) { // hack alert
            $mform->addElement('checkbox', 'mailnow', get_string('mailnow', 'forum'));
        }

        if (!empty($CFG->forum_enabletimedposts) && !$post->parent && has_capability('mod/forum:viewhiddentimedposts', $coursecontext)) { // hack alert
            $mform->addElement('header', '', get_string('displayperiod', 'forum'));

            $mform->addElement('date_selector', 'timestart', get_string('displaystart', 'forum'), array('optional'=>true));
            $mform->setHelpButton('timestart', array('displayperiod', get_string('displayperiod', 'forum'), 'forum'));

            $mform->addElement('date_selector', 'timeend', get_string('displayend', 'forum'), array('optional'=>true));
            $mform->setHelpButton('timeend', array('displayperiod', get_string('displayperiod', 'forum'), 'forum'));

        } else {
            $mform->addElement('hidden', 'timestart');
            $mform->setType('timestart', PARAM_INT);
            $mform->addElement('hidden', 'timeend');
            $mform->setType('timeend', PARAM_INT);
            $mform->setConstants(array('timestart'=> 0, 'timeend'=>0));
        }

        if (groups_get_activity_groupmode($cm, $course)) { // hack alert
            if (empty($post->groupid)) {
                $groupname = get_string('allparticipants');
            } else {
                $group = groups_get_group($post->groupid);
                $groupname = format_string($group->name);
            }
            $mform->addElement('static', 'groupinfo', get_string('group'), $groupname);
        }

//-------------------------------------------------------------------------------
        // buttons
        if (isset($post->edit)) { // hack alert
            $submit_string = get_string('savechanges');
        } else {
            $submit_string = get_string('posttoforum', 'forum');
        }
        $this->add_action_buttons(false, $submit_string);

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

    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if (($data['timeend']!=0) && ($data['timestart']!=0)
            && $data['timeend'] <= $data['timestart']) {
                $errors['timeend'] = get_string('timestartenderror', 'forum');
            }
        return $errors;
    }

}
?>
