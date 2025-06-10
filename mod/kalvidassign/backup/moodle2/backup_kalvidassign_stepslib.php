<?php
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
 * Kaltura video assignment backup stepslib script.
 *
 * @package    mod_kalvidassign
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

/**
 * Define all the backup steps that will be used by the backup_kalvidassign_activity_task
 */

/**
 * Define the complete kalvidassign structure for backup, with file and id annotations
 */
class backup_kalvidassign_activity_structure_step extends backup_activity_structure_step {

    /**
     * This function defines the structure used to backup the activity.
     * @return backup_nested_element The $activitystructure wrapped by the common 'activity' element.
     */
    protected function define_structure() {

        // To know if we are including userinfo.
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated.
        $columns = array(
                'course',
                'name',
                'intro',
                'introformat',
                'timeavailable',
                'timedue',
                'preventlate',
                'resubmit',
                'emailteachers',
                'grade',
                'timecreated',
                'timemodified'
        );
        $kalvidassign = new backup_nested_element('kalvidassign', array('id'), $columns);

        $issues = new backup_nested_element('submissions');

        $columns = array(
                'userid',
                'entry_id',
                'source',
                'width',
                'height',
                'grade',
                'submissioncomment',
                'format',
                'teacher',
                'mailed',
                'timemarked',
                'timecreated',
                'timemodified'
        );
        $issue = new backup_nested_element('submission', array('id'), $columns);

        // Build the tree.
        $kalvidassign->add_child($issues);
        $issues->add_child($issue);

        // Define sources.
        $kalvidassign->set_source_table('kalvidassign', array('id' => backup::VAR_ACTIVITYID));

        // All the rest of elements only happen if we are including user info.
        if ($userinfo) {
            $issue->set_source_table('kalvidassign_submission', array('vidassignid' => backup::VAR_PARENTID));
        }

        // Annotate the user id's where required.
        $issue->annotate_ids('user', 'userid');

        // Annotate the file areas in use.
        $issue->annotate_files('mod_kalvidassign', 'submission', 'id');

        // Return the root element, wrapped into standard activity structure.
        return $this->prepare_activity_structure($kalvidassign);
    }
}