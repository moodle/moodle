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
 * Backup steps for mod_h5pactivity are defined here.
 *
 * @package     mod_h5pactivity
 * @category    backup
 * @copyright   2020 Ferran Recio <ferran@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Define the complete structure for backup, with file and id annotations.
 */
class backup_h5pactivity_activity_structure_step extends backup_activity_structure_step {

    /**
     * Defines the structure of the resulting xml file.
     *
     * @return backup_nested_element The structure wrapped by the common 'activity' element.
     */
    protected function define_structure() {
        $userinfo = $this->get_setting_value('userinfo');

        // Replace with the attributes and final elements that the element will handle.
        $attributes = ['id'];
        $finalelements = ['name', 'timecreated', 'timemodified', 'intro',
                'introformat', 'grade', 'displayoptions', 'enabletracking', 'grademethod', 'reviewmode'];
        $root = new backup_nested_element('h5pactivity', $attributes, $finalelements);

        $attempts = new backup_nested_element('attempts');

        $attempt = new backup_nested_element('attempt', ['id'],
            ['h5pactivityid', 'userid', 'timecreated', 'timemodified', 'attempt', 'rawscore', 'maxscore',
            'duration', 'completion', 'success', 'scaled']
        );

        $results = new backup_nested_element('results');

        $result = new backup_nested_element('result', ['id'],
            [
                'attemptid', 'subcontent', 'timecreated', 'interactiontype', 'description',
                'correctpattern', 'response', 'additionals', 'rawscore', 'maxscore',
                'duration', 'completion', 'success'
            ]
        );

        // Build the tree.
        $root->add_child($attempts);
        $attempts->add_child($attempt);
        $attempt->add_child($results);
        $results->add_child($result);

        // Define the source tables for the elements.
        $root->set_source_table('h5pactivity', ['id' => backup::VAR_ACTIVITYID]);

        // All the rest of elements only happen if we are including user info.
        if ($userinfo) {
            $attempt->set_source_table('h5pactivity_attempts', ['h5pactivityid' => backup::VAR_PARENTID], 'id ASC');
            $result->set_source_table('h5pactivity_attempts_results', ['attemptid' => backup::VAR_PARENTID], 'id ASC');
        }

        // Define id annotations.
        $attempt->annotate_ids('user', 'userid');

        // Define file annotations.
        $root->annotate_files('mod_h5pactivity', 'intro', null); // This file area hasn't itemid.
        $root->annotate_files('mod_h5pactivity', 'package', null); // This file area hasn't itemid.

        return $this->prepare_activity_structure($root);
    }
}
