<?php
namespace tool_bruteforce\form;

// Form to capture reason for manual unblock.

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/formslib.php');

class unblock extends \moodleform {
    protected function definition() {
        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'type');
        $mform->setType('type', PARAM_ALPHA);

        $mform->addElement('hidden', 'value');
        $mform->setType('value', PARAM_RAW_TRIMMED);

        $mform->addElement('text', 'reason', get_string('reason', 'tool_bruteforce'), ['size' => 40]);
        $mform->setType('reason', PARAM_TEXT);

        $this->add_action_buttons(true, get_string('unblock', 'tool_bruteforce'));
    }
}
