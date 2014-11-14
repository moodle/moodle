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
 * Form to edit handlers.
 *
 * @package    tool_messageinbound
 * @copyright  2014 Andrew Nicols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Form to edit handlers.
 *
 * @copyright  2014 Andrew Nicols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_messageinbound_edit_handler_form extends moodleform {
    public function definition() {
        $mform = $this->_form;

        $handler = $this->_customdata['handler'];

        // Set up the options for formatting text for descriptions, etc.
        $formatoptions = new stdClass();
        $formatoptions->trusted = false;
        $formatoptions->noclean = false;
        $formatoptions->smiley = false;
        $formatoptions->filter = false;
        $formatoptions->para = true;
        $formatoptions->newlines = false;
        $formatoptions->overflowdiv = true;

        // General information about the handler.
        $mform->addElement('header', 'general', get_string('general'));
        $mform->addElement('static', 'name', get_string('name', 'tool_messageinbound'),
            $handler->name);
        $mform->addElement('static', 'classname', get_string('classname', 'tool_messageinbound'));

        $description = format_text($handler->description, FORMAT_MARKDOWN, $formatoptions);

        $mform->addElement('static', 'description', get_string('description', 'tool_messageinbound'),
            $description);

        // Items which can be configured.
        $mform->addElement('header', 'configuration', get_string('configuration'));

        $options = array(
            HOURSECS => get_string('onehour', 'tool_messageinbound'),
            DAYSECS => get_string('oneday', 'tool_messageinbound'),
            WEEKSECS => get_string('oneweek', 'tool_messageinbound'),
            YEARSECS => get_string('oneyear', 'tool_messageinbound'),
            0 => get_string('noexpiry', 'tool_messageinbound'),
        );
        $mform->addElement('select', 'defaultexpiration', get_string('defaultexpiration', 'tool_messageinbound'), $options);
        $mform->addHelpButton('defaultexpiration', 'defaultexpiration', 'tool_messageinbound');

        if ($handler->can_change_validateaddress()) {
            $mform->addElement('checkbox', 'validateaddress', get_string('requirevalidation', 'tool_messageinbound'));
            $mform->addHelpButton('validateaddress', 'validateaddress', 'tool_messageinbound');
        } else {
            if ($handler->validateaddress) {
                $text = get_string('yes');
            } else {
                $text = get_string('no');
            }
            $mform->addElement('static', 'validateaddress_fake', get_string('requirevalidation', 'tool_messageinbound'), $text);
            $mform->addElement('hidden', 'validateaddress');
            $mform->addHelpButton('validateaddress_fake', 'fixedvalidateaddress', 'tool_messageinbound');
            $mform->setType('validateaddress', PARAM_INT);
        }

        if ($handler->can_change_enabled()) {
            $mform->addElement('checkbox', 'enabled', get_string('enabled', 'tool_messageinbound'));
        } else {
            if ($handler->enabled) {
                $text = get_string('yes');
            } else {
                $text = get_string('no');
            }
            $mform->addElement('static', 'enabled_fake', get_string('enabled', 'tool_messageinbound'), $text);
            $mform->addHelpButton('enabled', 'fixedenabled', 'tool_messageinbound');
            $mform->addElement('hidden', 'enabled');
            $mform->setType('enabled', PARAM_INT);
        }

        $this->add_action_buttons(true, get_string('savechanges'));
    }
}
