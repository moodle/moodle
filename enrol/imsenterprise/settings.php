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
 * IMS Enterprise enrolments plugin settings and presets.
 *
 * @package    enrol
 * @subpackage imsenterprise
 * @copyright  2010 Eugene Venter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    require_once('locallib.php');

    $settings->add(new admin_setting_heading('enrol_imsenterprise_settings', '', get_string('pluginname_desc', 'enrol_imsenterprise')));

    //--- general settings -----------------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('enrol_imsenterprise_basicsettings', get_string('basicsettings', 'enrol_imsenterprise'), ''));

    $settings->add(new admin_setting_configtext('enrol_imsenterprise/imsfilelocation', get_string('location', 'enrol_imsenterprise'), '', ''));

    $settings->add(new admin_setting_configtext('enrol_imsenterprise/logtolocation', get_string('logtolocation', 'enrol_imsenterprise'), '', ''));

    $settings->add(new admin_setting_configcheckbox('enrol_imsenterprise/mailadmins', get_string('mailadmins', 'enrol_imsenterprise'), '', 0));

    //--- user data options ---------------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('enrol_imsenterprise_usersettings', get_string('usersettings', 'enrol_imsenterprise'), ''));

    $settings->add(new admin_setting_configcheckbox('enrol_imsenterprise/createnewusers', get_string('createnewusers', 'enrol_imsenterprise'), get_string('createnewusers_desc', 'enrol_imsenterprise'), 0));

    $settings->add(new admin_setting_configcheckbox('enrol_imsenterprise/imsdeleteusers', get_string('deleteusers', 'enrol_imsenterprise'), get_string('deleteusers_desc', 'enrol_imsenterprise'), 0));

    $settings->add(new admin_setting_configcheckbox('enrol_imsenterprise/fixcaseusernames', get_string('fixcaseusernames', 'enrol_imsenterprise'), '', 0));

    $settings->add(new admin_setting_configcheckbox('enrol_imsenterprise/fixcasepersonalnames', get_string('fixcasepersonalnames', 'enrol_imsenterprise'), '', 0));

    $settings->add(new admin_setting_configcheckbox('enrol_imsenterprise/imssourcedidfallback', get_string('sourcedidfallback', 'enrol_imsenterprise'), get_string('sourcedidfallback_desc', 'enrol_imsenterprise'), 0));

    $settings->add(new admin_setting_heading('enrol_imsenterprise_usersettings_roles', get_string('roles', 'enrol_imsenterprise'), get_string('imsrolesdescription', 'enrol_imsenterprise')));

    if (!during_initial_install()) {
        $coursecontext = get_context_instance(CONTEXT_COURSE, SITEID);
        $assignableroles = get_assignable_roles($coursecontext);
        $assignableroles = array('0' => get_string('ignore', 'enrol_imsenterprise')) + $assignableroles;
        $imsroles = new imsenterprise_roles();
        foreach ($imsroles->get_imsroles() as $imsrolenum => $imsrolename) {
            $settings->add(new admin_setting_configselect('enrol_imsenterprise/imsrolemap'.$imsrolenum, format_string('"'.$imsrolename.'" ('.$imsrolenum.')'), '', (int)$imsroles->determine_default_rolemapping($imsrolenum), $assignableroles));
        }
    }

    //--- course data options -------------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('enrol_imsenterprise_coursesettings', get_string('coursesettings', 'enrol_imsenterprise'), ''));

    $settings->add(new admin_setting_configtext('enrol_imsenterprise/truncatecoursecodes', get_string('truncatecoursecodes', 'enrol_imsenterprise'), get_string('truncatecoursecodes_desc', 'enrol_imsenterprise'), 0, PARAM_INT, 2));

    $settings->add(new admin_setting_configcheckbox('enrol_imsenterprise/createnewcourses', get_string('createnewcourses', 'enrol_imsenterprise'), get_string('createnewcourses_desc', 'enrol_imsenterprise'), 0));

    $settings->add(new admin_setting_configcheckbox('enrol_imsenterprise/createnewcategories', get_string('createnewcategories', 'enrol_imsenterprise'), get_string('createnewcategories_desc', 'enrol_imsenterprise'), 0));

    $settings->add(new admin_setting_configcheckbox('enrol_imsenterprise/imsunenrol', get_string('allowunenrol', 'enrol_imsenterprise'), get_string('allowunenrol_desc', 'enrol_imsenterprise'), 0));

    //--- miscellaneous -------------------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('enrol_imsenterprise_miscsettings', get_string('miscsettings', 'enrol_imsenterprise'), ''));

    $settings->add(new admin_setting_configtext('enrol_imsenterprise/imsrestricttarget', get_string('restricttarget', 'enrol_imsenterprise'), get_string('restricttarget_desc', 'enrol_imsenterprise'), ''));

    $settings->add(new admin_setting_configcheckbox('enrol_imsenterprise/imscapitafix', get_string('usecapitafix', 'enrol_imsenterprise'), get_string('usecapitafix_desc', 'enrol_imsenterprise'), 0));

    $importnowstring = get_string('aftersaving...', 'enrol_imsenterprise').' <a href="../enrol/imsenterprise/importnow.php">'.get_string('doitnow', 'enrol_imsenterprise').'</a>';
    $settings->add(new admin_setting_heading('enrol_imsenterprise_doitnowmessage', '', $importnowstring));
}
