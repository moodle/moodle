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
 * Form for editing steps.
 *
 * @package    tool_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_usertours\local\forms;

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

require_once($CFG->libdir . '/formslib.php');

/**
 * Form for editing steps.
 *
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class editstep extends \moodleform {
    /**
     * @var tool_usertours\step $step
     */
    protected $step;

    /**
     * Create the edit step form.
     *
     * @param   string      $target     The target of the form.
     * @param   step        $step       The step being editted.
     */
    public function __construct($target, \tool_usertours\step $step) {
        $this->step = $step;

        parent::__construct($target);
    }

    /**
     * Form definition.
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('header', 'heading_target', get_string('target_heading', 'tool_usertours'));
        $types = [];
        foreach (\tool_usertours\target::get_target_types() as $value => $type) {
            $types[$value] = get_string('target_' . $type, 'tool_usertours');
        }
        $mform->addElement('select', 'targettype', get_string('targettype', 'tool_usertours'), $types);
        $mform->addHelpButton('targettype', 'targettype', 'tool_usertours');

        // The target configuration.
        foreach (\tool_usertours\target::get_target_types() as $value => $type) {
            $targetclass = \tool_usertours\target::get_classname($type);
            $targetclass::add_config_to_form($mform);
        }

        // Content of the step.
        $mform->addElement('header', 'heading_content', get_string('content_heading', 'tool_usertours'));
        $mform->addElement('textarea', 'title', get_string('title', 'tool_usertours'));
        $mform->addRule('title', get_string('required'), 'required', null, 'client');
        $mform->setType('title', PARAM_TEXT);
        $mform->addHelpButton('title', 'title', 'tool_usertours');

        $mform->addElement('textarea', 'content', get_string('content', 'tool_usertours'));
        $mform->addRule('content', get_string('required'), 'required', null, 'client');
        $mform->setType('content', PARAM_RAW);
        $mform->addHelpButton('content', 'content', 'tool_usertours');

        // Add the step configuration.
        $mform->addElement('header', 'heading_options', get_string('options_heading', 'tool_usertours'));
        // All step configuration is defined in the step.
        $this->step->add_config_to_form($mform);

        // And apply any form constraints.
        foreach (\tool_usertours\target::get_target_types() as $value => $type) {
            $targetclass = \tool_usertours\target::get_classname($type);
            $targetclass::add_disabled_constraints_to_form($mform);
        }

        $this->add_action_buttons();
    }
}
