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

    protected function define_structure() {

        $paths = array();
        $lti = new restore_path_element('lti', '/activity/lti');
        $paths[] = new restore_path_element('ltitype', '/activity/lti/ltitypes/ltitype');
        $paths[] = new restore_path_element('ltitypesconfig', '/activity/lti/ltitypesconfigs/ltitypesconfig');
        $paths[] = $lti;

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
     * @param object $data The data in object form
     * @return void
     */
    protected function process_ltitype($data) {
        global $DB;

        $data = (object)$data;
        $data->createdby = $this->get_mappingid('user', $data->createdby);
        $ltitype = $DB->get_record_sql("SELECT *
            FROM {lti_types}
            WHERE id = ?
            AND baseurl = ?", array($data->id, $data->baseurl));

        // If restore is occurring on the same site, don't add lti_types data if
        // restoring on the SITEID. If restore isn't occurring on the same site,
        // always add lti_type data from backup.
        if ($this->task->is_samesite() && $ltitype->course != SITEID
                && $ltitype->state == LTI_TOOL_STATE_CONFIGURED) {
            // If restoring into the same course, use existing data, else re-create.
            $course = $this->get_courseid();
            if ($ltitype->course != $course) {
                $data->course = $course;
                $ltitype = new stdClass();
                $ltitype->id = $DB->insert_record('lti_types', $data);
            }
        } else if (!$this->task->is_samesite() || !isset($ltitype->id)) {
            // Either we are restoring into a new site, or didn't find a database match.
            $data->course = $this->get_courseid();
            $ltitype = new stdClass();
            $ltitype->id = $DB->insert_record('lti_types', $data);
        }

        // Add the typeid entry back to LTI module.
        $lti = new stdClass();
        $lti->id = $this->get_new_parentid('lti');
        $lti->typeid = $ltitype->id;
        $DB->update_record('lti', $lti);
    }

    /**
     * Process an lti config restore
     * @param object $data The data in object form
     * @return void
     */
    protected function process_ltitypesconfig($data) {
        global $DB;

        $data = (object)$data;

        $parentid = $this->get_new_parentid('lti');
        $lti = $DB->get_record_sql("SELECT typeid
            FROM {lti}
            WHERE id = ?", array($parentid));

        // Only add configuration if typeid doesn't match new LTI tool.
        if ($lti->typeid != $data->typeid) {
            $data->typeid = $lti->typeid;
            if ($data->name == 'servicesalt') {
                $data->value = uniqid('', true);
            }
            $DB->insert_record('lti_types_config', $data);
        }
    }

    protected function after_execute() {
        // Add lti related files, no need to match by itemname (just internally handled context).
        $this->add_related_files('mod_lti', 'intro', null);
    }
}
