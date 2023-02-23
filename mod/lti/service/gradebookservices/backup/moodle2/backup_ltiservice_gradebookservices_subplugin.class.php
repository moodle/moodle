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
 * @author     Dirk Singels, Diego del Blanco
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot.'/mod/lti/locallib.php');

/**
 * Provides the information to backup gradebookservices lineitems
 *
 * @package    ltiservice_gradebookservices
 * @copyright  2017 Cengage Learning http://www.cengage.com
 * @author     Dirk Singels, Diego del Blanco, Claude Vervoort
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_ltiservice_gradebookservices_subplugin extends backup_subplugin {

    /** TypeId contained in DB but is invalid */
    const NONVALIDTYPEID = 0;

    /**
     * Returns the subplugin information to attach to submission element
     * @return backup_subplugin_element
     */
    protected function define_lti_subplugin_structure() {
        global $DB;

        // Create XML elements.
        $subplugin = $this->get_subplugin_element();
        $subpluginwrapper = new backup_nested_element($this->get_recommended_name());
        // The gbs entries related with this element.
        $lineitems = new backup_nested_element('lineitems');
        $lineitem = new backup_nested_element('lineitem', array('id'), array(
                'gradeitemid',
                'courseid',
                'toolproxyid',
                'typeid',
                'baseurl',
                'ltilinkid',
                'resourceid',
                'tag',
                'vendorcode',
                'guid',
                'subreviewurl',
                'subreviewparams'
                )
        );

        // Build the tree.
        $subplugin->add_child($subpluginwrapper);
        $subpluginwrapper->add_child($lineitems);
        $lineitems->add_child($lineitem);

        // We need to know the actual activity tool or toolproxy.
        // If and activity is assigned to a type that doesn't exists we don't want to backup any related lineitems.``
        // Default to invalid condition.
        $typeid = 0;
        $toolproxyid = '0';

        /* cache parent property to account for missing PHPDoc type specification */
        /** @var backup_activity_task $activitytask */
        $activitytask = $this->task;
        $activityid = $activitytask->get_activityid();
        $activitycourseid = $activitytask->get_courseid();
        $lti = $DB->get_record('lti', ['id' => $activityid], 'typeid, toolurl, securetoolurl');
        $ltitype = $DB->get_record('lti_types', ['id' => $lti->typeid], 'toolproxyid, baseurl');
        if ($ltitype) {
            $typeid = $lti->typeid;
            $toolproxyid = $ltitype->toolproxyid;
        } else if ($lti->typeid == self::NONVALIDTYPEID) { // This activity comes from an old backup.
            // 1. Let's check if the activity is coupled. If so, find the values in the GBS element.
            $gbsrecord = $DB->get_record('ltiservice_gradebookservices',
                    ['ltilinkid' => $activityid], 'typeid,toolproxyid,baseurl');
            if ($gbsrecord) {
                $typeid = $gbsrecord->typeid;
                $toolproxyid = $gbsrecord->toolproxyid;
            } else { // 2. If it is uncoupled... we will need to guess the right activity typeid
                // Guess the typeid for the activity.
                $tool = lti_get_tool_by_url_match($lti->toolurl, $activitycourseid);
                if (!$tool) {
                    $tool = lti_get_tool_by_url_match($lti->securetoolurl, $activitycourseid);
                }
                if ($tool) {
                    $alttypeid = $tool->id;
                    // If we have a valid typeid then get types again.
                    if ($alttypeid != self::NONVALIDTYPEID) {
                        $ltitype = $DB->get_record('lti_types', ['id' => $alttypeid], 'toolproxyid, baseurl');
                        $toolproxyid = $ltitype->toolproxyid;
                    }
                }
            }
        }

        // Define sources.
        if ($toolproxyid != null) {
            $lineitemssql = "SELECT l.*, t.vendorcode as vendorcode, t.guid as guid
                               FROM {ltiservice_gradebookservices} l
                         INNER JOIN {lti_tool_proxies} t ON (t.id = l.toolproxyid)
                              WHERE l.courseid = ?
                                AND l.toolproxyid = ?
                                AND l.typeid is null";
            $lineitemsparams = ['courseid' => backup::VAR_COURSEID, backup_helper::is_sqlparam($toolproxyid)];
        } else {
            $lineitemssql = "SELECT l.*, null as vendorcode, null as guid
                               FROM {ltiservice_gradebookservices} l
                              WHERE l.courseid = ?
                                AND l.typeid = ?
                                AND l.toolproxyid is null";
            $lineitemsparams = ['courseid' => backup::VAR_COURSEID, backup_helper::is_sqlparam($typeid)];
        }

        $lineitem->set_source_sql($lineitemssql, $lineitemsparams);

        return $subplugin;
    }
}
