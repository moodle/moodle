<?php
/**
 * Rule form definition.
 *
 * @package    local_coursematrix
 * @copyright  2024 Author Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_coursematrix\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Form for editing a course matrix rule.
 *
 * @package    local_coursematrix
 * @copyright  2024 Author Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class rule_form extends \moodleform {

    /**
     * Define the form definition.
     */
    public function definition() {
        $mform = $this->_form;

        // Custom data passed to constructor.
        $dept = $this->_customdata['department'] ?? '';
        $job = $this->_customdata['jobtitle'] ?? '';

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        // If we are editing a specific combo, make these readonly or static.
        $mform->addElement('static', 'department_static', get_string('department', 'local_coursematrix'));
        $mform->setDefault('department_static', $dept);
        
        $mform->addElement('hidden', 'department');
        $mform->setType('department', PARAM_TEXT);
        $mform->setDefault('department', $dept);

        $mform->addElement('static', 'jobtitle_static', get_string('jobtitle', 'local_coursematrix'));
        $mform->setDefault('jobtitle_static', $job);
        
        $mform->addElement('hidden', 'jobtitle');
        $mform->setType('jobtitle', PARAM_TEXT);
        $mform->setDefault('jobtitle', $job);

        // Courses autocomplete.
        global $DB;
        $allcourses = $DB->get_records_menu('course', [], 'fullname', 'id, fullname');
        if (isset($allcourses[1])) {
            unset($allcourses[1]);
        }
        
        $mform->addElement('autocomplete', 'courses', get_string('courses', 'local_coursematrix'), $allcourses, [
            'multiple' => true,
            'noselectionstring' => get_string('selectcourses', 'local_coursematrix'),
        ]);

        $this->add_action_buttons(true, get_string('save', 'local_coursematrix'));
    }
}
