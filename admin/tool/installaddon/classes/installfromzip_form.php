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
 * @package     tool_installaddon
 * @subpackage  classes
 * @category    form
 * @copyright   2013 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');

/**
 * Defines a simple form for uploading the add-on ZIP package
 *
 * @copyright 2013 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_installaddon_installfromzip extends moodleform {

    /**
     * Defines the form elements
     */
    public function definition() {

        $mform = $this->_form;
        $installer = $this->_customdata['installer'];

        $mform->addElement('header', 'general', get_string('installfromzip', 'tool_installaddon'));
        $mform->addHelpButton('general', 'installfromzip', 'tool_installaddon');

        $options = $installer->get_plugin_types_menu();
        $mform->addElement('select', 'plugintype', get_string('installfromziptype', 'tool_installaddon'), $options,
            array('id' => 'tool_installaddon_installfromzip_plugintype'));
        $mform->addHelpButton('plugintype', 'installfromziptype', 'tool_installaddon');
        $mform->addRule('plugintype', null, 'required', null, 'client');

        $mform->addElement('static', 'permcheck', '',
            html_writer::span(get_string('permcheck', 'tool_installaddon'), '',
                array('id' => 'tool_installaddon_installfromzip_permcheck')));

        $mform->addElement('filepicker', 'zipfile', get_string('installfromzipfile', 'tool_installaddon'),
            null, array('accepted_types' => '.zip'));
        $mform->addHelpButton('zipfile', 'installfromzipfile', 'tool_installaddon');
        $mform->addRule('zipfile', null, 'required', null, 'client');

        $mform->addElement('text', 'rootdir', get_string('installfromziprootdir', 'tool_installaddon'));
        $mform->addHelpButton('rootdir', 'installfromziprootdir', 'tool_installaddon');
        $mform->setType('rootdir', PARAM_PLUGIN);
        $mform->setAdvanced('rootdir');

        $mform->addElement('checkbox', 'acknowledgement', get_string('acknowledgement', 'tool_installaddon'),
            ' '.get_string('acknowledgementtext', 'tool_installaddon'));
        $mform->addRule('acknowledgement', get_string('acknowledgementmust', 'tool_installaddon'), 'required', null, 'client');

        $this->add_action_buttons(false, get_string('installfromzipsubmit', 'tool_installaddon'));
    }

    /**
     * Validate the form fields
     *
     * @param array $data
     * @param array $files
     * @return array (string)field name => (string)validation error text
     */
    public function validation($data, $files) {

        $installer = $this->_customdata['installer'];
        $errors = parent::validation($data, $files);

        if (!$installer->is_plugintype_writable($data['plugintype'])) {
            $path = $installer->get_plugintype_root($data['plugintype']);
            $errors['plugintype'] = get_string('permcheckresultno', 'tool_installaddon', array('path' => $path));
        }

        return $errors;
    }
}
