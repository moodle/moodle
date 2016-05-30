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
class tool_installaddon_installfromzip_form extends moodleform {

    /**
     * Defines the form elements
     */
    public function definition() {

        $mform = $this->_form;
        $installer = $this->_customdata['installer'];

        $mform->addElement('header', 'general', get_string('installfromzip', 'tool_installaddon'));
        $mform->addHelpButton('general', 'installfromzip', 'tool_installaddon');

        $mform->addElement('filepicker', 'zipfile', get_string('installfromzipfile', 'tool_installaddon'),
            null, array('accepted_types' => '.zip'));
        $mform->addHelpButton('zipfile', 'installfromzipfile', 'tool_installaddon');
        $mform->addRule('zipfile', null, 'required', null, 'client');

        $options = $installer->get_plugin_types_menu();
        $mform->addElement('select', 'plugintype', get_string('installfromziptype', 'tool_installaddon'), $options,
            array('id' => 'tool_installaddon_installfromzip_plugintype'));
        $mform->addHelpButton('plugintype', 'installfromziptype', 'tool_installaddon');
        $mform->setAdvanced('plugintype');

        $mform->addElement('static', 'permcheck', '',
            html_writer::span(get_string('permcheck', 'tool_installaddon'), '',
                array('id' => 'tool_installaddon_installfromzip_permcheck')));
        $mform->setAdvanced('permcheck');

        $mform->addElement('text', 'rootdir', get_string('installfromziprootdir', 'tool_installaddon'));
        $mform->addHelpButton('rootdir', 'installfromziprootdir', 'tool_installaddon');
        $mform->setType('rootdir', PARAM_PLUGIN);
        $mform->setAdvanced('rootdir');

        $this->add_action_buttons(false, get_string('installfromzipsubmit', 'tool_installaddon'));
    }

    /**
     * Switch the form to a mode that requires manual selection of the plugin type
     */
    public function require_explicit_plugintype() {

        $mform = $this->_form;

        $mform->addRule('plugintype', get_string('required'), 'required', null, 'client');
        $mform->setAdvanced('plugintype', false);
        $mform->setAdvanced('permcheck', false);

        $typedetectionfailed = $mform->createElement('static', 'typedetectionfailed', '',
            html_writer::span(get_string('typedetectionfailed', 'tool_installaddon'), 'error'));
        $mform->insertElementBefore($typedetectionfailed, 'permcheck');
    }

    /**
     * Warn that the selected plugin type does not match the detected one.
     *
     * @param string $detected detected plugin type
     */
    public function selected_plugintype_mismatch($detected) {

        $mform = $this->_form;
        $mform->addRule('plugintype', get_string('required'), 'required', null, 'client');
        $mform->setAdvanced('plugintype', false);
        $mform->setAdvanced('permcheck', false);
        $mform->insertElementBefore($mform->createElement('static', 'selectedplugintypemismatch', '',
            html_writer::span(get_string('typedetectionmismatch', 'tool_installaddon', $detected), 'error')), 'permcheck');
    }

    /**
     * Validate the form fields
     *
     * @param array $data
     * @param array $files
     * @return array (string)field name => (string)validation error text
     */
    public function validation($data, $files) {

        $pluginman = core_plugin_manager::instance();
        $errors = parent::validation($data, $files);

        if (!empty($data['plugintype'])) {
            if (!$pluginman->is_plugintype_writable($data['plugintype'])) {
                $path = $pluginman->get_plugintype_root($data['plugintype']);
                $errors['plugintype'] = get_string('permcheckresultno', 'tool_installaddon', array('path' => $path));
            }
        }

        return $errors;
    }
}
