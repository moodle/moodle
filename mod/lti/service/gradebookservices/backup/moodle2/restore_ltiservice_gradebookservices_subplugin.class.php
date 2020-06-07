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
 * This file contains the class for restore of this gradebookservices plugin
 *
 * @package    ltiservice_gradebookservices
 * @copyright  2017 Cengage Learning http://www.cengage.com
 * @author     Dirk Singels, Diego del Blanco, Claude Vervoort
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/mod/lti/locallib.php');

/**
 * Restore subplugin class.
 *
 * Provides the necessary information
 * needed to restore the lineitems related with the lti activity (coupled),
 * and all the uncoupled ones from the course.
 *
 * @package    ltiservice_gradebookservices
 * @copyright  2017 Cengage Learning http://www.cengage.com
 * @author     Dirk Singels, Diego del Blanco, Claude Vervoort
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_ltiservice_gradebookservices_subplugin extends restore_subplugin {

    /**
     * Returns the subplugin structure to attach to the XML element.
     *
     * @return restore_path_element[] array of elements to be processed on restore.
     */
    protected function define_lti_subplugin_structure() {

        $paths = array();
        $elename = $this->get_namefor('lineitem');
        $elepath = $this->get_pathfor('/lineitems/lineitem');
        $paths[] = new restore_path_element($elename, $elepath);
        return $paths;
    }

    /**
     * Processes one lineitem
     *
     * @param mixed $data
     * @return void
     */
    public function process_ltiservice_gradebookservices_lineitem($data) {
        global $DB;
        $data = (object)$data;
        // The coupled lineitems are restored as any other grade item
        // so we will only create the entry in the ltiservice_gradebookservices table.
        // We will try to find a valid toolproxy in the system.
        // If it has been found before... we use it.
        /* cache parent property to account for missing PHPDoc type specification */
        /** @var backup_activity_task $activitytask */
        $activitytask = $this->task;
        $courseid = $activitytask->get_courseid();
        if ($data->typeid != null) {
            if ($ltitypeid = $this->get_mappingid('ltitype', $data->typeid)) {
                $newtypeid = $ltitypeid;
            } else { // If not, then we will call our own function to find it.
                $newtypeid = $this->find_typeid($data, $courseid);
            }
        } else {
            $newtypeid = null;
        }
        if ($data->toolproxyid != null) {
            $ltitoolproxy = $this->get_mappingid('ltitoolproxy', $data->toolproxyid);
            if ($ltitoolproxy && $ltitoolproxy != 0) {
                $newtoolproxyid = $ltitoolproxy;
            } else { // If not, then we will call our own function to find it.
                $newtoolproxyid = $this->find_proxy_id($data);
            }
        } else {
            $newtoolproxyid = null;
        }
        if ($data->ltilinkid != null) {
            $ltilinkid = $this->get_new_parentid('lti');
        } else {
            $ltilinkid = null;
        }
        $resourceid = null;
        if (property_exists( $data, 'resourceid' )) {
            $resourceid = $data->resourceid;
        }
        // If this has not been restored before.
        if ($this->get_mappingid('gbsgradeitemrestored',  $data->id, 0) == 0) {
            $newgbsid = $DB->insert_record('ltiservice_gradebookservices', (object) array(
                    'gradeitemid' => 0,
                    'courseid' => $courseid,
                    'toolproxyid' => $newtoolproxyid,
                    'ltilinkid' => $ltilinkid,
                    'typeid' => $newtypeid,
                    'baseurl' => $data->baseurl,
                    'resourceid' => $resourceid,
                    'tag' => $data->tag
            ));
            $this->set_mapping('gbsgradeitemoldid', $newgbsid, $data->gradeitemid);
            $this->set_mapping('gbsgradeitemrestored', $data->id, $data->id);
        }
    }

    /**
     * If the toolproxy is not in the mapping (or it is 0)
     * we try to find the toolproxyid.
     * If none is found, then we set it to 0.
     *
     * @param mixed $data
     * @return integer $newtoolproxyid
     */
    private function find_proxy_id($data) {
        global $DB;
        $newtoolproxyid = 0;
        $oldtoolproxyguid = $data->guid;
        $oldtoolproxyvendor = $data->vendorcode;

        $dbtoolproxyjsonparams = array('guid' => $oldtoolproxyguid, 'vendorcode' => $oldtoolproxyvendor);
        $dbtoolproxy = $DB->get_field('lti_tool_proxies', 'id', $dbtoolproxyjsonparams, IGNORE_MISSING);
        if ($dbtoolproxy) {
            $newtoolproxyid = $dbtoolproxy;
        }
        return $newtoolproxyid;
    }

    /**
     * If the typeid is not in the mapping or it is 0, (it should be most of the times)
     * we will try to find the better typeid that matches with the lineitem.
     * If none is found, then we set it to 0.
     *
     * @param stdClass $data
     * @param int $courseid
     * @return int The item type id
     */
    private function find_typeid($data, $courseid) {
        global $DB;
        $newtypeid = 0;
        $oldtypeid = $data->typeid;

        // 1. Find a type with the same id in the same course.
        $dbtypeidparameter = array('id' => $oldtypeid, 'course' => $courseid, 'baseurl' => $data->baseurl);
        $dbtype = $DB->get_field_select('lti_types', 'id', "id=:id
                AND course=:course AND ".$DB->sql_compare_text('baseurl')."=:baseurl",
                $dbtypeidparameter);
        if ($dbtype) {
            $newtypeid = $dbtype;
        } else {
            // 2. Find a site type for all the courses (course == 1), but with the same id.
            $dbtypeidparameter = array('id' => $oldtypeid, 'baseurl' => $data->baseurl);
            $dbtype = $DB->get_field_select('lti_types', 'id', "id=:id
                    AND course=1 AND ".$DB->sql_compare_text('baseurl')."=:baseurl",
                    $dbtypeidparameter);
            if ($dbtype) {
                $newtypeid = $dbtype;
            } else {
                // 3. Find a type with the same baseurl in the actual site.
                $dbtypeidparameter = array('course' => $courseid, 'baseurl' => $data->baseurl);
                $dbtype = $DB->get_field_select('lti_types', 'id', "course=:course
                        AND ".$DB->sql_compare_text('baseurl')."=:baseurl",
                        $dbtypeidparameter);
                if ($dbtype) {
                    $newtypeid = $dbtype;
                } else {
                    // 4. Find a site type for all the courses (course == 1) with the same baseurl.
                    $dbtypeidparameter = array('course' => 1, 'baseurl' => $data->baseurl);
                    $dbtype = $DB->get_field_select('lti_types', 'id', "course=1
                            AND ".$DB->sql_compare_text('baseurl')."=:baseurl",
                            $dbtypeidparameter);
                    if ($dbtype) {
                        $newtypeid = $dbtype;
                    }
                }
            }
        }
        return $newtypeid;
    }

    /**
     * We call the after_restore_lti to update the grade_items id's that we didn't know in the moment of creating
     * the gradebookservices rows.
     */
    protected function after_restore_lti() {
        global $DB;
        $activitytask = $this->task;
        $courseid = $activitytask->get_courseid();
        $gbstoupdate = $DB->get_records('ltiservice_gradebookservices', array('gradeitemid' => 0, 'courseid' => $courseid));
        foreach ($gbstoupdate as $gbs) {
            $oldgradeitemid = $this->get_mappingid('gbsgradeitemoldid', $gbs->id, 0);
            $newgradeitemid = $this->get_mappingid('grade_item', $oldgradeitemid, 0);
            if ($newgradeitemid > 0) {
                $gbs->gradeitemid = $newgradeitemid;
                if (!isset($gbs->resourceid)) {
                    // Before 3.9 resourceid was stored in grade_item->idnumber.
                    $gbs->resourceid = $DB->get_field_select('grade_items', 'idnumber', "id=:id", ['id' => $newgradeitemid]);
                }
                $DB->update_record('ltiservice_gradebookservices', $gbs);
            }
        }
        // Pre 3.9 backups did not include a gradebookservices record. Adding one here if missing for the restored instance.
        $gi = $DB->get_record('grade_items', array('itemtype' => 'mod', 'itemmodule' => 'lti', 'courseid' => $courseid,
            'iteminstance' => $this->task->get_activityid()));
        if ($gi) {
            $gbs = $DB->get_records('ltiservice_gradebookservices', ['gradeitemid' => $gi->id]);
            if (empty($gbs)) {
                // The currently restored LTI link has a grade item but no gbs, so let's create a gbs entry.
                if ($instance = $DB->get_record('lti', array('id' => $gi->iteminstance))) {
                    if ($tool = lti_get_instance_type($instance)) {
                        $DB->insert_record('ltiservice_gradebookservices', (object) array(
                            'gradeitemid' => $gi->id,
                            'courseid' => $courseid,
                            'toolproxyid' => $tool->toolproxyid,
                            'ltilinkid' => $gi->iteminstance,
                            'typeid' => $tool->id,
                            'baseurl' => $tool->baseurl,
                            'resourceid' => $gi->idnumber
                        ));
                    }
                }
            }
        }
    }

}
