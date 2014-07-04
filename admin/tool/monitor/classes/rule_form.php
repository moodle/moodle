<?php
/**
 * The mform for creating and editing a rule
 *
 * @copyright 2014 onwards Simey Lameze <lameze@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package tool_monitor
 */
namespace tool_monitor;

require_once($CFG->dirroot.'/lib/formslib.php');

class rule_form extends \moodleform {

    function definition () {
        global $CFG, $USER, $OUTPUT;
        $mform = $this->_form;

        // General section header
        $mform->addElement('header', 'general', get_string('general'));
        // Hidden rule ID
        $mform->addElement('hidden', 'ruleid');
        $mform->setType('ruleid', PARAM_INT);
        $mform->setDefault('ruleid', '');
        // Hidden course ID
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);
        $mform->setDefault('courseid', '');
        // Name field
        $mform->addElement('text', 'name', get_string('name','report_monitor'), 'size="50"');
        $mform->addRule('name', get_string('required'), 'required');
        $mform->setType('name', PARAM_TEXT);
        $mform->addHelpButton('name', 'name', 'report_monitor');
        // Plugin field
        $mform->addElement('select', 'plugin', get_string('plugin', 'report_monitor'), $pluginslist);
        $mform->addRule('plugin', get_string('required'), 'required');
        $mform->addHelpButton('plugin', 'plugin', 'report_monitor');
        // Event field
        $mform->addElement('select', 'event', get_string('event', 'report_monitor'), $eventoption);
        $mform->addRule('event', get_string('required'), 'required');
        $mform->addHelpButton('event', 'event', 'report_monitor');
        // Description field
        $mform->addElement('editor', 'description', get_string('description', 'report_monitor'));
        $mform->addHelpButton('description', 'description', 'report_monitor');
        // Customize your trigger section
        $mform->addElement('header', 'customize', get_string('customize', 'report_monitor'));
        // Call the filters
        $filter = new filter_manager();
        foreach ($filter->get_filters() as $filterobj) {
            $filterobj->add_form_elements($mform);
        }
        // Customize your trigger message section
        $mform->addElement('header', 'message', get_string('message_header', 'report_monitor'));
        // Message template field
        $mform->addElement('editor', 'message_template', get_string('message_template', 'report_monitor'));
        $mform->setDefault('message_template', get_string('defaultmessagetpl', 'report_monitor'));
        $mform->addRule('message_template', get_string('required'), 'required');
        $mform->addHelpButton('message_template', 'message_template', 'report_monitor');
        // Submit button
        $this->add_action_buttons(false, get_string('savechanges'));
    }

    /**
     * Form validation
     *
     * @param array $data data from the form.
     * @param array $files files uploaded.
     * @return array of errors.
     */
    function validation($data, $files) {

    }
}
?>