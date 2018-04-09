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
//
// This file is part of BasicLTI4Moodle
//
// BasicLTI4Moodle is an IMS BasicLTI (Basic Learning Tools for Interoperability)
// consumer for Moodle 1.9 and Moodle 2.0. BasicLTI is a IMS Standard that allows web
// based learning tools to be easily integrated in LMS as native ones. The IMS BasicLTI
// specification is part of the IMS standard Common Cartridge 1.1 Sakai and other main LMS
// are already supporting or going to support BasicLTI. This project Implements the consumer
// for Moodle. Moodle is a Free Open source Learning Management System by Martin Dougiamas.
// BasicLTI4Moodle is a project iniciated and leaded by Ludo(Marc Alier) and Jordi Piguillem
// at the GESSI research group at UPC.
// SimpleLTI consumer for Moodle is an implementation of the early specification of LTI
// by Charles Severance (Dr Chuck) htp://dr-chuck.com , developed by Jordi Piguillem in a
// Google Summer of Code 2008 project co-mentored by Charles Severance and Marc Alier.
//
// BasicLTI4Moodle is copyright 2009 by Marc Alier Forment, Jordi Piguillem and Nikolas Galanis
// of the Universitat Politecnica de Catalunya http://www.upc.edu
// Contact info: Marc Alier Forment granludo @ gmail.com or marc.alier @ upc.edu.

/**
 * This file contains all the restore steps that will be used
 * by the restore_lti_activity_task
 *
 * @package mod_lti
 * @copyright  2009 Marc Alier, Jordi Piguillem, Nikolas Galanis
 *  marc.alier@upc.edu
 * @copyright  2009 Universitat Politecnica de Catalunya http://www.upc.edu
 * @author     Marc Alier
 * @author     Jordi Piguillem
 * @author     Nikolas Galanis
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Structure step to restore one lti activity
 */
class restore_lti_activity_structure_step extends restore_activity_structure_step {

    /** @var bool */
    protected $newltitype = false;

