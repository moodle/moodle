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
 * @package basiclti
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

class mod_basiclti_edit_types_form extends moodleform{

    function definition() {
        $mform    =& $this->_form;

//-------------------------------------------------------------------------------
        // Add basiclti elements
        $mform->addElement('header', 'setup', get_string('modstandardels', 'form'));

        $mform->addElement('text', 'lti_typename', get_string('typename', 'basiclti'));
        $mform->setType('lti_typename', PARAM_INT);
//        $mform->addHelpButton('lti_typename', 'typename','basiclti');
        $mform->addRule('lti_typename', null, 'required', null, 'client');

        $regex = '/^(http|https):\/\/([a-z0-9-]\.+)*/i';

        $mform->addElement('text', 'lti_toolurl', get_string('toolurl', 'basiclti'), array('size'=>'64'));
        $mform->setType('lti_toolurl', PARAM_TEXT);
//        $mform->addHelpButton('lti_toolurl', 'toolurl', 'basiclti');
        $mform->addRule('lti_toolurl', get_string('validurl', 'basiclti'), 'regex', $regex, 'client');
        $mform->addRule('lti_toolurl', null, 'required', null, 'client');

        $mform->addElement('text', 'lti_resourcekey', get_string('resourcekey', 'basiclti'));
        $mform->setType('lti_resourcekey', PARAM_TEXT);

        $mform->addElement('passwordunmask', 'lti_password', get_string('password', 'basiclti'));
        $mform->setType('lti_password', PARAM_TEXT);

//-------------------------------------------------------------------------------
        // Add size parameters
        $mform->addElement('header', 'size', get_string('size', 'basiclti'));

        $mform->addElement('text', 'lti_preferheight', get_string('preferheight', 'basiclti'));
        $mform->setType('lti_preferheight', PARAM_INT);
//        $mform->addHelpButton('lti_preferheight', 'preferheight', 'basiclti');


//-------------------------------------------------------------------------------
        // Add privacy preferences fieldset where users choose whether to send their data
        $mform->addElement('header', 'privacy', get_string('privacy', 'basiclti'));

        $options=array();
        $options[0] = get_string('never', 'basiclti');
        $options[1] = get_string('always', 'basiclti');
        $options[2] = get_string('delegate', 'basiclti');

        $defaults=array();
        $defaults[0] = get_string('donot', 'basiclti');
        $defaults[1] = get_string('send', 'basiclti');

        $mform->addElement('select', 'lti_sendname', get_string('sendname', 'basiclti'), $options);
        $mform->setDefault('lti_sendname', '0');
//        $mform->addHelpButton('lti_sendname', 'sendname', 'basiclti');

        $mform->addElement('select', 'lti_instructorchoicesendname', get_string('setdefault', 'basiclti'), $defaults);
        $mform->setDefault('lti_instructorchoicesendname', '0');
        $mform->disabledIf('lti_instructorchoicesendname', 'lti_sendname', 'neq', 2);

        $mform->addElement('select', 'lti_sendemailaddr', get_string('sendemailaddr', 'basiclti'), $options);
        $mform->setDefault('lti_sendemailaddr', '0');
//        $mform->addHelpButton('lti_sendemailaddr', 'sendemailaddr', 'basiclti');

        $mform->addElement('select', 'lti_instructorchoicesendemailaddr', get_string('setdefault', 'basiclti'), $defaults);
        $mform->setDefault('lti_instructorchoicesendemailaddr', '0');
        $mform->disabledIf('lti_instructorchoicesendemailaddr', 'lti_sendemailaddr', 'neq', 2);

//-------------------------------------------------------------------------------
        // BLTI Extensions
        $mform->addElement('header', 'extensions', get_string('extensions', 'basiclti'));

        $defaults_accept=array();
        $defaults_accept[0] = get_string('donotaccept', 'basiclti');
        $defaults_accept[1] = get_string('accept', 'basiclti');

        $defaults_allow=array();
        $defaults_allow[0] = get_string('donotallow', 'basiclti');
        $defaults_allow[1] = get_string('allow', 'basiclti');

        // Add grading preferences fieldset where the tool is allowed to return grades
        $mform->addElement('select', 'lti_acceptgrades', get_string('acceptgrades', 'basiclti'), $options);
        $mform->setDefault('lti_acceptgrades', '0');
//        $mform->addHelpButton('lti_acceptgrades', 'acceptgrades', 'basiclti');

        $mform->addElement('select', 'lti_instructorchoiceacceptgrades', get_string('setdefault', 'basiclti'), $defaults_accept);
        $mform->setDefault('lti_instructorchoiceacceptgrades', '0');
        $mform->disabledIf('lti_instructorchoiceacceptgrades', 'lti_acceptgrades', 'neq', 2);

        // Add grading preferences fieldset where the tool is allowed to retrieve rosters
        $mform->addElement('select', 'lti_allowroster', get_string('allowroster', 'basiclti'), $options);
        $mform->setDefault('lti_allowroster', '0');
//        $mform->addHelpButton('lti_allowroster', 'allowroster', 'basiclti');

        $mform->addElement('select', 'lti_instructorchoiceallowroster', get_string('setdefault', 'basiclti'), $defaults_allow);
        $mform->setDefault('lti_instructorchoiceallowroster', '0');
        $mform->disabledIf('lti_instructorchoiceallowroster', 'lti_allowroster', 'neq', 2);

        // Add grading preferences fieldset where the tool is allowed to update settings
        $mform->addElement('select', 'lti_allowsetting', get_string('allowsetting', 'basiclti'), $options);
        $mform->setDefault('lti_allowsetting', '0');
//        $mform->addHelpButton('lti_allowsetting', 'allowsetting', 'basiclti');

        $mform->addElement('select', 'lti_instructorchoiceallowsetting', get_string('setdefault', 'basiclti'), $defaults_allow);
        $mform->setDefault('lti_instructorchoiceallowsetting', '0');
        $mform->disabledIf('lti_instructorchoiceallowsetting', 'lti_allowsetting', 'neq', 2);

//-------------------------------------------------------------------------------
        // Add custom parameters fieldset
        $mform->addElement('header', 'custom', get_string('custom', 'basiclti'));

        $mform->addElement('textarea', 'lti_customparameters', '', array('rows'=>15, 'cols'=>60));
        $mform->setType('lti_customparameters', PARAM_TEXT);

        $mform->addElement('select', 'lti_allowinstructorcustom', get_string('allowinstructorcustom', 'basiclti'), $defaults_allow);
        $mform->setDefault('lti_allowinstructorcustom', '0');

//-------------------------------------------------------------------------------
        // Add setup parameters fieldset
        $mform->addElement('header', 'setupoptions', get_string('setupoptions', 'basiclti'));

        // Adding option to change id that is placed in context_id
        $idoptions = array();
        $idoptions[0] = get_string('id', 'basiclti');
        $idoptions[1] = get_string('courseid', 'basiclti');

        $mform->addElement('select', 'lti_moodle_course_field', get_string('moodle_course_field', 'basiclti'), $idoptions);
        $mform->setDefault('lti_moodle_course_field', '0');

        // Added option to allow user to specify if this is a resource or activity type
        $classoptions = array();
        $classoptions[0] = get_string('activity', 'basiclti');
        $classoptions[1] = get_string('resource', 'basiclti');

        $mform->addElement('select', 'lti_module_class_type', get_string('module_class_type', 'basiclti'), $classoptions);
        $mform->setDefault('lti_module_class_type', '0');

//-------------------------------------------------------------------------------
        // Add organization parameters fieldset
        $mform->addElement('header', 'organization', get_string('organization', 'basiclti'));

        $mform->addElement('text', 'lti_organizationid', get_string('organizationid', 'basiclti'));
        $mform->setType('lti_organizationid', PARAM_TEXT);
//        $mform->addHelpButton('lti_organizationid', 'organizationid', 'basiclti');

        $mform->addElement('text', 'lti_organizationurl', get_string('organizationurl', 'basiclti'));
        $mform->setType('lti_organizationurl', PARAM_TEXT);
//        $mform->addHelpButton('lti_organizationurl', 'organizationurl', 'basiclti');

        /* Suppress this for now - Chuck
        $mform->addElement('text', 'lti_organizationdescr', get_string('organizationdescr', 'basiclti'));
        $mform->setType('lti_organizationdescr', PARAM_TEXT);
        $mform->addHelpButton('lti_organizationdescr', 'organizationdescr', 'basiclti');
        */

//-------------------------------------------------------------------------------
        // Add launch parameters fieldset
        $mform->addElement('header', 'launchoptions', get_string('launchoptions', 'basiclti'));

        $launchoptions=array();
        $launchoptions[0] = get_string('launch_in_moodle', 'basiclti');
        $launchoptions[1] = get_string('launch_in_popup', 'basiclti');

        $mform->addElement('select', 'lti_launchinpopup', get_string('launchinpopup', 'basiclti'), $launchoptions);
        $mform->setDefault('lti_launchinpopup', '0');
//        $mform->addHelpButton('lti_launchinpopup', 'launchinpopup', 'basiclti');

//-------------------------------------------------------------------------------
        // Add a hidden element to signal a tool fixing operation after a problematic backup - restore process
        $mform->addElement('hidden', 'lti_fix');

//-------------------------------------------------------------------------------
        // Add standard buttons, common to all modules
        $this->add_action_buttons();

    }
}
