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
 * Moodle restores data from course backups by executing so called restore plan.
 * The restore plan consists of a set of restore tasks and finally each restore task consists of one or more restore steps.
 * You as the developer of a plugin will have to implement one restore task that deals with your plugin data.
 * Most plugins have their restore tasks consisting of a single restore step
 * - the one that parses the plugin XML file and puts the data into its tables.
 *
 * @package   mod_pdfannotator
 * @category  backup
 * @copyright 2018 RWTH Aachen (see README.md)
 * @author    Anna Heynkes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Define all the restore steps that will be used by the restore_pdfannotator_activity_task
 */

/**
 * Structure step to restore one pdfannotator activity
 */
class restore_pdfannotator_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();

        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('pdfannotator',
            '/activity/pdfannotator');

        if($userinfo) {
	        $paths[] = new restore_path_element('pdfannotator_annotation',
	            '/activity/pdfannotator/annotations/annotation');

	        $paths[] = new restore_path_element('pdfannotator_subscription',
	            '/activity/pdfannotator/annotations/annotation/subscriptions/subscription');
	        $paths[] = new restore_path_element('pdfannotator_comment',
	            '/activity/pdfannotator/annotations/annotation/comments/comment');

	        $paths[] = new restore_path_element('pdfannotator_vote',
	            '/activity/pdfannotator/annotations/annotation/comments/comment/votes/vote');
	        $paths[] = new restore_path_element('pdfannotator_report',
	            '/activity/pdfannotator/annotations/annotation/comments/comment/reports/report');
        }
        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    protected function process_pdfannotator($data) {

        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();
        $data->timecreated = time();
        $data->timemodified = time();

        $newitemid = $DB->insert_record('pdfannotator', $data); // Insert the pdfannotator record.

        $this->apply_activity_instance($newitemid); // Immediately after inserting "activity" record, call this.
    }

    protected function process_pdfannotator_annotation($data) {

        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->pdfannotatorid = $this->get_new_parentid('pdfannotator');
        $data->userid = $this->get_mappingid('user', $data->userid);

        $newitemid = $DB->insert_record('pdfannotator_annotations', $data);
        $this->set_mapping('pdfannotator_annotation', $oldid, $newitemid);

    }

    protected function process_pdfannotator_subscription($data) {

        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->annotationid = $this->get_new_parentid('pdfannotator_annotation');
        $data->userid = $this->get_mappingid('user', $data->userid);

        $newitemid = $DB->insert_record('pdfannotator_subscriptions', $data);
        $this->set_mapping('pdfannotator_subscription', $oldid, $newitemid);

    }

    protected function process_pdfannotator_comment($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->annotationid = $this->get_new_parentid('pdfannotator_annotation');
        $data->userid = $this->get_mappingid('user', $data->userid);

        $data->pdfannotatorid = $this->get_mappingid('pdfannotator', $data->pdfannotatorid);

        $newitemid = $DB->insert_record('pdfannotator_comments', $data);
        $this->set_mapping('pdfannotator_comment', $oldid, $newitemid, true);
    }

    protected function process_pdfannotator_vote($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->commentid = $this->get_new_parentid('pdfannotator_comment');
        $data->userid = $this->get_mappingid('user', $data->userid);

        $newitemid = $DB->insert_record('pdfannotator_votes', $data);
        $this->set_mapping('pdfannotator_vote', $oldid, $newitemid);
    }

    protected function process_pdfannotator_report($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->courseid = $this->get_courseid();

        $data->commentid = $this->get_new_parentid('pdfannotator_comment');
        $data->userid = $this->get_mappingid('user', $data->userid);

        // $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->pdfannotatorid = $this->get_mappingid('pdfannotator', $data->pdfannotatorid);
        // Params: 1. Object class as defined in structure, 2. attribute&/column name.

        $newitemid = $DB->insert_record('pdfannotator_reports', $data);
        $this->set_mapping('pdfannotator_report', $oldid, $newitemid);
    }

    protected function after_execute() {
        // Add pdfannotator related files, no need to match by itemname (just internally handled context).
        $this->add_related_files('mod_pdfannotator', 'intro', null);
        $this->add_related_files('mod_pdfannotator', 'content', null);
        $this->add_related_files('mod_pdfannotator', 'post', 'pdfannotator_comment');
    }
}
