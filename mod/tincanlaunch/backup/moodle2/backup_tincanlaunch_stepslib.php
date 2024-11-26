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
 * Define all the backup steps that will be used by the backup_tincanlaunch_activity_task
 *
 * @package    mod_tincanlaunch
 * @copyright  2016 onward Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Structure step to backup one tincanlaunch activity
 *
 * @package    mod_tincanlaunch
 * @copyright  2016 onward Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_tincanlaunch_activity_structure_step extends backup_activity_structure_step {

    /**
     * Define the structure of the activity to be backed up.
     *
     * @return backup_nested_element $activitystructure the element to wrap
     */
    protected function define_structure() {

        // Define each element separated.
        $tincanlaunch = new backup_nested_element('tincanlaunch', array('id'), array(
            'name', 'intro', 'introformat', 'tincanlaunchurl', 'tincanactivityid',
            'tincanverbid', 'tincanexpiry', 'overridedefaults', 'tincanmultipleregs',
            'tincansimplelaunchnav', 'timecreated', 'timemodified'));

        $tincanlaunchlrs = new backup_nested_element('tincanlaunchlrs', array('id'),
            array( 'lrsendpoint', 'lrsauthentication', 'lrslogin', 'lrspass',
            'customacchp', 'useactoremail', 'lrsduration'));

        $tincanlaunch->add_child($tincanlaunchlrs);

        // Define sources.
        $tincanlaunch->set_source_table('tincanlaunch', array('id' => backup::VAR_ACTIVITYID));
        $tincanlaunchlrs->set_source_table('tincanlaunch_lrs', array('tincanlaunchid' => backup::VAR_PARENTID));

        // Define file annotations.
        $tincanlaunch->annotate_files('mod_tincanlaunch', 'intro', null);
        $tincanlaunch->annotate_files('mod_tincanlaunch', 'package', null);
        $tincanlaunch->annotate_files('mod_tincanlaunch', 'content', null);

        // Return the root element (tincanlaunch), wrapped into standard activity structure.
        return $this->prepare_activity_structure($tincanlaunch);
    }
}
