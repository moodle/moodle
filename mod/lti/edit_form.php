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
// Contact info: Marc Alier Forment granludo @ gmail.com or marc.alier @ upc.edu.

/**
 * This file defines de main basiclti configuration form
 *
 * @package mod_lti
 * @copyright  2009 Marc Alier, Jordi Piguillem, Nikolas Galanis
 *  marc.alier@upc.edu
 * @copyright  2009 Universitat Politecnica de Catalunya http://www.upc.edu
 * @author     Marc Alier
 * @author     Jordi Piguillem
 * @author     Nikolas Galanis
 * @author     Charles Severance
 * @author     Chris Scribner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/mod/lti/locallib.php');

class mod_lti_edit_types_form extends moodleform{
    public function definition() {
        global $CFG;

        $mform    =& $this->_form;

        $istool = $this->_customdata && $this->_customdata->istool;

        // Add basiclti elements.
        $mform->addElement('header', 'setup', get_string('tool_settings', 'lti'));

        $mform->addElement('text', 'lti_typename', get_string('typename', 'lti'));
        $mform->setType('lti_typename', PARAM_TEXT);
        $mform->addHelpButton('lti_typename', 'typename', 'lti');
        $mform->addRule('lti_typename', null, 'required', null, 'client');

        $mform->addElement('text', 'lti_toolurl', get_string('toolurl', 'lti'), array('size' => '64'));
        $mform->setType('lti_toolurl', PARAM_TEXT);
        $mform->addHelpButton('lti_toolurl', 'toolurl', 'lti');
        if (!$istool) {
            $mform->addRule('lti_toolurl', null, 'required', null, 'client');
        } else {
            $mform->disabledIf('lti_toolurl', null);
        }

        if (!$istool) {
            $mform->addElement('text', 'lti_resourcekey', get_string('resourcekey_admin', 'lti'));
            $mform->setType('lti_resourcekey', PARAM_TEXT);
            $mform->addHelpButton('lti_resourcekey', 'resourcekey_admin', 'lti');

            $mform->addElement('passwordunmask', 'lti_password', get_string('password_admin', 'lti'));
            $mform->setType('lti_password', PARAM_TEXT);
            $mform->addHelpButton('lti_password', 'password_admin', 'lti');
        }

        if ($istool) {
            $mform->addElement('textarea', 'lti_parameters', get_string('parameter', 'lti'), array('rows' => 4, 'cols' => 60));
            $mform->setType('lti_parameters', PARAM_TEXT);
            $mform->addHelpButton('lti_parameters', 'parameter', 'lti');
            $mform->disabledIf('lti_parameters', null);
        }

        $mform->addElement('textarea', 'lti_customparameters', get_string('custom', 'lti'), array('rows' => 4, 'cols' => 60));
        $mform->setType('lti_customparameters', PARAM_TEXT);
        $mform->addHelpButton('lti_customparameters', 'custom', 'lti');

        if (!$istool && !empty($this->_customdata->isadmin)) {
            $mform->addElement('checkbox', 'lti_coursevisible', '&nbsp;', ' ' . get_string('show_in_course', 'lti'));
            $mform->addHelpButton('lti_coursevisible', 'show_in_course', 'lti');
        } else {
            $mform->addElement('hidden', 'lti_coursevisible', '1');
        }
        $mform->setType('lti_coursevisible', PARAM_BOOL);

        $mform->addElement('hidden', 'typeid');
        $mform->setType('typeid', PARAM_INT);

        $launchoptions = array();
        $launchoptions[LTI_LAUNCH_CONTAINER_EMBED] = get_string('embed', 'lti');
        $launchoptions[LTI_LAUNCH_CONTAINER_EMBED_NO_BLOCKS] = get_string('embed_no_blocks', 'lti');
        $launchoptions[LTI_LAUNCH_CONTAINER_REPLACE_MOODLE_WINDOW] = get_string('existing_window', 'lti');
        $launchoptions[LTI_LAUNCH_CONTAINER_WINDOW] = get_string('new_window', 'lti');

        $mform->addElement('select', 'lti_launchcontainer', get_string('default_launch_container', 'lti'), $launchoptions);
        $mform->setDefault('lti_launchcontainer', LTI_LAUNCH_CONTAINER_EMBED_NO_BLOCKS);
        $mform->addHelpButton('lti_launchcontainer', 'default_launch_container', 'lti');
        $mform->setType('lti_launchcontainer', PARAM_INT);

        $mform->addElement('hidden', 'oldicon');
        $mform->setType('oldicon', PARAM_URL);

        $mform->addElement('text', 'lti_icon', get_string('icon_url', 'lti'), array('size' => '64'));
        $mform->setType('lti_icon', PARAM_URL);
        $mform->setAdvanced('lti_icon');
        $mform->addHelpButton('lti_icon', 'icon_url', 'lti');

        $mform->addElement('text', 'lti_secureicon', get_string('secure_icon_url', 'lti'), array('size' => '64'));
        $mform->setType('lti_secureicon', PARAM_URL);
        $mform->setAdvanced('lti_secureicon');
        $mform->addHelpButton('lti_secureicon', 'secure_icon_url', 'lti');

        if (!$istool) {
            // Add privacy preferences fieldset where users choose whether to send their data.
            $mform->addElement('header', 'privacy', get_string('privacy', 'lti'));

            $options = array();
            $options[0] = get_string('never', 'lti');
            $options[1] = get_string('always', 'lti');
            $options[2] = get_string('delegate', 'lti');

            $mform->addElement('select', 'lti_sendname', get_string('share_name_admin', 'lti'), $options);
            $mform->setType('lti_sendname', PARAM_INT);
            $mform->setDefault('lti_sendname', '2');
            $mform->addHelpButton('lti_sendname', 'share_name_admin', 'lti');

            $mform->addElement('select', 'lti_sendemailaddr', get_string('share_email_admin', 'lti'), $options);
            $mform->setType('lti_sendemailaddr', PARAM_INT);
            $mform->setDefault('lti_sendemailaddr', '2');
            $mform->addHelpButton('lti_sendemailaddr', 'share_email_admin', 'lti');

            // LTI Extensions.

            // Add grading preferences fieldset where the tool is allowed to return grades.
            $mform->addElement('select', 'lti_acceptgrades', get_string('accept_grades_admin', 'lti'), $options);
            $mform->setType('lti_acceptgrades', PARAM_INT);
            $mform->setDefault('lti_acceptgrades', '2');
            $mform->addHelpButton('lti_acceptgrades', 'accept_grades_admin', 'lti');

            $mform->addElement('checkbox', 'lti_forcessl', '&nbsp;', ' ' . get_string('force_ssl', 'lti'), $options);
            $mform->setType('lti_forcessl', PARAM_BOOL);
            if (!empty($CFG->mod_lti_forcessl)) {
                $mform->setDefault('lti_forcessl', '1');
                $mform->freeze('lti_forcessl');
            } else {
                $mform->setDefault('lti_forcessl', '0');
            }
            $mform->addHelpButton('lti_forcessl', 'force_ssl', 'lti');

            if (!empty($this->_customdata->isadmin)) {
                // Add setup parameters fieldset.
                $mform->addElement('header', 'setupoptions', get_string('miscellaneous', 'lti'));

                // Adding option to change id that is placed in context_id.
                $idoptions = array();
                $idoptions[0] = get_string('id', 'lti');
                $idoptions[1] = get_string('courseid', 'lti');

                $mform->addElement('text', 'lti_organizationid', get_string('organizationid', 'lti'));
                $mform->setType('lti_organizationid', PARAM_TEXT);
                $mform->addHelpButton('lti_organizationid', 'organizationid', 'lti');

                $mform->addElement('text', 'lti_organizationurl', get_string('organizationurl', 'lti'));
                $mform->setType('lti_organizationurl', PARAM_TEXT);
                $mform->addHelpButton('lti_organizationurl', 'organizationurl', 'lti');
            }
        }

        /* Suppress this for now - Chuck
         * mform->addElement('text', 'lti_organizationdescr', get_string('organizationdescr', 'lti'))
         * mform->setType('lti_organizationdescr', PARAM_TEXT)
         * mform->addHelpButton('lti_organizationdescr', 'organizationdescr', 'lti')
         */

        /*
        // Add a hidden element to signal a tool fixing operation after a problematic backup - restore process
        //$mform->addElement('hidden', 'lti_fix');
        */

        $tab = optional_param('tab', '', PARAM_ALPHAEXT);
        $mform->addElement('hidden', 'tab', $tab);
        $mform->setType('tab', PARAM_ALPHAEXT);

        $courseid = optional_param('course', 1, PARAM_INT);
        $mform->addElement('hidden', 'course', $courseid);
        $mform->setType('course', PARAM_INT);

        // Add standard buttons, common to all modules.
        $this->add_action_buttons();

    }
}
