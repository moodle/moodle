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
 * This file defines the main basiclti configuration form
 *
 * @package basiclti
 * @copyright 2009 Marc Alier, Jordi Piguillem, Nikolas Galanis
 *  marc.alier@upc.edu
 * @copyright 2009 Universitat Politecnica de Catalunya http://www.upc.edu
 *
 * @author Marc Alier
 * @author Jordi Piguillem
 * @author Nikolas Galanis
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/basiclti/locallib.php');

class mod_basiclti_mod_form extends moodleform_mod {

    function definition() {
        global $DB;

        $typename = optional_param('type', false, PARAM_ALPHA);

        if (empty($typename)) {
            //Updating instance
            if (!empty($this->_instance)) {
                $basiclti = $DB->get_record('basiclti', array('id' => $this->_instance));
                $this->typeid = $basiclti->typeid;

                $typeconfig = basiclti_get_config($basiclti);
                $this->typeconfig = $typeconfig;

            } else { // New not pre-configured instance
                $this->typeid = 0;
            }
        } else {
            // New pre-configured instance
            $basicltitype = $DB->get_record('basiclti_types', array('rawname' => $typename));
            $this->typeid = $basicltitype->id;

            $typeconfig = basiclti_get_type_config($this->typeid);
            $this->typeconfig = $typeconfig;
        }

        $mform =& $this->_form;
//-------------------------------------------------------------------------------
    /// Adding the "general" fieldset, where all the common settings are shown
        $mform->addElement('header', 'general', get_string('general', 'form'));
    /// Adding the standard "name" field
        $mform->addElement('text', 'name', get_string('basicltiname', 'basiclti'), array('size'=>'64'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
    /// Adding the optional "intro" and "introformat" pair of fields
        $this->add_intro_editor(true, get_string('basicltiintro', 'basiclti'));

//-------------------------------------------------------------------------------
        $mform->addElement('hidden', 'typeid', $this->typeid);
        $mform->addElement('hidden', 'toolurl', $this->typeconfig['toolurl']);
        $mform->addElement('hidden', 'type', $typename);

//-------------------------------------------------------------------------------
        // Add privacy preferences fieldset where users choose whether to send their data
        $mform->addElement('header', 'privacy', get_string('privacy', 'basiclti'));

        $privacyoptions=array();
        $privacyoptions[0] = get_string('donot', 'basiclti');
        $privacyoptions[1] = get_string('send', 'basiclti');

        $mform->addElement('select', 'instructorchoicesendname', get_string('sendname', 'basiclti'), $privacyoptions);

        if (isset($this->typeconfig['instructorchoicesendname'])) {
            if ($this->typeconfig['instructorchoicesendname'] == 0) {
                $mform->setDefault('instructorchoicesendname', '0');
            } else if ($this->typeconfig['instructorchoicesendname'] == 1) {
                $mform->setDefault('instructorchoicesendname', '1');
            }
        }
//        $mform->addHelpButton('instructorchoicesendname', 'sendname', 'basiclti');

          $mform->addElement('select', 'instructorchoicesendemailaddr', get_string('sendemailaddr', 'basiclti'), $privacyoptions);

        if (isset($this->typeconfig['instructorchoicesendemailaddr'])) {
            if ($this->typeconfig['instructorchoicesendemailaddr'] == 0) {
                $mform->setDefault('instructorchoicesendemailaddr', '0');
            } else if ($this->typeconfig['instructorchoicesendemailaddr'] == 1) {
                $mform->setDefault('instructorchoicesendemailaddr', '1');
            }
        }
    //            $mform->addHelpButton('instructorchoicesendemailaddr', 'sendemailaddr', 'basiclti');

//-------------------------------------------------------------------------------
        // Add grading preferences fieldset where the instructor determines whether to accept grades
        $mform->addElement('header', 'extensions', get_string('extensions', 'basiclti'));

        $extensionoptions=array();
        $extensionoptions[0] = get_string('donotaccept', 'basiclti');
        $extensionoptions[1] = get_string('accept', 'basiclti');

        $mform->addElement('select', 'instructorchoiceacceptgrades', get_string('acceptgrades', 'basiclti'), $extensionoptions);
        if (isset($this->typeconfig['instructorchoiceacceptgrades'])) {
            if ($this->typeconfig['instructorchoiceacceptgrades'] == 0) {
                $mform->setDefault('instructorchoiceacceptgrades', '0');
            } else if ($this->typeconfig['instructorchoiceacceptgrades'] == 1) {
                $mform->setDefault('instructorchoiceacceptgrades', '1');
            }
        }
    //        $mform->addHelpButton('instructorchoiceacceptgrades', 'acceptgrades', 'basiclti');

        $extensionoptions=array();
        $extensionoptions[0] = get_string('donotallow', 'basiclti');
        $extensionoptions[1] = get_string('allow', 'basiclti');

        $mform->addElement('select', 'instructorchoiceallowroster', get_string('allowroster', 'basiclti'), $extensionoptions);
        if (isset($this->typeconfig['instructorchoiceallowroster'])) {
            if ($this->typeconfig['instructorchoiceallowroster'] == 0) {
                $mform->setDefault('instructorchoiceallowroster', '0');
            } else if ($this->typeconfig['instructorchoiceallowroster'] == 1) {
                $mform->setDefault('instructorchoiceallowroster', '1');
            }
        }
    //        $mform->addHelpButton('instructorchoiceallowroster', 'allowroster', 'basiclti');
        $mform->setAdvanced('instructorchoiceallowroster');

        $mform->addElement('select', 'instructorchoiceallowsetting', get_string('allowsetting', 'basiclti'), $extensionoptions);

        if (isset($this->typeconfig['instructorchoiceallowsetting'])) {
            if ($this->typeconfig['instructorchoiceallowsetting'] == 0) {
                $mform->setDefault('instructorchoiceallowsetting', '0');
            } else if ($this->typeconfig['instructorchoiceallowsetting'] == 1) {
                $mform->setDefault('instructorchoiceallowsetting', '1');
            }
        }
//        $mform->addHelpButton('instructorchoiceallowsetting', 'allowsetting', 'basiclti');
        $mform->setAdvanced('instructorchoiceallowsetting');

//-------------------------------------------------------------------------------
        if (isset($this->typeconfig['allowinstructorcustom'])) {
            if ($this->typeconfig['allowinstructorcustom'] == 1) {
                // Add custom parameters fieldset
                $mform->addElement('header', 'launchoptions', get_string('custominstr', 'basiclti'));

                $mform->addElement('textarea', 'instructorcustomparameters', '', array('rows'=>15, 'cols'=>60));
                $mform->setType('instructorcustomparameters', PARAM_TEXT);
                $mform->setAdvanced('instructorcustomparameters');
            }
        }

//-------------------------------------------------------------------------------
        // Add launch parameters fieldset
        $mform->addElement('header', 'launchoptions', get_string('launchoptions', 'basiclti'));

        // Size parameters
        $mform->addElement('text', 'preferheight', get_string('preferheight', 'basiclti'));
        if (isset($this->typeconfig['preferheight'])) {
            $mform->setDefault('preferheight', $this->typeconfig['preferheight']);
        }

        $launchoptions=array();
        $launchoptions[0] = get_string('launch_in_moodle', 'basiclti');
        $launchoptions[1] = get_string('launch_in_popup', 'basiclti');

        $mform->addElement('select', 'launchinpopup', get_string('launchinpopup', 'basiclti'), $launchoptions);

        if (isset($this->typeconfig['launchinpopup'])) {
            if ($this->typeconfig['launchinpopup'] == 0) {
                $mform->setDefault('launchinpopup', '0');
            } else if ($this->typeconfig['launchinpopup'] == 1) {
                $mform->setDefault('launchinpopup', '1');
            }
        }

        $debugoptions=array();
        $debugoptions[0] = get_string('debuglaunchoff', 'basiclti');
        $debugoptions[1] = get_string('debuglaunchon', 'basiclti');

        $mform->addElement('select', 'debuglaunch', get_string('debuglaunch', 'basiclti'), $debugoptions);

        if (isset($this->typeconfig['debuglaunch'])) {
            if ($this->typeconfig['debuglaunch'] == 0) {
                $mform->setDefault('debuglaunch', '0');
            } else if ($this->typeconfig['debuglaunch'] == 1) {
                $mform->setDefault('debuglaunch', '1');
            }
        }

//-------------------------------------------------------------------------------
        // Organization parameters
        if (isset($this->typeconfig['organizationid'])) {
            $mform->addElement('hidden', 'organizationid', $this->typeconfig['organizationid']);
        }
        if (isset($this->typeconfig['organizationurl'])) {
            $mform->addElement('hidden', 'organizationurl', $this->typeconfig['organizationurl']);
        }
//       $mform->addElement('hidden', 'organizationdescr', $this->typeconfig['organizationdescr']);

//-------------------------------------------------------------------------------
        // add standard elements, common to all modules
        $this->standard_coursemodule_elements();
//-------------------------------------------------------------------------------
        // add standard buttons, common to all modules
        $this->add_action_buttons();
    }

    /**
     * Make fields editable or non-editable depending on the administrator choices
     * @see moodleform_mod::definition_after_data()
     */
    function definition_after_data() {
        parent::definition_after_data();
        $mform     =& $this->_form;
        $typeid      =& $mform->getElement('typeid');
        $typeidvalue = $mform->getElementValue('typeid');

        //Depending on the selection of the administrator
        //we don't want to have these appear as possible selections in the form but
        //we want the form to display them if they are set.
        if (!empty($typeidvalue)) {
            $typeconfig = basiclti_get_type_config($typeidvalue);

            if ($typeconfig["sendname"] != 2) {
                $field =& $mform->getElement('instructorchoicesendname');
                $mform->setDefault('instructorchoicesendname', $typeconfig["sendname"]);
                $field->freeze();
                $field->setPersistantFreeze(true);
            }
            if ($typeconfig["sendemailaddr"] != 2) {
                $field =& $mform->getElement('instructorchoicesendemailaddr');
                $mform->setDefault('instructorchoicesendemailaddr', $typeconfig["sendemailaddr"]);
                $field->freeze();
                $field->setPersistantFreeze(true);
            }
            if ($typeconfig["acceptgrades"] != 2) {
                $field =& $mform->getElement('instructorchoiceacceptgrades');
                $mform->setDefault('instructorchoiceacceptgrades', $typeconfig["acceptgrades"]);
                $field->freeze();
                $field->setPersistantFreeze(true);
            }
            if ($typeconfig["allowroster"] != 2) {
                $field =& $mform->getElement('instructorchoiceallowroster');
                $mform->setDefault('instructorchoiceallowroster', $typeconfig["allowroster"]);
                $field->freeze();
                $field->setPersistantFreeze(true);
            }
            if ($typeconfig["allowsetting"] != 2) {
                $field =& $mform->getElement('instructorchoiceallowsetting');
                $mform->setDefault('instructorchoiceallowsetting', $typeconfig["allowsetting"]);
                $field->freeze();
                $field->setPersistantFreeze(true);
            }
        }
    }

    /**
     * Function overwritten to change default values using
     * global configuration
     *
     * @param array $default_values passed by reference
     */
    function data_preprocessing(&$default_values) {
        global $CFG;
        $default_values['typeid'] = $this->typeid;

        if (!isset($default_values['toolurl'])) {
            if (isset($this->typeconfig['toolurl'])) {
                $default_values['toolurl'] = $this->typeconfig['toolurl'];
            } else if (isset($CFG->basiclti_toolurl)) {
                $default_values['toolurl'] = $CFG->basiclti_toolurl;
            }
        }

        if (!isset($default_values['resourcekey'])) {
            if (isset($this->typeconfig['resourcekey'])) {
                $default_values['resourcekey'] = $this->typeconfig['resourcekey'];
            } else if (isset($CFG->basiclti_resourcekey)) {
                $default_values['resourcekey'] = $CFG->basiclti_resourcekey;
            }
        }

        if (!isset($default_values['password'])) {
            if (isset($this->typeconfig['password'])) {
                $default_values['password'] = $this->typeconfig['password'];
            } else if (isset($CFG->basiclti_password)) {
                $default_values['password'] = $CFG->basiclti_password;
            }
        }

        if (!isset($default_values['preferheight'])) {
            if (isset($this->typeconfig['preferheight'])) {
                $default_values['preferheight'] = $this->typeconfig['preferheight'];
            } else if (isset($CFG->basiclti_preferheight)) {
                $default_values['preferheight'] = $CFG->basiclti_preferheight;
            }
        }

        if (!isset($default_values['sendname'])) {
            if (isset($this->typeconfig['sendname'])) {
                $default_values['sendname'] = $this->typeconfig['sendname'];
            } else if (isset($CFG->basiclti_sendname)) {
                $default_values['sendname'] = $CFG->basiclti_sendname;
            }
        }

        if (!isset($default_values['instructorchoicesendname'])) {
            if (isset($this->typeconfig['instructorchoicesendname'])) {
                $default_values['instructorchoicesendname'] = $this->typeconfig['instructorchoicesendname'];
            } else {
                if ($this->typeconfig['sendname'] == 2) {
                    $default_values['instructorchoicesendname'] = $CFG->basiclti_instructorchoicesendname;
                } else {
                      $default_values['instructorchoicesendname'] = $this->typeconfig['sendname'];
                }
            }
        }

        if (!isset($default_values['sendemailaddr'])) {
            if (isset($this->typeconfig['sendemailaddr'])) {
                $default_values['sendemailaddr'] = $this->typeconfig['sendemailaddr'];
            } else if (isset($CFG->basiclti_sendemailaddr)) {
                $default_values['sendemailaddr'] = $CFG->basiclti_sendemailaddr;
            }
        }

        if (!isset($default_values['instructorchoicesendemailaddr'])) {
            if (isset($this->typeconfig['instructorchoicesendemailaddr'])) {
                $default_values['instructorchoicesendemailaddr'] = $this->typeconfig['instructorchoicesendemailaddr'];
            } else {
                if ($this->typeconfig['sendemailaddr'] == 2) {
                    $default_values['instructorchoicesendemailaddr'] = $CFG->basiclti_instructorchoicesendemailaddr;
                } else {
                      $default_values['instructorchoicesendemailaddr'] = $this->typeconfig['sendemailaddr'];
                }
            }
        }

        if (!isset($default_values['acceptgrades'])) {
            if (isset($this->typeconfig['acceptgrades'])) {
                $default_values['acceptgrades'] = $this->typeconfig['acceptgrades'];
            } else if (isset($CFG->basiclti_acceptgrades)) {
                $default_values['acceptgrades'] = $CFG->basiclti_acceptgrades;
            }
        }

        if (!isset($default_values['instructorchoiceacceptgrades'])) {
            if (isset($this->typeconfig['instructorchoiceacceptgrades'])) {
                $default_values['instructorchoiceacceptgrades'] = $this->typeconfig['instructorchoiceacceptgrades'];
            } else {
                if ($this->typeconfig['acceptgrades'] == 2) {
                    $default_values['instructorchoiceacceptgrades'] = $CFG->basiclti_instructorchoiceacceptgrades;
                } else {
                      $default_values['instructorchoiceacceptgrades'] = $this->typeconfig['acceptgrades'];
                }
            }
        }

        if (!isset($default_values['allowroster'])) {
            if (isset($this->typeconfig['allowroster'])) {
                $default_values['allowroster'] = $this->typeconfig['allowroster'];
            } else if (isset($CFG->basiclti_allowroster)) {
                $default_values['allowroster'] = $CFG->basiclti_allowroster;
            }
        }

        if (!isset($default_values['instructorchoiceallowroster'])) {
            if (isset($this->typeconfig['instructorchoiceallowroster'])) {
                $default_values['instructorchoiceallowroster'] = $this->typeconfig['instructorchoiceallowroster'];
            } else {
                if ($this->typeconfig['allowroster'] == 2) {
                    $default_values['instructorchoiceallowroster'] = $CFG->basiclti_instructorchoiceallowroster;
                } else {
                      $default_values['instructorchoiceallowroster'] = $this->typeconfig['allowroster'];
                }
            }
        }

        if (!isset($default_values['allowsetting'])) {
            if (isset($this->typeconfig['allowsetting'])) {
                $default_values['allowsetting'] = $this->typeconfig['allowsetting'];
            } else if (isset($CFG->basiclti_allowsetting)) {
                $default_values['allowsetting'] = $CFG->basiclti_allowsetting;
            }
        }

        if (!isset($default_values['instructorchoiceallowsetting'])) {
            if (isset($this->typeconfig['instructorchoiceallowsetting'])) {
                $default_values['instructorchoiceallowsetting'] = $this->typeconfig['instructorchoiceallowsetting'];
            } else {
                if ($this->typeconfig['allowsetting'] == 2) {
                    $default_values['instructorchoiceallowsetting'] = $CFG->basiclti_instructorchoiceallowsetting;
                } else {
                      $default_values['instructorchoiceallowsetting'] = $this->typeconfig['allowsetting'];
                }
            }
        }

        if (!isset($default_values['customparameters'])) {
            if (isset($this->typeconfig['customparameters'])) {
                $default_values['customparameters'] = $this->typeconfig['customparameters'];
            } else if (isset($CFG->basiclti_customparameters)) {
                $default_values['customparameters'] = $CFG->basiclti_customparameters;
            }
        }

        if (!isset($default_values['allowinstructorcustom'])) {
            if (isset($this->typeconfig['allowinstructorcustom'])) {
                $default_values['allowinstructorcustom'] = $this->typeconfig['allowinstructorcustom'];
            } else if (isset($CFG->basiclti_allowinstructorcustom)) {
                $default_values['allowinstructorcustom'] = $CFG->basiclti_allowinstructorcustom;
            }
        }

        if (!isset($default_values['organizationid'])) {
            if (isset($this->typeconfig['organizationid'])) {
                $default_values['organizationid'] = $this->typeconfig['organizationid'];
            } else if (isset($CFG->basiclti_organizationid)) {
                $default_values['organizationid'] = $CFG->basiclti_organizationid;
            }
        }

        if (!isset($default_values['organizationurl'])) {
            if (isset($this->typeconfig['organizationurl'])) {
                $default_values['organizationurl'] = $this->typeconfig['organizationurl'];
            } else if (isset($CFG->basiclti_organizationurl)) {
                $default_values['organizationurl'] = $CFG->basiclti_organizationurl;
            }
        }

        if (!isset($default_values['organizationdescr'])) {
            if (isset($this->typeconfig['organizationdescr'])) {
                $default_values['organizationdescr'] = $this->typeconfig['organizationdescr'];
            } else if (isset($CFG->basiclti_organizationdescr)) {
                $default_values['organizationdescr'] = $CFG->basiclti_organizationdescr;
            }
        }

        if (!isset($default_values['launchinpopup'])) {
            if (isset($this->typeconfig['launchinpopup'])) {
                $default_values['launchinpopup'] = $this->typeconfig['launchinpopup'];
            } else if (isset($CFG->basiclti_launchinpopup)) {
                $default_values['launchinpopup'] = $CFG->basiclti_launchinpopup;
            }
        }

    }
}

