<?php
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
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * This file defines de main basiclti configuration form
 *
 * @package blti
 * @copyright 2009 Marc Alier, Jordi Piguillem, Nikolas Galanis
 *  marc.alier@upc.edu
 * @copyright 2009 Universitat Politecnica de Catalunya http://www.upc.edu
 *
 * @author Marc Alier
 * @author Jordi Piguillem
 * @author Nikolas Galanis
 * @author Charles Severance
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/formslib.php');

class mod_blti_edit_types_form extends moodleform{

    function definition() {
        $mform    =& $this->_form;

//-------------------------------------------------------------------------------
        // Add basiclti elements
        $mform->addElement('header', 'setup', get_string('tool_settings', 'blti'));
        
        $mform->addElement('text', 'lti_typename', get_string('typename', 'blti'));
        $mform->setType('lti_typename', PARAM_INT);
//        $mform->addHelpButton('lti_typename', 'typename','blti');
        $mform->addRule('lti_typename', null, 'required', null, 'client');

        $regex = '/^(http|https):\/\/([a-z0-9-]\.+)*/i';

        $mform->addElement('text', 'lti_toolurl', get_string('toolurl', 'blti'), array('size'=>'64'));
        $mform->setType('lti_toolurl', PARAM_TEXT);
//        $mform->addHelpButton('lti_toolurl', 'toolurl', 'blti');
        $mform->addRule('lti_toolurl', get_string('validurl', 'blti'), 'regex', $regex, 'client');
        $mform->addRule('lti_toolurl', null, 'required', null, 'client');

        $mform->addElement('text', 'lti_resourcekey', get_string('resourcekey', 'blti'));
        $mform->setType('lti_resourcekey', PARAM_TEXT);

        $mform->addElement('passwordunmask', 'lti_password', get_string('password', 'blti'));
        $mform->setType('lti_password', PARAM_TEXT);

        $mform->addElement('textarea', 'lti_customparameters', get_string('custom', 'blti'), array('rows'=>4, 'cols'=>60));
        $mform->setType('lti_customparameters', PARAM_TEXT);
        
        $mform->addElement('checkbox', 'lti_coursevisible', '&nbsp;', ' ' . get_string('show_in_course', 'blti'));
        
        $launchoptions=array();
        $launchoptions[BLTI_LAUNCH_CONTAINER_EMBED] = get_string('embed', 'blti');
        $launchoptions[BLTI_LAUNCH_CONTAINER_EMBED_NO_BLOCKS] = get_string('embed_no_blocks', 'blti');
        $launchoptions[BLTI_LAUNCH_CONTAINER_WINDOW] = get_string('new_window', 'blti');

        $mform->addElement('select', 'lti_launchcontainer', get_string('default_launch_container', 'blti'), $launchoptions);
        $mform->setDefault('lti_launchcontainer', BLTI_LAUNCH_CONTAINER_EMBED_NO_BLOCKS);
//        $mform->addHelpButton('lti_launchinpopup', 'launchinpopup', 'blti');
        
        // Add privacy preferences fieldset where users choose whether to send their data
        $mform->addElement('header', 'privacy', get_string('privacy', 'blti'));

        $options=array();
        $options[0] = get_string('never', 'blti');
        $options[1] = get_string('always', 'blti');
        $options[2] = get_string('delegate_yes', 'blti');
        $options[3] = get_string('delegate_no', 'blti');

        $mform->addElement('select', 'lti_sendname', get_string('sendname', 'blti'), $options);
        $mform->setDefault('lti_sendname', '2');
//        $mform->addHelpButton('lti_sendname', 'sendname', 'blti');

        $mform->addElement('select', 'lti_sendemailaddr', get_string('sendemailaddr', 'blti'), $options);
        $mform->setDefault('lti_sendemailaddr', '2');
//        $mform->addHelpButton('lti_sendemailaddr', 'sendemailaddr', 'blti');

//-------------------------------------------------------------------------------
        // BLTI Extensions

        // Add grading preferences fieldset where the tool is allowed to return grades
        $mform->addElement('select', 'lti_acceptgrades', get_string('acceptgrades', 'blti'), $options);
        $mform->setDefault('lti_acceptgrades', '2');
//        $mform->addHelpButton('lti_acceptgrades', 'acceptgrades', 'blti');

        // Add grading preferences fieldset where the tool is allowed to retrieve rosters
        $mform->addElement('select', 'lti_allowroster', get_string('allowroster', 'blti'), $options);
        $mform->setDefault('lti_allowroster', '2');
//        $mform->addHelpButton('lti_allowroster', 'allowroster', 'blti');

                
//-------------------------------------------------------------------------------
        // Add setup parameters fieldset
        $mform->addElement('header', 'setupoptions', get_string('miscellaneous', 'blti'));

        // Adding option to change id that is placed in context_id
        $idoptions = array();
        $idoptions[0] = get_string('id', 'blti');
        $idoptions[1] = get_string('courseid', 'blti');

        $mform->addElement('select', 'lti_moodle_course_field', get_string('moodle_course_field', 'blti'), $idoptions);
        $mform->setDefault('lti_moodle_course_field', '0');
        

        $mform->addElement('text', 'lti_organizationid', get_string('organizationid', 'blti'));
        $mform->setType('lti_organizationid', PARAM_TEXT);
//        $mform->addHelpButton('lti_organizationid', 'organizationid', 'blti');

        $mform->addElement('text', 'lti_organizationurl', get_string('organizationurl', 'blti'));
        $mform->setType('lti_organizationurl', PARAM_TEXT);
//        $mform->addHelpButton('lti_organizationurl', 'organizationurl', 'blti');

        /* Suppress this for now - Chuck
        $mform->addElement('text', 'lti_organizationdescr', get_string('organizationdescr', 'blti'));
        $mform->setType('lti_organizationdescr', PARAM_TEXT);
        $mform->addHelpButton('lti_organizationdescr', 'organizationdescr', 'blti');
        */

//-------------------------------------------------------------------------------
        // Add a hidden element to signal a tool fixing operation after a problematic backup - restore process
        $mform->addElement('hidden', 'lti_fix');

//-------------------------------------------------------------------------------
        // Add standard buttons, common to all modules
        $this->add_action_buttons();

    }
}
