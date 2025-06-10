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
 * Kaltura video presentation stepslib file.
 *
 * @package    mod_kalvidpres
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

/**
 * Define all the backup steps that will be used by the backup_kalvidpres_activity_task
 */

/**
 * Define the complete kalvidpres structure for backup, with file and id annotations
 */
class backup_kalvidpres_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        // Define each element separated
        $kalvidpres = new backup_nested_element('kalvidpres', array('id'), array(
                'name', 'intro', 'introformat', 'entry_id', 'video_entry_id', 'doc_entry_id',
                'video_title', 'uiconf_id', 'widescreen', 'height', 'width', 'source', 'timemodified',
                'timecreated'));

        // Define sources
        $kalvidpres->set_source_table('kalvidpres', array('id' => backup::VAR_ACTIVITYID));

        // Return the root element, wrapped into standard activity structure
        return $this->prepare_activity_structure($kalvidpres);
    }
}
