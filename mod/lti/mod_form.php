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
//
// This file is part of BasicLTI4Moodle
//
// BasicLTI4Moodle is an IMS BasicLTI (Basic Learning Tools for Interoperability)
// consumer for Moodle 1.9 and Moodle 2.0. BasicLTI is a IMS Standard that allows web
// based learning tools to be easily integrated in LMS as native ones. The IMS BasicLTI
// specification is part of the IMS standard Common Cartridge 1.1 Sakai and other main LMS
// are already supporting or going to support BasicLTI. This project Implements the consumer
// for Moodle. Moodle is a Free Open source Learning Management System by Martin Dougiamas.
// BasicLTI4Moodle is a project iniciated and leaded by Ludo(Marc Alier) and Jordi Piguillem
// at the GESSI research group at UPC.
// SimpleLTI consumer for Moodle is an implementation of the early specification of LTI
// by Charles Severance (Dr Chuck) htp://dr-chuck.com , developed by Jordi Piguillem in a
// Google Summer of Code 2008 project co-mentored by Charles Severance and Marc Alier.
//
// BasicLTI4Moodle is copyright 2009 by Marc Alier Forment, Jordi Piguillem and Nikolas Galanis
// of the Universitat Politecnica de Catalunya http://www.upc.edu
// Contact info: Marc Alier Forment granludo @ gmail.com or marc.alier @ upc.edu

