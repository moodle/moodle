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
 * @package moodlecore
 * @subpackage backup-moodle2
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the restore steps that will be used by the restore_hotpot_activity_task
 */

/**
 * Structure step to restore one hotpot activity
 */

defined('MOODLE_INTERNAL') || die();

/**
 * restore_hotpot_activity_structure_step
 *
 * @copyright 2010 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class restore_hotpot_activity_structure_step extends restore_activity_structure_step {

    /**
     * define_structure
     *
     * @return xxx
     */
    protected function define_structure()  {

        $paths = array();

        // cache userinfo flag
        $userinfo = $this->get_setting_value('userinfo');

        ////////////////////////////////////////////////////////////////////////
        // XML interesting paths - non-user data
        ////////////////////////////////////////////////////////////////////////

        // root element describing hotpot instance
        $paths[] = new restore_path_element('hotpot', '/activity/hotpot');

        ////////////////////////////////////////////////////////////////////////
        // XML interesting paths - user data
        ////////////////////////////////////////////////////////////////////////

        if ($userinfo) {
            $paths[] = new restore_path_element('hotpot_string',   '/activity/hotpot/strings/string');
            $paths[] = new restore_path_element('hotpot_attempt',  '/activity/hotpot/attempts/attempt');
            $paths[] = new restore_path_element('hotpot_question', '/activity/hotpot/questions/question');
            $paths[] = new restore_path_element('hotpot_response', '/activity/hotpot/questions/question/responses/response');
        }

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Define the contents in the activity that must be
     * processed by the link decoder
     */
    static public function define_decode_contents() {
        return array(
            new restore_decode_content('hotpot', array('entrytext'), 'entrytext'),
            new restore_decode_content('hotpot', array('exittext'),  'exittext')
        );
    }

    /**
     * process_hotpot
     *
     * @param xxx $data
     */
    protected function process_hotpot($data)  {
        global $DB;

        // convert $data to object
        $data = (object)$data;

        // save $oldid
        $oldid = $data->id;

        // fix fields
        if (! $data->course = $this->get_courseid()) {
            return false; // missing courseid - shouldn't happen !!
        }
        $data->timeopen = $this->apply_date_offset($data->timeopen);
        $data->timeclose = $this->apply_date_offset($data->timeclose);
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        // insert the hotpot record
        $newid = $DB->insert_record('hotpot', $data);

        // inmediately after inserting "activity" record, call this
        $this->apply_activity_instance($newid);
    }

    /**
     * process_hotpot_attempt
     *
     * @param xxx $data
     */
    protected function process_hotpot_attempt($data)  {
        global $DB;

        // convert $data to object
        $data = (object)$data;

        // save $oldid
        $oldid = $data->id;

        // fix fields
        if (! $data->userid = $this->get_mappingid('user', $data->userid)) {
            return false; // invalid userid - shouldn't happen !!
        }
        if (! $data->hotpotid = $this->get_new_parentid('hotpot')) {
            return false; // hotpotid not available - shouldn't happen !!
        }

        // get $newid
        if (! $newid = $DB->insert_record('hotpot_attempts', $data)) {
            return false; // could not add new attempt - shouldn't happen !!
        }

        // store mapping from $oldid to $newid
        $this->set_mapping('hotpot_attempt', $oldid, $newid);

        // reset clickreportid to point to parent attempt
        if (empty($data->clickreportid) || $data->clickreportid==$oldid) {
            // clickreporting is not enabled (this is the usual case)
            $clickreportid = $newid;
        } else {
            // clickreporting is enabled, so get main attempt id
            $clickreportid = $this->get_mappingid('hotpot_attempt', $data->clickreportid);
        }
        if (empty($clickreportid)) {
            $clickreportid = $newid; // old attempt id not avialable - shouldn't happen !!
        }
        $DB->set_field('hotpot_attempts', 'clickreportid', $clickreportid, array('id' => $newid));
    }

    /**
     * process_hotpot_question
     *
     * @uses $DB
     * @param xxx $data
     * @return xxx
     * @todo Finish documenting this function
     */
    protected function process_hotpot_question($data)   {
        global $DB;

        // convert $data to object
        $data = (object)$data;

        // save $oldid
        $oldid = $data->id;

        // fix fields
        if (! $data->hotpotid = $this->get_new_parentid('hotpot')) {
            return false; // taskid not available - shouldn't happen !!
        }
        $data->md5key = md5($data->name);
        $this->set_string_ids($data, array('text'), 0);

        // add new record
        if (! $newid = $DB->insert_record('hotpot_questions', $data)) {
            return false; // could not add new record - shouldn't happen !!
        }

        // store mapping from $oldid to $newid
        $this->set_mapping('hotpot_question', $oldid, $newid);
    }

    /**
     * process_hotpot_response
     *
     * @uses $DB
     * @param xxx $data
     * @return xxx
     * @todo Finish documenting this function
     */
    protected function process_hotpot_response($data)   {
        global $DB;

        // convert $data to object
        $data = (object)$data;

        // save $oldid
        $oldid = $data->id;

        // fix fields
        if (! $data->questionid = $this->get_new_parentid('hotpot_question')) {
            return false; // questionid not available - shouldn't happen !!
        }
        if (! isset($data->attemptid)) {
            return false; // attemptid not set - shouldn't happen !!
        }
        if (! $data->attemptid = $this->get_mappingid('hotpot_attempt', $data->attemptid)) {
            return false; // new attemptid not available - shouldn't happen !!
        }
        $this->set_string_ids($data, array('correct', 'wrong', 'ignored'));

        // add new record
        if (! $newid = $DB->insert_record('hotpot_responses', $data)) {
            return false; // could not add new record - shouldn't happen !!
        }
    }

    /**
     * process_hotpot_string
     *
     * @uses $DB
     * @param xxx $data
     * @todo Finish documenting this function
     */
    protected function process_hotpot_string($data)   {
        global $DB;

        // convert $data to object
        $data = (object)$data;

        // save $oldid
        $oldid = $data->id;

        // fix fields
        $data->md5key = md5($data->string);

        // add new record, if necessary
        $params = array('md5key' => $data->md5key);
        if (! $newid = $DB->get_field('hotpot_strings', 'id', $params)) {
            if (! $newid = $DB->insert_record('hotpot_strings', $data)) {
                return false; // could not add new record - shouldn't happen !!
            }
        }

        // store mapping from $oldid to $newid
        $this->set_mapping('hotpot_string', $oldid, $newid);
    }

    /**
     * set_string_ids
     *
     * @param xxx $data (passed by reference)
     * @param xxx $fieldnames
     * @param xxx $default (optional, default='')
     * @todo Finish documenting this function
     */
    protected function set_string_ids(&$data, $fieldnames, $default='')  {

        foreach ($fieldnames as $fieldname) {

            $newids = array();
            if (isset($data->$fieldname)) {
                $oldids = explode(',', $data->$fieldname);
                $oldids = array_filter($oldids); // remove blanks
                foreach ($oldids as $oldid) {
                    if ($newid = $this->get_mappingid('hotpot_string', $oldid)) {
                        $newids[] = $newid;
                    } else {
                        // new string id not available - should we report it ?
                    }
                }
            }

            if (count($newids)) {
                $data->$fieldname = implode(',', $newids);
            } else {
                $data->$fieldname = $default;
            }
        }
    }

    /**
     * after_execute
     *
     * @uses $DB
     */
    protected function after_execute()  {
        global $DB;

        // restore files
        $this->add_related_files('mod_hotpot', 'sourcefile', null);
        $this->add_related_files('mod_hotpot', 'entrytext',  null);
        $this->add_related_files('mod_hotpot', 'exittext',   null);

        // get most recently restored hotpot record
        $params = array('id' => $this->task->get_activityid());
        if (! $hotpot = $DB->get_record('hotpot', $params)) {
            return false; // shouldn;t happen !!
        }

        // remap $hotpot->entrycm and $hotpot->exitcm
        $keys = array('entrycm' => 'course_module', 'exitcm' => 'course_module');
        $this->after_execute_foreignkeys($hotpot, 'hotpot', $keys);
    }

    /**
     * after_execute_foreignkeys
     *
     * @uses $DB
     * @param object $record (passed by reference)
     * @param string $tablename table from which $record was extracted
     * @param array $keys map record $field => $itemname
     * @return void, but may update $record and DB tables
     * @todo Finish documenting this function
     */
    protected function after_execute_foreignkeys(&$record, $table, $keys, $default=0)  {
        global $DB;

        $update = false;
        foreach ($keys as $field => $itemname) {
            if ($record->$field > 0) {
                $record->$field = $this->get_mappingid($itemname, $record->$field);
                if ($record->$field===false || $record->$field===null) {
                    $record->$field = $default; // shouldn't happen !!
                }
                $update = true;
            }
        }
        if ($update) {
            $DB->update_record($table, $record);
        }
    }
}
