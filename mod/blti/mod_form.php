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
 * @package blti
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
require_once($CFG->dirroot.'/mod/blti/locallib.php');

class mod_blti_mod_form extends moodleform_mod {

    function definition() {
        global $DB;

        $typename = optional_param('type', false, PARAM_ALPHA);

        if (empty($typename)) {
            //Updating instance
            if (!empty($this->_instance)) {
                $basiclti = $DB->get_record('blti', array('id' => $this->_instance));
                $this->typeid = $basiclti->typeid;

                $typeconfig = blti_get_config($basiclti);
                $this->typeconfig = $typeconfig;

            } else { // New not pre-configured instance
                $this->typeid = 0;
            }
        } else {
            // New pre-configured instance
            $basicltitype = $DB->get_record('blti_types', array('rawname' => $typename));
            $this->typeid = $basicltitype->id;

            $typeconfig = blti_get_type_config($this->typeid);
            $this->typeconfig = $typeconfig;
        }

        $mform =& $this->_form;
//-------------------------------------------------------------------------------
    /// Adding the "general" fieldset, where all the common settings are shown
        $mform->addElement('header', 'general', get_string('general', 'form'));
    /// Adding the standard "name" field
        $mform->addElement('text', 'name', get_string('basicltiname', 'blti'), array('size'=>'64'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
    /// Adding the optional "intro" and "introformat" pair of fields
        $this->add_intro_editor(false, get_string('basicltiintro', 'blti'));
        $mform->setAdvanced('introeditor');

        $mform->addElement('checkbox', 'showtitle', '&nbsp;', ' ' . get_string('display_name', 'blti'));
        $mform->setAdvanced('showtitle');
        
        $mform->addElement('checkbox', 'showdescription', '&nbsp;', ' ' . get_string('display_description', 'blti'));
        $mform->setAdvanced('showdescription');
        
        //Tool settings
        $mform->addElement('select', 'typeid', get_string('external_tool_type', 'blti'), blti_get_types_for_add_instance());
        //$mform->setDefault('typeid', '0');
        
        $mform->addElement('text', 'toolurl', get_string('launch_url', 'blti'), array('size'=>'64'));
        $mform->setType('toolurl', PARAM_TEXT);
        
        $mform->addElement('text', 'resourcekey', get_string('resourcekey', 'blti'));
        $mform->setType('resourcekey', PARAM_TEXT);
        $mform->setAdvanced('resourcekey');

        $mform->addElement('passwordunmask', 'password', get_string('password', 'blti'));
        $mform->setType('password', PARAM_TEXT);
        $mform->setAdvanced('password');
        
        $mform->addElement('textarea', 'instructorcustomparameters', get_string('custom', 'blti'), array('rows'=>4, 'cols'=>60));
        $mform->setType('instructorcustomparameters', PARAM_TEXT);
        $mform->setAdvanced('instructorcustomparameters');
        
        $launchoptions=array();
        $launchoptions[BLTI_LAUNCH_CONTAINER_DEFAULT] = get_string('default', 'blti');
        $launchoptions[BLTI_LAUNCH_CONTAINER_EMBED] = get_string('embed', 'blti');
        $launchoptions[BLTI_LAUNCH_CONTAINER_EMBED_NO_BLOCKS] = get_string('embed_no_blocks', 'blti');
        $launchoptions[BLTI_LAUNCH_CONTAINER_WINDOW] = get_string('new_window', 'blti');

        $mform->addElement('select', 'launchcontainer', get_string('launchinpopup', 'blti'), $launchoptions);

        $mform->setDefault('launchcontainer', BLTI_LAUNCH_CONTAINER_DEFAULT);
        
//-------------------------------------------------------------------------------
        //$mform->addElement('hidden', 'typeid', $this->typeid);
        //$mform->addElement('hidden', 'toolurl', $this->typeconfig['toolurl']);
        $mform->addElement('hidden', 'type', $typename);

//-------------------------------------------------------------------------------
        // Add privacy preferences fieldset where users choose whether to send their data
        $mform->addElement('header', 'privacy', get_string('privacy', 'blti'));

        $mform->addElement('checkbox', 'instructorchoicesendname', '&nbsp;', ' ' . get_string('share_name', 'blti'));
        $mform->setDefault('instructorchoicesendname', '1');
        
        $mform->addElement('checkbox', 'instructorchoicesendemailaddr', '&nbsp;', ' ' . get_string('share_email', 'blti'));
        $mform->setDefault('instructorchoicesendemailaddr', '1');
        
        $mform->addElement('checkbox', 'instructorchoiceacceptgrades', '&nbsp;', ' ' . get_string('accept_grades', 'blti'));
        $mform->setDefault('instructorchoiceacceptgrades', '1');
        
        $mform->addElement('checkbox', 'instructorchoiceallowroster', '&nbsp;', ' ' . get_string('share_roster', 'blti'));
        $mform->setDefault('instructorchoiceallowroster', '1');

//-------------------------------------------------------------------------------

/*        $debugoptions=array();
        $debugoptions[0] = get_string('debuglaunchoff', 'blti');
        $debugoptions[1] = get_string('debuglaunchon', 'blti');

        $mform->addElement('select', 'debuglaunch', get_string('debuglaunch', 'blti'), $debugoptions);

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
       /* $mform     =& $this->_form;
        $typeid      =& $mform->getElement('typeid');
        $typeidvalue = $mform->getElementValue('typeid');

        //Depending on the selection of the administrator
        //we don't want to have these appear as possible selections in the form but
        //we want the form to display them if they are set.
        if (!empty($typeidvalue)) {
            $typeconfig = blti_get_type_config($typeidvalue);

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
        }*/
    }

    /**
     * Function overwritten to change default values using
     * global configuration
     *
     * @param array $default_values passed by reference
     */
    function data_preprocessing(&$default_values) {
/*        global $CFG;
        $default_values['typeid'] = $this->typeid;

        if (!isset($default_values['toolurl'])) {
            if (isset($this->typeconfig['toolurl'])) {
                $default_values['toolurl'] = $this->typeconfig['toolurl'];
            } else if (isset($CFG->blti_toolurl)) {
                $default_values['toolurl'] = $CFG->blti_toolurl;
            }
        }

        if (!isset($default_values['resourcekey'])) {
            if (isset($this->typeconfig['resourcekey'])) {
                $default_values['resourcekey'] = $this->typeconfig['resourcekey'];
            } else if (isset($CFG->blti_resourcekey)) {
                $default_values['resourcekey'] = $CFG->blti_resourcekey;
            }
        }

        if (!isset($default_values['password'])) {
            if (isset($this->typeconfig['password'])) {
                $default_values['password'] = $this->typeconfig['password'];
            } else if (isset($CFG->blti_password)) {
                $default_values['password'] = $CFG->blti_password;
            }
        }

        if (!isset($default_values['preferheight'])) {
            if (isset($this->typeconfig['preferheight'])) {
                $default_values['preferheight'] = $this->typeconfig['preferheight'];
            } else if (isset($CFG->blti_preferheight)) {
                $default_values['preferheight'] = $CFG->blti_preferheight;
            }
        }

        if (!isset($default_values['sendname'])) {
            if (isset($this->typeconfig['sendname'])) {
                $default_values['sendname'] = $this->typeconfig['sendname'];
            } else if (isset($CFG->blti_sendname)) {
                $default_values['sendname'] = $CFG->blti_sendname;
            }
        }

        if (!isset($default_values['instructorchoicesendname'])) {
            if (isset($this->typeconfig['instructorchoicesendname'])) {
                $default_values['instructorchoicesendname'] = $this->typeconfig['instructorchoicesendname'];
            } else {
                if ($this->typeconfig['sendname'] == 2) {
                    $default_values['instructorchoicesendname'] = $CFG->blti_instructorchoicesendname;
                } else {
                      $default_values['instructorchoicesendname'] = $this->typeconfig['sendname'];
                }
            }
        }

        if (!isset($default_values['sendemailaddr'])) {
            if (isset($this->typeconfig['sendemailaddr'])) {
                $default_values['sendemailaddr'] = $this->typeconfig['sendemailaddr'];
            } else if (isset($CFG->blti_sendemailaddr)) {
                $default_values['sendemailaddr'] = $CFG->blti_sendemailaddr;
            }
        }

        if (!isset($default_values['instructorchoicesendemailaddr'])) {
            if (isset($this->typeconfig['instructorchoicesendemailaddr'])) {
                $default_values['instructorchoicesendemailaddr'] = $this->typeconfig['instructorchoicesendemailaddr'];
            } else {
                if ($this->typeconfig['sendemailaddr'] == 2) {
                    $default_values['instructorchoicesendemailaddr'] = $CFG->blti_instructorchoicesendemailaddr;
                } else {
                      $default_values['instructorchoicesendemailaddr'] = $this->typeconfig['sendemailaddr'];
                }
            }
        }

        if (!isset($default_values['acceptgrades'])) {
            if (isset($this->typeconfig['acceptgrades'])) {
                $default_values['acceptgrades'] = $this->typeconfig['acceptgrades'];
            } else if (isset($CFG->blti_acceptgrades)) {
                $default_values['acceptgrades'] = $CFG->blti_acceptgrades;
            }
        }

        if (!isset($default_values['instructorchoiceacceptgrades'])) {
            if (isset($this->typeconfig['instructorchoiceacceptgrades'])) {
                $default_values['instructorchoiceacceptgrades'] = $this->typeconfig['instructorchoiceacceptgrades'];
            } else {
                if ($this->typeconfig['acceptgrades'] == 2) {
                    $default_values['instructorchoiceacceptgrades'] = $CFG->blti_instructorchoiceacceptgrades;
                } else {
                      $default_values['instructorchoiceacceptgrades'] = $this->typeconfig['acceptgrades'];
                }
            }
        }

        if (!isset($default_values['allowroster'])) {
            if (isset($this->typeconfig['allowroster'])) {
                $default_values['allowroster'] = $this->typeconfig['allowroster'];
            } else if (isset($CFG->blti_allowroster)) {
                $default_values['allowroster'] = $CFG->blti_allowroster;
            }
        }

        if (!isset($default_values['instructorchoiceallowroster'])) {
            if (isset($this->typeconfig['instructorchoiceallowroster'])) {
                $default_values['instructorchoiceallowroster'] = $this->typeconfig['instructorchoiceallowroster'];
            } else {
                if ($this->typeconfig['allowroster'] == 2) {
                    $default_values['instructorchoiceallowroster'] = $CFG->blti_instructorchoiceallowroster;
                } else {
                      $default_values['instructorchoiceallowroster'] = $this->typeconfig['allowroster'];
                }
            }
        }

        if (!isset($default_values['allowsetting'])) {
            if (isset($this->typeconfig['allowsetting'])) {
                $default_values['allowsetting'] = $this->typeconfig['allowsetting'];
            } else if (isset($CFG->blti_allowsetting)) {
                $default_values['allowsetting'] = $CFG->blti_allowsetting;
            }
        }

        if (!isset($default_values['instructorchoiceallowsetting'])) {
            if (isset($this->typeconfig['instructorchoiceallowsetting'])) {
                $default_values['instructorchoiceallowsetting'] = $this->typeconfig['instructorchoiceallowsetting'];
            } else {
                if ($this->typeconfig['allowsetting'] == 2) {
                    $default_values['instructorchoiceallowsetting'] = $CFG->blti_instructorchoiceallowsetting;
                } else {
                      $default_values['instructorchoiceallowsetting'] = $this->typeconfig['allowsetting'];
                }
            }
        }

        if (!isset($default_values['customparameters'])) {
            if (isset($this->typeconfig['customparameters'])) {
                $default_values['customparameters'] = $this->typeconfig['customparameters'];
            } else if (isset($CFG->blti_customparameters)) {
                $default_values['customparameters'] = $CFG->blti_customparameters;
            }
        }

        if (!isset($default_values['allowinstructorcustom'])) {
            if (isset($this->typeconfig['allowinstructorcustom'])) {
                $default_values['allowinstructorcustom'] = $this->typeconfig['allowinstructorcustom'];
            } else if (isset($CFG->blti_allowinstructorcustom)) {
                $default_values['allowinstructorcustom'] = $CFG->blti_allowinstructorcustom;
            }
        }

        if (!isset($default_values['organizationid'])) {
            if (isset($this->typeconfig['organizationid'])) {
                $default_values['organizationid'] = $this->typeconfig['organizationid'];
            } else if (isset($CFG->blti_organizationid)) {
                $default_values['organizationid'] = $CFG->blti_organizationid;
            }
        }

        if (!isset($default_values['organizationurl'])) {
            if (isset($this->typeconfig['organizationurl'])) {
                $default_values['organizationurl'] = $this->typeconfig['organizationurl'];
            } else if (isset($CFG->blti_organizationurl)) {
                $default_values['organizationurl'] = $CFG->blti_organizationurl;
            }
        }

        if (!isset($default_values['organizationdescr'])) {
            if (isset($this->typeconfig['organizationdescr'])) {
                $default_values['organizationdescr'] = $this->typeconfig['organizationdescr'];
            } else if (isset($CFG->blti_organizationdescr)) {
                $default_values['organizationdescr'] = $CFG->blti_organizationdescr;
            }
        }

        if (!isset($default_values['launchinpopup'])) {
            if (isset($this->typeconfig['launchinpopup'])) {
                $default_values['launchinpopup'] = $this->typeconfig['launchinpopup'];
            } else if (isset($CFG->blti_launchinpopup)) {
                $default_values['launchinpopup'] = $CFG->blti_launchinpopup;
            }
        }
*/
    }
}