/**
 * This file defines the main lti configuration form
 *
 * @package    mod
 * @subpackage lti
 * @copyright  2009 Marc Alier, Jordi Piguillem, Nikolas Galanis
 *  marc.alier@upc.edu
 * @copyright  2009 Universitat Politecnica de Catalunya http://www.upc.edu
 * @author     Marc Alier
 * @author     Jordi Piguillem
 * @author     Nikolas Galanis
 * @author     Chris Scribner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/lti/locallib.php');

class mod_lti_mod_form extends moodleform_mod {

    public function definition() {
        global $DB, $PAGE, $OUTPUT, $USER, $COURSE;

        $this->typeid = 0;

        $mform =& $this->_form;
        //-------------------------------------------------------------------------------
        // Adding the "general" fieldset, where all the common settings are shown
        $mform->addElement('header', 'general', get_string('general', 'form'));
        // Adding the standard "name" field
        $mform->addElement('text', 'name', get_string('basicltiname', 'lti'), array('size'=>'64'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        // Adding the optional "intro" and "introformat" pair of fields
        $this->add_intro_editor(false, get_string('basicltiintro', 'lti'));
        $mform->setAdvanced('introeditor');

        // Display the label to the right of the checkbox so it looks better & matches rest of the form
        $coursedesc = $mform->getElement('showdescription');
        if(!empty($coursedesc)){
            $coursedesc->setText(' ' . $coursedesc->getLabel());
            $coursedesc->setLabel('&nbsp');
        }

        $mform->setAdvanced('showdescription');

        $mform->addElement('checkbox', 'showtitlelaunch', '&nbsp;', ' ' . get_string('display_name', 'lti'));
        $mform->setAdvanced('showtitlelaunch');
        $mform->addHelpButton('showtitlelaunch', 'display_name', 'lti');

        $mform->addElement('checkbox', 'showdescriptionlaunch', '&nbsp;', ' ' . get_string('display_description', 'lti'));
        $mform->setAdvanced('showdescriptionlaunch');
        $mform->addHelpButton('showdescriptionlaunch', 'display_description', 'lti');

        // Tool settings
        $tooltypes = $mform->addElement('select', 'typeid', get_string('external_tool_type', 'lti'), array());
        $mform->addHelpButton('typeid', 'external_tool_type', 'lti');

        foreach (lti_get_types_for_add_instance() as $id => $type) {
            if ($type->course == $COURSE->id) {
                $attributes = array( 'editable' => 1, 'courseTool' => 1, 'domain' => $type->tooldomain );
            } else if ($id != 0) {
                $attributes = array( 'globalTool' => 1, 'domain' => $type->tooldomain);
            } else {
                $attributes = array();
            }

            $tooltypes->addOption($type->name, $id, $attributes);
        }

        $mform->addElement('text', 'toolurl', get_string('launch_url', 'lti'), array('size'=>'64'));
        $mform->setType('toolurl', PARAM_TEXT);
        $mform->addHelpButton('toolurl', 'launch_url', 'lti');

        $mform->addElement('text', 'securetoolurl', get_string('secure_launch_url', 'lti'), array('size'=>'64'));
        $mform->setType('securetoolurl', PARAM_TEXT);
        $mform->setAdvanced('securetoolurl');
        $mform->addHelpButton('securetoolurl', 'secure_launch_url', 'lti');

        $launchoptions=array();
        $launchoptions[LTI_LAUNCH_CONTAINER_DEFAULT] = get_string('default', 'lti');
        $launchoptions[LTI_LAUNCH_CONTAINER_EMBED] = get_string('embed', 'lti');
        $launchoptions[LTI_LAUNCH_CONTAINER_EMBED_NO_BLOCKS] = get_string('embed_no_blocks', 'lti');
        $launchoptions[LTI_LAUNCH_CONTAINER_WINDOW] = get_string('new_window', 'lti');

        $mform->addElement('select', 'launchcontainer', get_string('launchinpopup', 'lti'), $launchoptions);
        $mform->setDefault('launchcontainer', LTI_LAUNCH_CONTAINER_DEFAULT);
        $mform->addHelpButton('launchcontainer', 'launchinpopup', 'lti');

        $mform->addElement('text', 'resourcekey', get_string('resourcekey', 'lti'));
        $mform->setType('resourcekey', PARAM_TEXT);
        $mform->setAdvanced('resourcekey');
        $mform->addHelpButton('resourcekey', 'resourcekey', 'lti');

        $mform->addElement('passwordunmask', 'password', get_string('password', 'lti'));
        $mform->setType('password', PARAM_TEXT);
        $mform->setAdvanced('password');
        $mform->addHelpButton('password', 'password', 'lti');

        $mform->addElement('textarea', 'instructorcustomparameters', get_string('custom', 'lti'), array('rows'=>4, 'cols'=>60));
        $mform->setType('instructorcustomparameters', PARAM_TEXT);
        $mform->setAdvanced('instructorcustomparameters');
        $mform->addHelpButton('instructorcustomparameters', 'custom', 'lti');

        $mform->addElement('text', 'icon', get_string('icon_url', 'lti'), array('size'=>'64'));
        $mform->setType('icon', PARAM_TEXT);
        $mform->setAdvanced('icon');
        $mform->addHelpButton('icon', 'icon_url', 'lti');

        $mform->addElement('text', 'secureicon', get_string('secure_icon_url', 'lti'), array('size'=>'64'));
        $mform->setType('secureicon', PARAM_TEXT);
        $mform->setAdvanced('secureicon');
        $mform->addHelpButton('secureicon', 'secure_icon_url', 'lti');

        //-------------------------------------------------------------------------------
        // Add privacy preferences fieldset where users choose whether to send their data
        $mform->addElement('header', 'privacy', get_string('privacy', 'lti'));

        $mform->addElement('checkbox', 'instructorchoicesendname', '&nbsp;', ' ' . get_string('share_name', 'lti'));
        $mform->setDefault('instructorchoicesendname', '1');
        $mform->addHelpButton('instructorchoicesendname', 'share_name', 'lti');

        $mform->addElement('checkbox', 'instructorchoicesendemailaddr', '&nbsp;', ' ' . get_string('share_email', 'lti'));
        $mform->setDefault('instructorchoicesendemailaddr', '1');
        $mform->addHelpButton('instructorchoicesendemailaddr', 'share_email', 'lti');

        $mform->addElement('checkbox', 'instructorchoiceacceptgrades', '&nbsp;', ' ' . get_string('accept_grades', 'lti'));
        $mform->setDefault('instructorchoiceacceptgrades', '1');
        $mform->addHelpButton('instructorchoiceacceptgrades', 'accept_grades', 'lti');

        //$mform->addElement('checkbox', 'instructorchoiceallowroster', '&nbsp;', ' ' . get_string('share_roster', 'lti'));
        //$mform->setDefault('instructorchoiceallowroster', '1');
        //$mform->addHelpButton('instructorchoiceallowroster', 'share_roster', 'lti');

        //-------------------------------------------------------------------------------

        /**
        $debugoptions=array();
        $debugoptions[0] = get_string('debuglaunchoff', 'lti');
        $debugoptions[1] = get_string('debuglaunchon', 'lti');

        $mform->addElement('select', 'debuglaunch', get_string('debuglaunch', 'lti'), $debugoptions);

        if (isset($this->typeconfig['debuglaunch'])) {
            if ($this->typeconfig['debuglaunch'] == 0) {
                $mform->setDefault('debuglaunch', '0');
            } else if ($this->typeconfig['debuglaunch'] == 1) {
                $mform->setDefault('debuglaunch', '1');
            }
        }
        */

        //-------------------------------------------------------------------------------
        // add standard elements, common to all modules
        $this->standard_coursemodule_elements();
        $mform->setAdvanced('cmidnumber');
        //-------------------------------------------------------------------------------
        // add standard buttons, common to all modules
        $this->add_action_buttons();

        $editurl = new moodle_url("/mod/lti/instructor_edit_tool_type.php?sesskey={$USER->sesskey}&course={$COURSE->id}");
        $ajaxurl = new moodle_url('/mod/lti/ajax.php');

        $jsinfo = (object)array(
                        'edit_icon_url' => (string)$OUTPUT->pix_url('t/edit'),
                        'add_icon_url' => (string)$OUTPUT->pix_url('t/add'),
                        'delete_icon_url' => (string)$OUTPUT->pix_url('t/delete'),
                        'green_check_icon_url' => (string)$OUTPUT->pix_url('i/valid'),
                        'warning_icon_url' => (string)$OUTPUT->pix_url('warning', 'lti'),
                        'instructor_tool_type_edit_url' => $editurl->out(false),
                        'ajax_url' => $ajaxurl->out(true),
                        'courseId' => $COURSE->id
                  );

        $module = array(
            'name'      => 'mod_lti_edit',
            'fullpath'  => '/mod/lti/mod_form.js',
            'requires'  => array('base', 'io', 'querystring-stringify-simple', 'node', 'event', 'json-parse'),
            'strings'   => array(
                array('addtype', 'lti'),
                array('edittype', 'lti'),
                array('deletetype', 'lti'),
                array('delete_confirmation', 'lti'),
                array('cannot_edit', 'lti'),
                array('cannot_delete', 'lti'),
                array('global_tool_types', 'lti'),
                array('course_tool_types', 'lti'),
                array('using_tool_configuration', 'lti'),
                array('domain_mismatch', 'lti'),
                array('custom_config', 'lti'),
                array('tool_config_not_found', 'lti'),
                array('forced_help', 'lti')
            ),
        );

        $PAGE->requires->js_init_call('M.mod_lti.editor.init', array(json_encode($jsinfo)), true, $module);
    }

    /**
     * Make fields editable or non-editable depending on the administrator choices
     * @see moodleform_mod::definition_after_data()
     */
    public function definition_after_data() {
        parent::definition_after_data();

        //$mform =& $this->_form;
    }

    /**
     * Function overwritten to change default values using
     * global configuration
     *
     * @param array $default_values passed by reference
     */
    public function data_preprocessing(&$default_values) {

    }
}

