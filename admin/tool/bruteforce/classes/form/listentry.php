<?php
namespace tool_bruteforce\form;

// Form for adding or editing whitelist/blacklist entries.

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/formslib.php');

class listentry extends \moodleform {
    protected function definition() {
        $mform = $this->_form;
        $list = $this->_customdata['list'];

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'list', $list);
        $mform->setType('list', PARAM_ALPHA);

        $mform->addElement('select', 'type', get_string('type', 'tool_bruteforce'), [
            'user' => get_string('user'),
            'ip' => 'IP',
        ]);
        $mform->setType('type', PARAM_ALPHA);

        $mform->addElement('text', 'value', get_string('value', 'tool_bruteforce'), ['size' => 40]);
        $mform->setType('value', PARAM_RAW_TRIMMED);

        $mform->addElement('text', 'comment', get_string('comment', 'tool_bruteforce'), ['size' => 40]);
        $mform->setType('comment', PARAM_TEXT);

        $this->add_action_buttons(true, get_string('addentry', 'tool_bruteforce'));
    }

    /**
     * Validate form input.
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if ($data['type'] === 'ip'
                && !\core\ip_utils::is_ip_address($data['value'])
                && !\core\ip_utils::is_ipv4_range($data['value'])
                && !\core\ip_utils::is_ipv6_range($data['value'])) {
            $errors['value'] = get_string('invalidip', 'tool_bruteforce');
        }
        if ($data['type'] === 'user' && !is_numeric($data['value'])) {
            $errors['value'] = get_string('invaliduserid', 'tool_bruteforce');
        }
        return $errors;
    }
}
