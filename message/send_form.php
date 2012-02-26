<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Contains the definition of the form used to send messages
 *
 * @package    core_message
 * @copyright  2009 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot.'/lib/formslib.php');

/**
 * The form used by users to send instant messages
 *
 * @package   core_message
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class send_form extends moodleform {

    /**
     * Define the mform elements required
     */
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
     * @param array $data
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