    protected function define_structure() {

        $paths = array();
        // To know if we are including userinfo.
        $userinfo = $this->get_setting_value('userinfo');

        $lti = new restore_path_element('lti', '/activity/lti');
        $paths[] = $lti;
        $paths[] = new restore_path_element('ltitype', '/activity/lti/ltitype');
        $paths[] = new restore_path_element('ltitypesconfig', '/activity/lti/ltitype/ltitypesconfigs/ltitypesconfig');
        $paths[] = new restore_path_element('ltitypesconfigencrypted',
            '/activity/lti/ltitype/ltitypesconfigs/ltitypesconfigencrypted');
        $paths[] = new restore_path_element('ltitoolproxy', '/activity/lti/ltitype/ltitoolproxy');
        $paths[] = new restore_path_element('ltitoolsetting', '/activity/lti/ltitype/ltitoolproxy/ltitoolsettings/ltitoolsetting');

        if ($userinfo) {
            $submission = new restore_path_element('ltisubmission', '/activity/lti/ltisubmissions/ltisubmission');
            $paths[] = $submission;
        }

        // Add support for subplugin structures.
        $this->add_subplugin_structure('ltisource', $lti);
        $this->add_subplugin_structure('ltiservice', $lti);

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    protected function process_lti($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();
        $data->servicesalt = uniqid('', true);

        // Any changes to the list of dates that needs to be rolled should be same during course restore and course reset.
        // See MDL-9367.

         // Grade used to be a float (whole numbers only), restore as int.
        $data->grade = (int) $data->grade;

        $data->typeid = 0;

        // Try to decrypt resourcekey and password. Null if not possible (DB default).
        // Note these fields were originally encrypted on backup using {link @encrypted_final_element}.
        $data->resourcekey = isset($data->resourcekey) ? $this->decrypt($data->resourcekey) : null;
        $data->password = isset($data->password) ? $this->decrypt($data->password) : null;

        $newitemid = $DB->insert_record('lti', $data);

        // Immediately after inserting "activity" record, call this.
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Process an lti type restore
     * @param mixed $data The data from backup XML file
     * @return void
     */
    protected function process_ltitype($data) {
        global $DB, $USER;

        $data = (object)$data;
        $oldid = $data->id;
        if (!empty($data->createdby)) {
            $data->createdby = $this->get_mappingid('user', $data->createdby) ?: $USER->id;
        }

        $courseid = $this->get_courseid();
        $data->course = ($this->get_mappingid('course', $data->course) == $courseid) ? $courseid : SITEID;

        // Try to find existing lti type with the same properties.
        $ltitypeid = $this->find_existing_lti_type($data);

        $this->newltitype = false;
        if (!$ltitypeid && $data->course == $courseid) {
            unset($data->toolproxyid); // Course tools can not use LTI2.
            $ltitypeid = $DB->insert_record('lti_types', $data);
            $this->newltitype = true;
            $this->set_mapping('ltitype', $oldid, $ltitypeid);
        }

        // Add the typeid entry back to LTI module.
        $DB->update_record('lti', ['id' => $this->get_new_parentid('lti'), 'typeid' => $ltitypeid]);
    }

    /**
     * Attempts to find existing record in lti_type
     * @param stdClass $data
     * @return int|null field lti_types.id or null if tool is not found
     */
    protected function find_existing_lti_type($data) {
        global $DB;
        if ($ltitypeid = $this->get_mappingid('ltitype', $data->id)) {
            return $ltitypeid;
        }

        $ltitype = null;
        $params = (array)$data;
        if ($this->task->is_samesite()) {
            // If we are restoring on the same site try to find lti type with the same id.
            $sql = 'id = :id AND course = :course';
            $sql .= ($data->toolproxyid) ? ' AND toolproxyid = :toolproxyid' : ' AND toolproxyid IS NULL';
            if ($DB->record_exists_select('lti_types', $sql, $params)) {
                $this->set_mapping('ltitype', $data->id, $data->id);
                if ($data->toolproxyid) {
                    $this->set_mapping('ltitoolproxy', $data->toolproxyid, $data->toolproxyid);
                }
                return $data->id;
            }
        }

        if ($data->course != $this->get_courseid()) {
            // Site tools are not backed up and are not restored.
            return null;
        }

        // Now try to find the same type on the current site available in this course.
        // Compare only fields baseurl, course and name, if they are the same we assume it is the same tool.
        // LTI2 is not possible in the course so we add "lt.toolproxyid IS NULL" to the query.
        $sql = 'SELECT id
            FROM {lti_types}
           WHERE ' . $DB->sql_compare_text('baseurl', 255) . ' = ' . $DB->sql_compare_text(':baseurl', 255) . ' AND
                 course = :course AND name = :name AND toolproxyid IS NULL';
        if ($ltitype = $DB->get_record_sql($sql, $params, IGNORE_MULTIPLE)) {
            $this->set_mapping('ltitype', $data->id, $ltitype->id);
            return $ltitype->id;
        }

        return null;
    }

    /**
     * Process an lti config restore
     * @param mixed $data The data from backup XML file
     */
    protected function process_ltitypesconfig($data) {
        global $DB;

        $data = (object)$data;
        $data->typeid = $this->get_new_parentid('ltitype');

        // Only add configuration if the new lti_type was created.
        if ($data->typeid && $this->newltitype) {
            if ($data->name == 'servicesalt') {
                $data->value = uniqid('', true);
            }
            $DB->insert_record('lti_types_config', $data);
        }
    }

    /**
     * Process an lti config restore
     * @param mixed $data The data from backup XML file
     */
    protected function process_ltitypesconfigencrypted($data) {
        global $DB;

        $data = (object)$data;
        $data->typeid = $this->get_new_parentid('ltitype');

        // Only add configuration if the new lti_type was created.
        if ($data->typeid && $this->newltitype) {
            $data->value = $this->decrypt($data->value);
            if (!is_null($data->value)) {
                $DB->insert_record('lti_types_config', $data);
            }
        }
    }

    /**
     * Process a restore of LTI tool registration
     * This method is empty because we actually process registration as part of process_ltitype()
     * @param mixed $data The data from backup XML file
     */
    protected function process_ltitoolproxy($data) {

    }

    /**
     * Process an lti tool registration settings restore (only settings for the current activity)
     * @param mixed $data The data from backup XML file
     */
    protected function process_ltitoolsetting($data) {
        global $DB;

        $data = (object)$data;
        $data->toolproxyid = $this->get_new_parentid('ltitoolproxy');

        if (!$data->toolproxyid) {
            return;
        }

        $data->course = $this->get_courseid();
        $data->coursemoduleid = $this->task->get_moduleid();
        $DB->insert_record('lti_tool_settings', $data);
    }

    /**
     * Process a submission restore
     * @param mixed $data The data from backup XML file
     */
    protected function process_ltisubmission($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->ltiid = $this->get_new_parentid('lti');

        $data->datesubmitted = $this->apply_date_offset($data->datesubmitted);
        $data->dateupdated = $this->apply_date_offset($data->dateupdated);
        if ($data->userid > 0) {
            $data->userid = $this->get_mappingid('user', $data->userid);
        }

        $newitemid = $DB->insert_record('lti_submission', $data);

        $this->set_mapping('ltisubmission', $oldid, $newitemid);
    }

    protected function after_execute() {
        // Add lti related files, no need to match by itemname (just internally handled context).
        $this->add_related_files('mod_lti', 'intro', null);
    }
}
