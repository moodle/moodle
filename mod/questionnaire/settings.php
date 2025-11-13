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
 * Setting page for questionaire module
 *
 * @package    mod_questionnaire
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  2016 onward Mike Churchward (mike.churchward@poetgroup.org)
 * @author     Mike Churchward
 */

defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot . '/mod/questionnaire/locallib.php');

if ($ADMIN->fulltree) {
    $options = array(0 => get_string('no'), 1 => get_string('yes'));
    $str = get_string('configusergraphlong', 'questionnaire');
    $settings->add(new admin_setting_configselect('questionnaire/usergraph',
                                    get_string('configusergraph', 'questionnaire'),
                                    $str, 0, $options));
    $settings->add(new admin_setting_configtext('questionnaire/maxsections',
                                    get_string('configmaxsections', 'questionnaire'),
                                    '', 10, PARAM_INT));
    $choices = array(
        'response' => get_string('response', 'questionnaire'),
        'submitted' => get_string('submitted', 'questionnaire'),
        'institution' => get_string('institution'),
        'department' => get_string('department'),
        'course' => get_string('course'),
        'group' => get_string('group'),
        'id' => get_string('id', 'questionnaire'),
        'useridnumber' => get_string('useridnumber', 'questionnaire'),
        'fullname' => get_string('fullname'),
        'username' => get_string('username'),
        'useridentityfields' => get_string('showuseridentity', 'admin')
    );

    $settings->add(new admin_setting_configmultiselect('questionnaire/downloadoptions',
            get_string('textdownloadoptions', 'questionnaire'), '', array_keys($choices), $choices));

    $settings->add(new admin_setting_configcheckbox('questionnaire/allowemailreporting',
        get_string('configemailreporting', 'questionnaire'), get_string('configemailreportinglong', 'questionnaire'), 0));

    // Delete old responses after. The default value is 24 months.
    $options = [
            '0' => new lang_string('disabled', 'questionnaire'),
            '1' => new lang_string('enabled', 'questionnaire'),
    ];
    $name = get_string('autodeletereponse', 'questionnaire');
    $desc = get_string('autodeletereponse_desc', 'questionnaire');
    $setting = new admin_setting_configselect('questionnaire/autodeleteresponse', $name, $desc, 0, $options);
    $settings->add($setting);

    $options = questionnaire_create_remove_options();
    $settings->add(new admin_setting_configselect('questionnaire/removeoldresponses',
            get_string('removeoldresponsesafter', 'questionnaire'),
            get_string('configremoveoldresponses', 'questionnaire'), 0, $options));
}
