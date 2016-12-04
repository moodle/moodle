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
 * Define all the backup steps that will be used by the backup_assign_activity_task
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/assign/locallib.php');

/**
 * Define the complete choice structure for backup, with file and id annotations
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_assign_activity_structure_step extends backup_activity_structure_step {

    /**
     * Annotate files from plugin configuration
     * @param backup_nested_element $assign the backup structure of the activity
     * @param string $subtype the plugin type to handle
     * @return void
     */
    protected function annotate_plugin_config_files(backup_nested_element $assign, $subtype) {
        $dummyassign = new assign(null, null, null);
        $plugins = $dummyassign->load_plugins($subtype);
        foreach ($plugins as $plugin) {
            $component = $plugin->get_subtype() . '_' . $plugin->get_type();
            $areas = $plugin->get_config_file_areas();
            foreach ($areas as $area) {
                $assign->annotate_files($component, $area, null);
            }
        }
    }

    /**
     * Define the structure for the assign activity
     * @return void
     */
    protected function define_structure() {

        // To know if we are including userinfo.
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated.
        $assign = new backup_nested_element('assign', array('id'),
                                            array('name',
                                                  'intro',
                                                  'introformat',
                                                  'alwaysshowdescription',
                                                  'submissiondrafts',
                                                  'sendnotifications',
                                                  'sendlatenotifications',
                                                  'sendstudentnotifications',
                                                  'duedate',
                                                  'cutoffdate',
                                                  'allowsubmissionsfromdate',
                                                  'grade',
                                                  'timemodified',
                                                  'completionsubmit',
                                                  'requiresubmissionstatement',
                                                  'teamsubmission',
                                                  'requireallteammemberssubmit',
                                                  'teamsubmissiongroupingid',
                                                  'blindmarking',
                                                  'revealidentities',
                                                  'attemptreopenmethod',
                                                  'maxattempts',
                                                  'markingworkflow',
                                                  'markingallocation',
                                                  'preventsubmissionnotingroup'));

        $userflags = new backup_nested_element('userflags');

        $userflag = new backup_nested_element('userflag', array('id'),
                                                array('userid',
                                                      'assignment',
                                                      'mailed',
                                                      'locked',
                                                      'extensionduedate',
                                                      'workflowstate',
                                                      'allocatedmarker'));

        $submissions = new backup_nested_element('submissions');

        $submission = new backup_nested_element('submission', array('id'),
                                                array('userid',
                                                      'timecreated',
                                                      'timemodified',
                                                      'status',
                                                      'groupid',
                                                      'attemptnumber',
                                                      'latest'));

        $grades = new backup_nested_element('grades');

        $grade = new backup_nested_element('grade', array('id'),
                                           array('userid',
                                                 'timecreated',
                                                 'timemodified',
                                                 'grader',
                                                 'grade',
                                                 'attemptnumber'));

        $pluginconfigs = new backup_nested_element('plugin_configs');

        $pluginconfig = new backup_nested_element('plugin_config', array('id'),
                                                   array('plugin',
                                                         'subtype',
                                                         'name',
                                                         'value'));

        $overrides = new backup_nested_element('overrides');
        $override = new backup_nested_element('override', array('id'), array(
            'groupid', 'userid', 'sortorder', 'allowsubmissionsfromdate', 'duedate', 'cutoffdate'));

        // Build the tree.
        $assign->add_child($userflags);
        $userflags->add_child($userflag);
        $assign->add_child($submissions);
        $submissions->add_child($submission);
        $assign->add_child($grades);
        $grades->add_child($grade);
        $assign->add_child($pluginconfigs);
        $pluginconfigs->add_child($pluginconfig);
        $assign->add_child($overrides);
        $overrides->add_child($override);

        // Define sources.
        $assign->set_source_table('assign', array('id' => backup::VAR_ACTIVITYID));
        $pluginconfig->set_source_table('assign_plugin_config',
                                        array('assignment' => backup::VAR_PARENTID));

        // Assign overrides to backup are different depending of user info.
        $overrideparams = array('assignid' => backup::VAR_PARENTID);

        if ($userinfo) {
            $userflag->set_source_table('assign_user_flags',
                                     array('assignment' => backup::VAR_PARENTID));

            $submission->set_source_table('assign_submission',
                                     array('assignment' => backup::VAR_PARENTID));

            $grade->set_source_table('assign_grades',
                                     array('assignment' => backup::VAR_PARENTID));

            // Support 2 types of subplugins.
            $this->add_subplugin_structure('assignsubmission', $submission, true);
            $this->add_subplugin_structure('assignfeedback', $grade, true);
        } else {
            $overrideparams['userid'] = backup_helper::is_sqlparam(null); // Without userinfo, skip user overrides.
        }

        $override->set_source_table('assign_overrides', $overrideparams);

        // Define id annotations.
        $userflag->annotate_ids('user', 'userid');
        $userflag->annotate_ids('user', 'allocatedmarker');
        $submission->annotate_ids('user', 'userid');
        $submission->annotate_ids('group', 'groupid');
        $grade->annotate_ids('user', 'userid');
        $grade->annotate_ids('user', 'grader');
        $assign->annotate_ids('grouping', 'teamsubmissiongroupingid');
        $override->annotate_ids('user', 'userid');
        $override->annotate_ids('group', 'groupid');

        // Define file annotations.
        // These file areas don't have an itemid.
        $assign->annotate_files('mod_assign', 'intro', null);
        $assign->annotate_files('mod_assign', 'introattachment', null);
        $this->annotate_plugin_config_files($assign, 'assignsubmission');
        $this->annotate_plugin_config_files($assign, 'assignfeedback');

        // Return the root element (choice), wrapped into standard activity structure.

        return $this->prepare_activity_structure($assign);
    }
}
