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
 * @package    mod_assignment
 * @subpackage backup-moodle2
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the backup steps that will be used by the backup_assignment_activity_task
 */

/**
 * Define the complete assignment structure for backup, with file and id annotations
 */
class backup_assignment_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated
        $assignment = new backup_nested_element('assignment', array('id'), array(
            'name', 'intro', 'introformat', 'assignmenttype',
            'resubmit', 'preventlate', 'emailteachers', 'var1',
            'var2', 'var3', 'var4', 'var5',
            'maxbytes', 'timedue', 'timeavailable', 'grade',
            'timemodified'));

        $submissions = new backup_nested_element('submissions');

        $submission = new backup_nested_element('submission', array('id'), array(
            'userid', 'timecreated', 'timemodified', 'numfiles',
            'data1', 'data2', 'grade', 'submissioncomment',
            'format', 'teacher', 'timemarked', 'mailed'));

        // Build the tree

        // Apply for 'assignment' subplugins optional stuff at assignment level (not multiple)
        // Remember that order is important, try moving this line to the end and compare XML
        $this->add_subplugin_structure('assignment', $assignment, false);

        $assignment->add_child($submissions);
        $submissions->add_child($submission);

        // Apply for 'assignment' subplugins optional stuff at submission level (not multiple)
        $this->add_subplugin_structure('assignment', $submission, false);

        // Define sources
        $assignment->set_source_table('assignment', array('id' => backup::VAR_ACTIVITYID));

        // All the rest of elements only happen if we are including user info
        if ($userinfo) {
            $submission->set_source_table('assignment_submissions', array('assignment' => backup::VAR_PARENTID));
        }

        // Define id annotations
        $assignment->annotate_ids('scale', 'grade');
        $submission->annotate_ids('user', 'userid');
        $submission->annotate_ids('user', 'teacher');

        // Define file annotations
        $assignment->annotate_files('mod_assignment', 'intro', null); // This file area hasn't itemid
        $submission->annotate_files('mod_assignment', 'submission', 'id');
        $submission->annotate_files('mod_assignment', 'response', 'id');

        // Return the root element (assignment), wrapped into standard activity structure
        return $this->prepare_activity_structure($assignment);
    }
}
