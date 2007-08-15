<?php //$Id$

require_once($CFG->libdir.'/formslib.php');

class user_message_form extends moodleform {

    function definition() {
        $mform    =& $this->_form;
        $mform->addElement('header', 'general', get_string('message', 'message'));

        $mform->addElement('textarea', 'messagebody', get_string('messagebody'), array('rows'=>15, 'cols'=>30));
        $mform->setType('messagebody', PARAM_CLEANHTML);
        $mform->addRule('messagebody', '', 'required', null, 'client');
        $mform->setHelpButton('messagebody', array('writing', 'reading', 'questions', 'richtext'), false, 'editorhelpbutton');

        $mform->addElement('format', 'format', get_string('format'));
        
        $objs = array();
        foreach($this->_customdata['userlist'] as $k=>$u) {
            $user = get_record('user', 'id', $u);
            $objs[] =& $mform->createElement('static', null, null, '<input type="checkbox" name="userid['. $k .']" checked="checked" value="'.$u . '" />'. fullname($user));
        }
        $mform->addElement('group', 'users', 'Users', $objs, '<br />', false);

        $objs = array();
        $objs[] = &$mform->createElement('submit', 'send', 'Send');
        $objs[] = &$mform->createElement('submit', 'preview', 'Preview');
        $objs[] = &$mform->createElement('cancel');
        $mform->addElement('group', 'buttonar', '', $objs, ' ', false);
        $mform->closeHeaderBefore('buttonar');
    }
}
