<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot.'/lib/formslib.php');

class send_form extends moodleform {

    function definition () {

        $mform =& $this->_form;

        //$editoroptions = array('maxfiles'=>0, 'maxbytes'=>0, 'trusttext'=>false);
        $editoroptions = array();

        //width handled by css so cols is empty. Still present so the page validates.
        $displayoptions = array('rows'=>'4', 'cols'=>'', 'class'=>'messagesendbox');

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        //$mform->addElement('html', '<div class="message-send-box">');
            $mform->addElement('textarea', 'message', get_string('message', 'message'), $displayoptions, $editoroptions);
            //$mform->addElement('editor', 'message_editor', get_string('message', 'message'), null, $editoroptions);
        //$mform->addElement('html', '</div>');

        $this->add_action_buttons(false, get_string('sendmessage', 'message'));
    }

    /**
     * Used to structure incoming data for the message editor component
     *
     * @param <type> $data
     */
    function set_data($data) {

        //$data->message = array('text'=>$data->message, 'format'=>$data->messageformat);

        parent::set_data($data);
    }

    /**
     * Used to reformat the data from the editor component
     *
     * @return stdClass
     */
    function get_data() {
        $data = parent::get_data();

        /*if ($data !== null) {
            //$data->messageformat = $data->message_editor['format'];
            //$data->message = clean_text($data->message_editor['text'], $data->messageformat);
        }*/

        return $data;
    }

    /**
     * Resets the value of the message
     *
     * This is used because after we have acted on the submitted content we want to
     * re-display the form but with an empty message so the user can type the next
     * thing into it
     */
    //function reset_message() {
        //$this->_form->_elements[$this->_form->_elementIndex['message']]->setValue(array('text'=>''));
    //}

}

?>
