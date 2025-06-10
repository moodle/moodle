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
 * Cross Enrollment Tool
 *
 * @package   block_lsuxe
 * @copyright 2008 onwards Louisiana State University
 * @copyright 2008 onwards David Lowe, Robert Russo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_lsuxe\persistents;
use block_lsuxe\models;

class mappings extends \block_lsuxe\persistents\persistent {

    /** Table name for the persistent. */
    const TABLE = 'block_lsuxe_mappings';
    const PNAME = 'mappings';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'courseid' => [
                'type' => PARAM_INT
            ],
            'shortname' => [
                'type' => PARAM_TEXT
            ],
            'authmethod' => [
                'type' => PARAM_TEXT
            ],
            'groupid' => [
                'type' => PARAM_INT
            ],
            'groupname' => [
                'type' => PARAM_TEXT
            ],
            'destmoodleid' => [
                'type' => PARAM_INT
            ],
            'destcourseid' => [
                'type' => PARAM_INT,
                'default' => null,
                'null' => NULL_ALLOWED
            ],
            'destcourseshortname' => [
                'type' => PARAM_TEXT
            ],
            'destgroupprefix' => [
                'type' => PARAM_TEXT
            ],
            'destgroupid' => [
                'type' => PARAM_INT,
                'default' => null,
                'null' => NULL_ALLOWED
            ],
            'updateinterval' => [
                'type' => PARAM_INT,
                'default' => null,
                'null' => NULL_ALLOWED
            ],
            'starttime' => [
                'type' => PARAM_INT,
                'default' => null,
                'null' => NULL_ALLOWED
            ],
            'endtime' => [
                'type' => PARAM_INT,
                'default' => null,
                'null' => NULL_ALLOWED
            ],
            'usercreated' => [
                'type' => PARAM_INT
            ],
            'timecreated' => [
                'type' => PARAM_INT
            ],
            'usermodified' => [
                'type' => PARAM_INT,
                'default' => null,
                'null' => NULL_ALLOWED
            ],
            'timemodified' => [
                'type' => PARAM_INT,
                'default' => null,
                'null' => NULL_ALLOWED
            ],
            'userdeleted' => [
                'type' => PARAM_INT,
                'default' => null,
                'null' => NULL_ALLOWED
            ],
            'timedeleted' => [
                'type' => PARAM_INT,
                'default' => null,
                'null' => NULL_ALLOWED
            ],
            'timeprocessed' => [
                'type' => PARAM_INT,
                'default' => null,
                'null' => NULL_ALLOWED
            ]
        ];
    }

    /**
     * Define the columns that need to be checked for duplicate records.
     *
     * @return array
     */
    public function column_record_check() {
        return array(
            // DB Column Name => Form Name.
            'shortname' => 'srccourseshortname',
            'groupname' => 'srccoursegroupname',
            'destcourseshortname' => 'destcourseshortname'
        );
    }

    /**
     * When saving a new record this matches the form fields to the db columns.
     *
     * @return array
     */
    public function column_form_symetric() {
        return array(
            // DB Column Name => Form Name.
            'shortname' => 'srccourseshortname',
            'groupname' => 'srccoursegroupname',
            'destcourseshortname' => 'destcourseshortname',
            'destgroupprefix' => 'destcoursegroupname',
            'destmoodleid' => 'available_moodle_instances',
            'updateinterval' => 'defaultupdateinterval',
            'authmethod' => 'authmethod'
        );
    }

    /**
     * The form has limited data and the rest will have to be extracted and/or
     * interpolated. This function is where we do that.
     * @param object This is the current info ready to be saved
     * @param object All form data and tidbits to be extracted and/or interpolated.
     * @return void The object is referenced.
     */
    public function column_form_custom(&$tosave, $data, $update = false) {
        global $DB, $USER;
        // If enabled, let's use Moodle's autocomplete feature.
        $enableautocomplete = get_config('moodle', "block_lsuxe_enable_form_auto");
        if ($enableautocomplete) {
            // The course shortname field is an autocomplete that returns the course id.
            $courseid = $tosave->shortname;

            $coursedata = $DB->get_record_sql(
                'SELECT g.id as groupid, c.id as courseid, c.idnumber, c.shortname, g.name as groupname
                FROM mdl_course c, mdl_groups g
                WHERE c.id = g.courseid AND c.id = ?',
                array($courseid)
            );
            // We'll have the course id.
            $tosave->courseid = $coursedata->courseid;
            $tosave->shortname = $coursedata->shortname;

            // The source groupname varies and have to check if the user used a
            // select form or RAW Text.

            if (property_exists($data, "selectgroupentry") && $data->selectgroupentry == "1") {
                // The user used RAW Text to enter the group name.
                $tosave->groupname = $data->srccoursegroupnametext;
            } else {
                // The user used the select which means we have groupid and name.
                $tosave->groupname = $data->srccoursegroupname;
                $tosave->groupid = $data->srccoursegroupid;
            }

            // Destination Course.
            if (strpos($data->destcourseshortname, '__') !== false) {
                $splitdestinfo = explode("__", $data->destcourseshortname);
                $tosave->destcourseid = $splitdestinfo[0];
                $tosave->destcourseshortname = $splitdestinfo[1];
            }
        } else {
            // Manual Form Entry.
            // Save course id and group id, will be present if user verified.
            // But if the user didn't verify, then fetch the data.
            if (isset($data->srccourseid) && $data->srccourseid != 0) {
                $tosave->courseid = $data->srccourseid;
                $tosave->groupid = $data->srccoursegroupid;
            } else {
                // User didn't verify, then fetch the data.
                $fuzzy = new \block_lsuxe\models\xemixed();
                $dbresult = $fuzzy->get_course_group_info($tosave->shortname, $tosave->groupname);
                $tosave->courseid = $dbresult->id;
                $tosave->groupid = $dbresult->groupid;
            }
            // Save the groupname.
            $tosave->groupname = $data->srccoursegroupname;

            // Save course id if user verified the destination course.
            if (isset($data->destcourseid) && $data->destcourseid != 0) {
                $tosave->destcourseid = $data->destcourseid;
            }
        }

        // If it's new then update first time fields.
        if ($update == false) {
            $tosave->timecreated = time();
            $tosave->usercreated = $USER->id;
        } else {
            // It's an update, so change the modified fields.
            $tosave->usermodified = $USER->id;
            $tosave->timemodified = time();
        }

        // The interval is a select and will be a string, need to typecast it.
        $tosave->updateinterval = (int) $data->defaultupdateinterval;

        // Update the start and end times (if any).
        $tosave->starttime = (int) $data->starttime;
        $tosave->endtime = (int) $data->endtime;
    }

    /**
     * Transform any custom data from the DB to be used in the form.
     * @param object the data object
     * @param object Helper injection
     * @return void The object is referenced.
     */
    public function transform_for_view($data, $helpers) {
        global $DB;
        $intervals = $helpers->config_to_array('block_lsuxe_interval_list');

        // We need to show the correct interval and not the number.
        foreach ($data[self::PNAME] as &$thisrecord) {
            // Handle intervals.
            if (isset($intervals[$thisrecord['updateinterval']]) && $thisrecord['updateinterval'] != 0) {
                $thisrecord['updateinterval'] = $intervals[$thisrecord['updateinterval']];
            } else {
                $thisrecord['updateinterval'] = "<i class='fa fa-ban'></i>";
            }
            // Handle URL as we are storing the id.
            $destmoodle = $DB->get_record(
                'block_lsuxe_moodles',
                array('id' => $thisrecord['destmoodleid']),
                $fields = '*'
            );
            if ($destmoodle) {
                $thisrecord['moodleurl'] = "https://".$destmoodle->url;
            } else {
                // The moodle instance may have been deleted.
                $thisrecord['moodleurl'] = "The URL has been deleted.";
            }

            // Convert timestamp to readable date for enrollment endtime.
            $thisrecord['endtime'] = userdate($thisrecord['endtime']);
        }
        return $data;
    }

    /**
     * Persistent hook to redirect user back to the view after the object is saved.
     *
     * @return void
     */
    protected function after_create() {
        global $CFG;
        redirect($CFG->wwwroot . '/blocks/lsuxe/mappings.php',
            get_string('creatednewmapping', 'block_lsuxe'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    }

    /**
     * Persistent hook to redirect user back to the view after the object is updated.
     *
     * @return void
     */
    protected function after_update($result) {
        global $CFG;
        // The action should still be stored so let's use that to redirect accordingly.
        $action = optional_param('sentaction', "", PARAM_TEXT);
        if ($action === "recovered") {
            redirect($CFG->wwwroot . '/blocks/lsuxe/archives.php',
                get_string('recoverarchive', 'block_lsuxe'),
                null,
                \core\output\notification::NOTIFY_SUCCESS
            );
        } else if ($action === "delete") {
            redirect($CFG->wwwroot . '/blocks/lsuxe/mappings.php',
                get_string('deletemapping', 'block_lsuxe'),
                null,
                \core\output\notification::NOTIFY_SUCCESS
            );
        } else {
            redirect($CFG->wwwroot . '/blocks/lsuxe/mappings.php',
                get_string('updatedmapping', 'block_lsuxe'),
                null,
                \core\output\notification::NOTIFY_SUCCESS
            );
        }
    }
}
