<?php

/**
 * Version details
 *
 * @package    local_message
 * @author  Albohtori
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_message\form;

use moodleform;

//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");

class edit extends moodleform
{
    //Add elements to form
    public function definition()
    {
        global $CFG;

        $mform = $this->_form; // Don't forget the underscore! 

        $mform->addElement('text', 'messagetext', get_string('message_type', 'local_message')); // Add elements to your form.
        $mform->setType('messagetext', PARAM_NOTAGS);                   // Set type of element.
        $mform->setDefault('messagetext', get_string('enter_message', 'local_message'));        // Default value.


        $choises = array();
        $choises['0'] = \core\output\notification::NOTIFY_WARNING;
        $choises['1'] = \core\output\notification::NOTIFY_SUCCESS;
        $choises['2'] = \core\output\notification::NOTIFY_ERROR;
        $choises['3'] = \core\output\notification::NOTIFY_INFO;

        $mform->setDefault('messagetype', 3);

        $mform->addElement('select', 'messagetype', get_string('message_type', 'local_message'), $choises);

        $this->add_action_buttons();
    }

    //Custom validation should be added here
    function validation($data, $files)
    {
        return array();
    }
}
