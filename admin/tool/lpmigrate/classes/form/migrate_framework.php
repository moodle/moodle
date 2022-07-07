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
 * Form.
 *
 * @package    tool_lpmigrate
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_lpmigrate\form;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
\MoodleQuickForm::registerElementType('framework_autocomplete',
    $CFG->dirroot . '/admin/tool/lp/classes/form/framework_autocomplete.php',
    '\\tool_lp\\form\\framework_autocomplete');

/**
 * Form class.
 *
 * @package    tool_lpmigrate
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class migrate_framework extends \moodleform {

    /** @var context The page context. */
    protected $pagecontext;

    /**
     * Constructor.
     * @param \context $context The page context.
     */
    public function __construct(\context $context) {
        $this->pagecontext = $context;
        parent::__construct();
    }

    protected function definition() {
        $mform = $this->_form;

        $mform->addElement('header', 'hdrcourses', get_string('frameworks', 'tool_lpmigrate'));

        $mform->addElement('framework_autocomplete', 'from', get_string('migratefrom', 'tool_lpmigrate'), array(
            'contextid' => $this->pagecontext->id,
            'onlyvisible' => '0',
        ), 1, 2, 3);
        $mform->addRule('from', get_string('required'), 'required', null);
        $mform->addHelpButton('from', 'migratefrom', 'tool_lpmigrate');

        $mform->addElement('framework_autocomplete', 'to', get_string('migrateto', 'tool_lpmigrate'), array(
            'contextid' => $this->pagecontext->id,
            'onlyvisible' => '1',      // We cannot add competencies from hidden frameworks, so it must be visible.
        ), 1, 2, 3);
        $mform->addRule('to', get_string('required'), 'required', null);
        $mform->addHelpButton('to', 'migrateto', 'tool_lpmigrate');

        $mform->addElement('header', 'hdrcourses', get_string('courses'));
        $mform->addElement('course', 'allowedcourses', get_string('limittothese', 'tool_lpmigrate'),
            array('showhidden' => true, 'multiple' => true));
        $mform->addHelpButton('allowedcourses', 'allowedcourses', 'tool_lpmigrate');
        $mform->addElement('course', 'disallowedcourses', get_string('excludethese', 'tool_lpmigrate'),
            array('showhidden' => true, 'multiple' => true));
        $mform->addHelpButton('disallowedcourses', 'disallowedcourses', 'tool_lpmigrate');
        $mform->addElement('date_time_selector', 'coursestartdate', get_string('startdatefrom', 'tool_lpmigrate'),
            array('optional' => true));
        $mform->addHelpButton('coursestartdate', 'coursestartdate', 'tool_lpmigrate');

        $this->add_action_buttons(true, get_string('performmigration', 'tool_lpmigrate'));
    }

    public function validation($data, $files) {
        $errors = array();

        if ($data['from'] == $data['to']) {
            $errors['to'] = get_string('errorcannotmigratetosameframework', 'tool_lpmigrate');

        } else if (!empty($data['from']) && !empty($data['to'])) {
            $mapper = new \tool_lpmigrate\framework_mapper($data['from'], $data['to']);
            $mapper->automap();
            if (!$mapper->has_mappings()) {
                $errors['to'] = 'Could not map to any competency in this framework.';
            }
        }

        return $errors;
    }

}
