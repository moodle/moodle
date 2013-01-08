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
 * This file adds the settings pages to the navigation menu
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/mod/assign/adminlib.php');

$ADMIN->add('modules', new admin_category('assignmentplugins',
                new lang_string('assignmentplugins', 'assign'), $module->is_enabled() === false));
$ADMIN->add('assignmentplugins', new admin_category('assignsubmissionplugins',
                new lang_string('submissionplugins', 'assign'), $module->is_enabled() === false));
$ADMIN->add('assignsubmissionplugins', new assign_admin_page_manage_assign_plugins('assignsubmission'));
$ADMIN->add('assignmentplugins', new admin_category('assignfeedbackplugins',
                new lang_string('feedbackplugins', 'assign'), $module->is_enabled() === false));
$ADMIN->add('assignfeedbackplugins', new assign_admin_page_manage_assign_plugins('assignfeedback'));


assign_plugin_manager::add_admin_assign_plugin_settings('assignsubmission', $ADMIN, $settings, $module);
assign_plugin_manager::add_admin_assign_plugin_settings('assignfeedback', $ADMIN, $settings, $module);

if ($ADMIN->fulltree) {
    $menu = array();
    foreach (get_plugin_list('assignfeedback') as $type => $notused) {
        $visible = !get_config('assignfeedback_' . $type, 'disabled');
        if ($visible) {
            $menu['assignfeedback_' . $type] = new lang_string('pluginname', 'assignfeedback_' . $type);
        }
    }

    // The default here is feedback_comments (if it exists).
    $name = new lang_string('feedbackplugin', 'mod_assign');
    $description = new lang_string('feedbackpluginforgradebook', 'mod_assign');
    $settings->add(new admin_setting_configselect('assign/feedback_plugin_for_gradebook',
                                                  $name,
                                                  $description,
                                                  'assignfeedback_comments',
                                                  $menu));

    $name = new lang_string('showrecentsubmissions', 'mod_assign');
    $description = new lang_string('configshowrecentsubmissions', 'mod_assign');
    $settings->add(new admin_setting_configcheckbox('assign/showrecentsubmissions',
                                                    $name,
                                                    $description,
                                                    0));

    $name = new lang_string('sendsubmissionreceipts', 'mod_assign');
    $description = new lang_string('sendsubmissionreceipts_help', 'mod_assign');
    $settings->add(new admin_setting_configcheckbox('assign/submissionreceipts',
                                                    $name,
                                                    $description,
                                                    1));

    $name = new lang_string('submissionstatement', 'mod_assign');
    $description = new lang_string('submissionstatement_help', 'mod_assign');
    $default = get_string('submissionstatementdefault', 'mod_assign');
    $settings->add(new admin_setting_configtextarea('assign/submissionstatement',
                                                    $name,
                                                    $description,
                                                    $default));

    $name = new lang_string('requiresubmissionstatement', 'mod_assign');
    $description = new lang_string('requiresubmissionstatement_help', 'mod_assign');
    $settings->add(new admin_setting_configcheckbox('assign/requiresubmissionstatement',
                                                    $name,
                                                    $description,
                                                    0));

}
